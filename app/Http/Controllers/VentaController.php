<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
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
            $montos
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
            $venta,
            $validated,
            $total,
            $totalPagado,
            $estado,
            $productoId,
            $productoNombre,
            $cantidad,
            $precio,
            $montos
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

            foreach ($montos as $formaPago => $monto) {
                $venta->pagos()->create([
                    'forma_pago' => $formaPago,
                    'monto' => $monto,
                ]);
            }
        });

        return redirect('/ventas')
            ->with('success', 'Venta actualizada correctamente.');
    }

    public function destroy($id)
    {
        $venta = Venta::findOrFail($id);

        DB::transaction(function () use ($venta) {
            $venta->detalles()->delete();
            $venta->pagos()->delete();
            $venta->delete();
        });

        return redirect('/ventas')
            ->with('success', 'Venta eliminada correctamente.');
    }
}