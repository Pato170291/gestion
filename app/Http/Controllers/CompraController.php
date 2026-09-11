<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\CompraDetalle;
use App\Models\CompraPago;
use App\Models\Caja;
use App\Models\MovimientoCaja;
use App\Models\MovimientoStock;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller
{
    public function index(Request $request)
    {
        $buscar = trim($request->input('buscar', ''));
        $consulta = Compra::with(['proveedor', 'detalles', 'pagos'])
            ->orderByDesc('updated_at')
            ->orderByDesc('id');

        if ($buscar !== '') {
            $consulta->where(function ($query) use ($buscar) {
                $query->where('fecha', 'like', "%{$buscar}%")
                    ->orWhere('estado', 'like', "%{$buscar}%")
                    ->orWhere('total', 'like', "%{$buscar}%")
                    ->orWhereHas('proveedor', function ($proveedorQuery) use ($buscar) {
                        $proveedorQuery->where('empresa', 'like', "%{$buscar}%");
                    });
            });
        }

        $compras = $consulta->paginate(6)->appends(['buscar' => $buscar]);

        if ($request->ajax()) {
            return view('partials.compras-ajax', compact('compras'));
        }

        return view('compras', compact('compras'));
    }

    public function crear()
    {
        $proveedores = Proveedor::where('activo', true)->orderBy('empresa')->get();
        $productos = Producto::orderBy('nombre')->get();

        return view('crear-compra', compact('proveedores', 'productos'));
    }

    public function store(Request $request)
    {
        $validated = $this->validarCompra($request);
        $datos = $this->prepararCompra($validated);
        $montosCaja = $this->montosCaja($datos['montos']);
        $caja = $this->cajaAbiertaParaPagos($montosCaja);

        if ($montosCaja && !$caja) {
            return back()
                ->withErrors(['formas_pago' => 'Para registrar una compra con pago en efectivo, tarjeta o transferencia debe haber una caja abierta.'])
                ->withInput();
        }

        DB::transaction(function () use ($validated, $datos, $montosCaja, $caja) {
            $compra = Compra::create([
                'fecha' => $validated['fecha'],
                'proveedor_id' => $validated['proveedor_id'] ?? null,
                'total' => $datos['total'],
                'total_pagado' => $datos['total_pagado'],
                'estado' => $datos['estado'],
            ]);

            $this->guardarDetalleYPagos($compra, $datos);
            $this->crearMovimientosEgreso($compra, $caja, $montosCaja);
            $this->generarMovimientosStock($compra, $datos);
        });

        return redirect('/compras')->with('success', 'Compra guardada correctamente.');
    }

    public function show($id)
    {
        $compra = Compra::with(['proveedor', 'detalles', 'pagos'])->findOrFail($id);

        return view('detalle-compra', compact('compra'));
    }

    public function edit($id)
    {
        $compra = Compra::with(['detalles', 'pagos'])->findOrFail($id);

        if ($compra->estado === 'Anulada') {
            return redirect('/compras')
                ->with('error', 'Una compra anulada no se puede editar.');
        }

        $proveedores = Proveedor::where('activo', true)->orderBy('empresa')->get();
        $productos = Producto::orderBy('nombre')->get();

        return view('editar-compra', compact('compra', 'proveedores', 'productos'));
    }

    public function update(Request $request, $id)
    {
        $compra = Compra::with(['detalles', 'pagos'])->findOrFail($id);

        if ($compra->estado === 'Anulada') {
            return redirect('/compras')
                ->with('error', 'Una compra anulada no se puede editar.');
        }

        $validated = $this->validarCompra($request);
        $datos = $this->prepararCompra($validated);
        $montosCaja = $this->montosCaja($datos['montos']);
        $caja = $this->cajaAbiertaParaPagos($montosCaja);

        if ($montosCaja && !$caja) {
            return back()
                ->withErrors(['formas_pago' => 'Para registrar una compra con pago en efectivo, tarjeta o transferencia debe haber una caja abierta.'])
                ->withInput();
        }

        $movimientosExistentes = MovimientoCaja::with('caja')
            ->where('compra_id', $compra->id)
            ->get();

        if ($movimientosExistentes->contains(function ($movimiento) {
            return $movimiento->caja && $movimiento->caja->estado !== 'abierta';
        })) {
            return redirect('/compras')
                ->with('error', 'No se puede editar una compra cuyos movimientos pertenecen a una caja cerrada.');
        }

        DB::transaction(function () use ($compra, $validated, $datos, $montosCaja, $caja) {
            $compra->update([
                'fecha' => $validated['fecha'],
                'proveedor_id' => $validated['proveedor_id'] ?? null,
                'total' => $datos['total'],
                'total_pagado' => $datos['total_pagado'],
                'estado' => $datos['estado'],
            ]);

            $compra->detalles()->delete();
            $compra->pagos()->delete();
            MovimientoCaja::where('compra_id', $compra->id)->delete();
            MovimientoStock::where('compra_id', $compra->id)->delete();
            $this->guardarDetalleYPagos($compra, $datos);
            $this->crearMovimientosEgreso($compra, $caja, $montosCaja);
            $this->generarMovimientosStock($compra, $datos);
        });

        return redirect('/compras')->with('success', 'Compra actualizada correctamente.');
    }

    public function anular(Request $request, $id)
    {
        $compra = Compra::findOrFail($id);

        if ($compra->estado === 'Anulada') {
            return redirect('/compras')
                ->with('error', 'La compra ya está anulada.');
        }

        $validated = $request->validate([
            'motivo_anulacion' => 'nullable|string',
        ]);

        $movimientosOriginales = MovimientoCaja::where('compra_id', $compra->id)->get();
        $caja = $movimientosOriginales->isNotEmpty()
            ? $this->cajaAbiertaParaReversion()
            : null;

        if ($movimientosOriginales->isNotEmpty() && !$caja) {
            return redirect('/compras')
                ->with('error', 'No se puede anular esta compra porque tiene movimientos de Caja asociados y actualmente no hay una caja abierta para registrar la reversión.');
        }

        try {
            DB::transaction(function () use ($id, $validated, $movimientosOriginales, $caja) {
                $compra = Compra::lockForUpdate()->findOrFail($id);

                if ($compra->estado === 'Anulada') {
                    throw new \RuntimeException('La compra ya está anulada.');
                }

                if ($caja) {
                    $caja = Caja::whereKey($caja->id)
                        ->where('estado', 'abierta')
                        ->lockForUpdate()
                        ->first();

                    if (!$caja) {
                        throw new \RuntimeException('No se puede anular esta compra porque la caja ya no está abierta para registrar la reversión.');
                    }
                }

                $compra->update([
                    'estado' => 'Anulada',
                    'motivo_anulacion' => $validated['motivo_anulacion'] ?? null,
                    'fecha_anulacion' => now(),
                ]);

                if ($caja) {
                    foreach ($movimientosOriginales as $movimiento) {
                        MovimientoCaja::create([
                            'caja_id' => $caja->id,
                            'compra_anulada_id' => $compra->id,
                            'tipo' => 'ingreso',
                            'concepto' => 'Anulación Compra #' . $compra->id,
                            'medio' => $movimiento->medio,
                            'monto' => $movimiento->monto,
                            'observacion' => $validated['motivo_anulacion'] ?? null,
                        ]);
                    }
                }

                $this->revertirMovimientosStock($compra);
            });
        } catch (\RuntimeException $exception) {
            return redirect('/compras')->with('error', $exception->getMessage());
        }

        return redirect('/compras')->with('success', 'La compra fue anulada correctamente.');
    }

    private function validarCompra(Request $request): array
    {
        return $request->validate([
            'fecha' => 'required|date',
            'proveedor_id' => 'nullable|exists:proveedores,id',
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
    }

    private function prepararCompra(array $validated): array
    {
        $cantidad = (int) $validated['cantidad'];
        $precio = round((float) $validated['precio'], 2);
        $total = round($cantidad * $precio, 2);
        $montos = [];

        if (count($validated['formas_pago']) === 1) {
            $montos[$validated['formas_pago'][0]] = $total;
        } else {
            foreach ($validated['formas_pago'] as $formaPago) {
                if (!array_key_exists($formaPago, $validated['montos_pago'] ?? [])) {
                    abort(422, 'Debe indicar el monto de cada forma de pago.');
                }

                $montos[$formaPago] = round((float) $validated['montos_pago'][$formaPago], 2);
            }
        }

        $totalPagado = round(array_sum(array_filter(
            $montos,
            fn ($formaPago) => $formaPago !== 'cuenta_corriente',
            ARRAY_FILTER_USE_KEY
        )), 2);

        if ($totalPagado > $total) {
            abort(422, 'La suma de los pagos no puede superar el total de la compra.');
        }

        $productoId = $validated['producto_id'] === 'otro' ? null : $validated['producto_id'];
        $productoNombre = 'Otro';

        if ($productoId !== null) {
            $productoNombre = Producto::findOrFail($productoId)->nombre;
        }

        return [
            'cantidad' => $cantidad,
            'precio' => $precio,
            'total' => $total,
            'total_pagado' => $totalPagado,
            'estado' => $totalPagado >= $total ? 'Pagado' : ($totalPagado > 0 ? 'Parcial' : 'Pendiente'),
            'producto_id' => $productoId,
            'producto_nombre' => $productoNombre,
            'montos' => $montos,
        ];
    }

    private function guardarDetalleYPagos(Compra $compra, array $datos): void
    {
        $compra->detalles()->create([
            'producto_id' => $datos['producto_id'],
            'producto_nombre' => $datos['producto_nombre'],
            'cantidad' => $datos['cantidad'],
            'precio' => $datos['precio'],
            'subtotal' => $datos['total'],
        ]);

        foreach ($datos['montos'] as $formaPago => $monto) {
            $compra->pagos()->create([
                'forma_pago' => $formaPago,
                'monto' => $monto,
            ]);
        }
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

    private function cajaAbiertaParaReversion(): ?Caja
    {
        return Caja::whereDate('fecha', now()->toDateString())
            ->where('estado', 'abierta')
            ->first();
    }

    private function crearMovimientosEgreso(Compra $compra, ?Caja $caja, array $montosCaja): void
    {
        if (!$caja) {
            return;
        }

        foreach ($montosCaja as $medio => $monto) {
            MovimientoCaja::create([
                'caja_id' => $caja->id,
                'compra_id' => $compra->id,
                'tipo' => 'egreso',
                'concepto' => 'Compra #' . $compra->id,
                'medio' => $medio,
                'monto' => $monto,
            ]);
        }
    }

    // "Otro" (producto_id null) no controla Stock: solo se generan entradas para productos reales del catálogo.
    private function generarMovimientosStock(Compra $compra, array $datos): void
    {
        if ($datos['producto_id'] === null || $datos['cantidad'] <= 0) {
            return;
        }

        MovimientoStock::create([
            'producto_id' => $datos['producto_id'],
            'compra_id' => $compra->id,
            'tipo' => 'entrada',
            'cantidad' => $datos['cantidad'],
            'motivo' => 'Compra #' . $compra->id,
            'fecha' => $compra->fecha,
        ]);
    }

    // Genera el movimiento inverso (salida) de cada entrada original de la compra, sin borrar el historial.
    private function revertirMovimientosStock(Compra $compra): void
    {
        $entradas = MovimientoStock::where('compra_id', $compra->id)
            ->where('tipo', 'entrada')
            ->lockForUpdate()
            ->get();

        $yaRevertido = MovimientoStock::where('compra_id', $compra->id)
            ->where('tipo', 'salida')
            ->exists();

        if ($yaRevertido) {
            return;
        }

        foreach ($entradas as $entrada) {
            MovimientoStock::create([
                'producto_id' => $entrada->producto_id,
                'compra_id' => $compra->id,
                'tipo' => 'salida',
                'cantidad' => $entrada->cantidad,
                'motivo' => 'Anulación Compra #' . $compra->id,
                'fecha' => now()->toDateString(),
            ]);
        }
    }
}