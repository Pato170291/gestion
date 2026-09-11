<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Caja;
use App\Models\MovimientoCaja;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\VentaPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    public function index(Request $request)
    {
        $clientes = Cliente::orderBy('nombre')->orderBy('apellido')->get();

        $buscar = trim($request->input('buscar', ''));

        $consulta = Venta::with(['cliente', 'detalles', 'pagos'])
            ->orderByDesc('updated_at')
            ->orderByDesc('id');

        if ($buscar !== '') {
            $consulta->where(function ($query) use ($buscar) {
                $query->where('fecha', 'like', "%{$buscar}%")
                    ->orWhere('estado', 'like', "%{$buscar}%")
                    ->orWhere('total', 'like', "%{$buscar}%")
                    ->orWhereHas('cliente', function ($clienteQuery) use ($buscar) {
                        $clienteQuery->where('nombre', 'like', "%{$buscar}%")
                            ->orWhere('apellido', 'like', "%{$buscar}%");
                    });
            });
        }

        $ventas = $consulta->paginate(6)->appends([
            'buscar' => $buscar
        ]);

        if ($request->ajax()) {
            return view('partials.ventas-ajax', compact('ventas'));
        }

        return view('ventas', compact('clientes', 'ventas'));
    }

    public function crear()
    {
        $clientes = Cliente::orderBy('nombre')->orderBy('apellido')->get();
        $productos = Producto::orderBy('nombre')->get();

        return view('crear-venta', compact('clientes', 'productos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fecha' => 'required|date',
            'cliente_id' => 'nullable|exists:clientes,id',
            'producto_id' => 'required',
            'cantidad' => 'required|integer|min:1',
            'precio' => 'required|numeric|min:0',
            'formas_pago' => 'required|array|min:1',
            'formas_pago.*' => 'required|in:efectivo,tarjeta,transferencia,cuenta_corriente',
            'montos_pago' => 'nullable|array',
            'montos_pago.*' => 'nullable|numeric|min:0',
        ], [
            'formas_pago.required' => 'Seleccione al menos una forma de pago.',
            'formas_pago.min' => 'Seleccione al menos una forma de pago.',
        ]);

        $cantidad = (int) $validated['cantidad'];
        $precio = round((float) $validated['precio'], 2);
        $total = round($cantidad * $precio, 2);

        $formasPago = $validated['formas_pago'];
        $montos = [];

        if (count($formasPago) === 1) {
            $montos[$formasPago[0]] = $total;
        } else {
            foreach ($formasPago as $formaPago) {
                if (!array_key_exists($formaPago, $validated['montos_pago'] ?? [])) {
                    return back()
                        ->withErrors([
                            'montos_pago' => 'Debe indicar el monto de cada forma de pago.'
                        ])
                        ->withInput();
                }

                $montos[$formaPago] = round(
                    (float) $validated['montos_pago'][$formaPago],
                    2
                );
            }
        }

        $totalPagado = round(
            array_sum(
                array_filter(
                    $montos,
                    fn ($formaPago) => $formaPago !== 'cuenta_corriente',
                    ARRAY_FILTER_USE_KEY
                )
            ),
            2
        );

        if ($totalPagado > $total) {
            return back()
                ->withErrors([
                    'montos_pago' => 'La suma de los pagos no puede superar el total de la venta.'
                ])
                ->withInput();
        }

        $montosCaja = $this->montosCaja($montos);
        $caja = $this->cajaAbiertaParaPagos($montosCaja);

        if ($montosCaja && !$caja) {
            return back()
                ->withErrors(['formas_pago' => 'Debe abrir la caja antes de registrar un pago.'])
                ->withInput();
        }

        $estado = $totalPagado >= $total
            ? 'Pagado'
            : ($totalPagado > 0 ? 'Parcial' : 'Pendiente');

        $productoId = $validated['producto_id'] === 'otro'
            ? null
            : $validated['producto_id'];

        $productoNombre = 'Otro';

        if ($productoId !== null) {
            $producto = Producto::findOrFail($productoId);
            $productoNombre = $producto->nombre;
        }

        DB::transaction(function () use (
            $validated,
            $total,
            $totalPagado,
            $estado,
            $productoId,
            $productoNombre,
            $cantidad,
            $precio,
            $montos,
            $montosCaja,
            $caja
        ) {
            $venta = Venta::create([
                'fecha' => $validated['fecha'],
                'cliente_id' => $validated['cliente_id'] ?? null,
                'total' => $total,
                'total_pagado' => $totalPagado,
                'estado' => $estado,
            ]);

            VentaDetalle::create([
                'venta_id' => $venta->id,
                'producto_id' => $productoId,
                'producto_nombre' => $productoNombre,
                'cantidad' => $cantidad,
                'precio' => $precio,
                'subtotal' => $total,
            ]);

            foreach ($montos as $formaPago => $monto) {
                VentaPago::create([
                    'venta_id' => $venta->id,
                    'forma_pago' => $formaPago,
                    'monto' => $monto,
                ]);
            }

            $this->crearMovimientosIngreso($venta, $caja, $montosCaja);
        });

        return redirect('/ventas')
            ->with('success', 'Venta guardada correctamente.');
    }

    public function show($id)
    {
        return view('detalle-venta', compact('id'));
    }

    public function edit($id)
    {
        $venta = Venta::with(['detalles', 'pagos'])->findOrFail($id);

        if ($venta->estado === 'Anulada') {
            return redirect('/ventas')
                ->with('error', 'Una venta anulada no se puede editar.');
        }

        $clientes = Cliente::orderBy('nombre')
            ->orderBy('apellido')
            ->get();

        $productos = Producto::orderBy('nombre')->get();

        return view('editar-venta', compact(
            'venta',
            'clientes',
            'productos'
        ));
    }

    public function update(Request $request, $id)
    {
        $venta = Venta::with(['detalles', 'pagos'])->findOrFail($id);

        if ($venta->estado === 'Anulada') {
            return redirect('/ventas')
                ->with('error', 'Una venta anulada no se puede editar.');
        }

        $validated = $request->validate([
            'fecha' => 'required|date',
            'cliente_id' => 'nullable|exists:clientes,id',
            'producto_id' => 'required',
            'cantidad' => 'required|integer|min:1',
            'precio' => 'required|numeric|min:0',
            'formas_pago' => 'required|array|min:1',
            'formas_pago.*' => 'required|in:efectivo,tarjeta,transferencia,cuenta_corriente',
            'montos_pago' => 'nullable|array',
            'montos_pago.*' => 'nullable|numeric|min:0',
        ], [
            'formas_pago.required' => 'Seleccione al menos una forma de pago.',
            'formas_pago.min' => 'Seleccione al menos una forma de pago.',
        ]);

        $cantidad = (int) $validated['cantidad'];
        $precio = round((float) $validated['precio'], 2);
        $total = round($cantidad * $precio, 2);

        $formasPago = $validated['formas_pago'];
        $montos = [];

        if (count($formasPago) === 1) {
            $montos[$formasPago[0]] = $total;
        } else {
            foreach ($formasPago as $formaPago) {
                if (!array_key_exists($formaPago, $validated['montos_pago'] ?? [])) {
                    return back()
                        ->withErrors([
                            'montos_pago' => 'Debe indicar el monto de cada forma de pago.'
                        ])
                        ->withInput();
                }

                $montos[$formaPago] = round(
                    (float) $validated['montos_pago'][$formaPago],
                    2
                );
            }
        }

        $totalPagado = round(
            array_sum(
                array_filter(
                    $montos,
                    fn ($formaPago) => $formaPago !== 'cuenta_corriente',
                    ARRAY_FILTER_USE_KEY
                )
            ),
            2
        );

        if ($totalPagado > $total) {
            return back()
                ->withErrors([
                    'montos_pago' => 'La suma de los pagos no puede superar el total de la venta.'
                ])
                ->withInput();
        }

        $montosCaja = $this->montosCaja($montos);
        $caja = $this->cajaAbiertaParaPagos($montosCaja);

        if ($montosCaja && !$caja) {
            return back()
                ->withErrors(['formas_pago' => 'Debe abrir la caja antes de registrar un pago.'])
                ->withInput();
        }

        $estado = $totalPagado >= $total
            ? 'Pagado'
            : ($totalPagado > 0 ? 'Parcial' : 'Pendiente');

        $productoId = $validated['producto_id'] === 'otro'
            ? null
            : $validated['producto_id'];

        $productoNombre = 'Otro';

        if ($productoId !== null) {
            $producto = Producto::findOrFail($productoId);
            $productoNombre = $producto->nombre;
        }

        $movimientosExistentes = MovimientoCaja::with('caja')
            ->where('venta_id', $venta->id)
            ->get();

        if ($movimientosExistentes->contains(function ($movimiento) {
            return $movimiento->caja && $movimiento->caja->estado !== 'abierta';
        })) {
            return redirect('/ventas')
                ->with('error', 'No se puede editar una venta cuyos movimientos pertenecen a una caja cerrada.');
        }

        DB::transaction(function () use (
            $venta,
            $validated,
            $total,
            $totalPagado,
            $estado,
            $productoId,
            $productoNombre,
            $cantidad,
            $precio,
            $montos,
            $montosCaja,
            $caja
        ) {
            $venta->update([
                'fecha' => $validated['fecha'],
                'cliente_id' => $validated['cliente_id'] ?? null,
                'total' => $total,
                'total_pagado' => $totalPagado,
                'estado' => $estado,
            ]);

            $venta->detalles()->delete();

            $venta->detalles()->create([
                'producto_id' => $productoId,
                'producto_nombre' => $productoNombre,
                'cantidad' => $cantidad,
                'precio' => $precio,
                'subtotal' => $total,
            ]);

            $venta->pagos()->delete();
            MovimientoCaja::where('venta_id', $venta->id)->delete();

            foreach ($montos as $formaPago => $monto) {
                $venta->pagos()->create([
                    'forma_pago' => $formaPago,
                    'monto' => $monto,
                ]);
            }

            $this->crearMovimientosIngreso($venta, $caja, $montosCaja);
        });

        return redirect('/ventas')
            ->with('success', 'Venta actualizada correctamente.');
    }

    public function anular(Request $request, $id)
    {
        $venta = Venta::findOrFail($id);

        if ($venta->estado === 'Anulada') {
            return redirect('/ventas')
                ->with('error', 'La venta ya está anulada.');
        }

        $validated = $request->validate([
            'motivo_anulacion' => 'nullable|string',
        ]);

        $montosPagados = $venta->pagos()->pluck('monto', 'forma_pago')->all();
        $montosCaja = $this->montosCaja($montosPagados);
        $caja = $this->cajaAbiertaParaPagos($montosCaja);

        if ($montosCaja && !$caja) {
            return redirect('/ventas')
                ->with('error', 'Debe abrir la caja antes de anular una venta con pagos registrados.');
        }

        try {
            DB::transaction(function () use ($id, $validated, $montosCaja, $caja) {
                $venta = Venta::lockForUpdate()->findOrFail($id);

                if ($venta->estado === 'Anulada') {
                    throw new \RuntimeException('La venta ya está anulada.');
                }

                $venta->update([
                'estado' => 'Anulada',
                'motivo_anulacion' => $validated['motivo_anulacion'] ?? null,
                'fecha_anulacion' => now(),
                ]);

                if ($caja) {
                    foreach ($montosCaja as $medio => $monto) {
                        MovimientoCaja::create([
                            'caja_id' => $caja->id,
                            'venta_anulada_id' => $venta->id,
                            'tipo' => 'egreso',
                            'concepto' => 'Anulación Venta #' . $venta->id,
                            'medio' => $medio,
                            'monto' => $monto,
                            'observacion' => $validated['motivo_anulacion'] ?? null,
                        ]);
                    }
                }
            });
        } catch (\RuntimeException $exception) {
            return redirect('/ventas')->with('error', $exception->getMessage());
        }

        return redirect('/ventas')
            ->with('success', 'La venta fue anulada correctamente.');
    }

    private function montosCaja(array $montos): array
    {
        return array_filter($montos, function ($monto, $medio) {
            return $medio !== 'cuenta_corriente' && (float) $monto > 0;
        }, ARRAY_FILTER_USE_BOTH);
    }

    private function cajaAbiertaParaPagos(array $montosCaja): ?Caja
    {
        if (!$montosCaja) {
            return null;
        }

        return Caja::whereDate('fecha', now()->toDateString())
            ->where('estado', 'abierta')
            ->first();
    }

    private function crearMovimientosIngreso(Venta $venta, ?Caja $caja, array $montosCaja): void
    {
        if (!$caja) {
            return;
        }

        foreach ($montosCaja as $medio => $monto) {
            MovimientoCaja::create([
                'caja_id' => $caja->id,
                'venta_id' => $venta->id,
                'tipo' => 'ingreso',
                'concepto' => 'Venta #' . $venta->id,
                'medio' => $medio,
                'monto' => $monto,
            ]);
        }
    }
}