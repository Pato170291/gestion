<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\CompraDetalle;
use App\Models\CompraPago;
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

        DB::transaction(function () use ($validated, $datos) {
            $compra = Compra::create([
                'fecha' => $validated['fecha'],
                'proveedor_id' => $validated['proveedor_id'] ?? null,
                'total' => $datos['total'],
                'total_pagado' => $datos['total_pagado'],
                'estado' => $datos['estado'],
            ]);

            $this->guardarDetalleYPagos($compra, $datos);
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
        $proveedores = Proveedor::where('activo', true)->orderBy('empresa')->get();
        $productos = Producto::orderBy('nombre')->get();

        return view('editar-compra', compact('compra', 'proveedores', 'productos'));
    }

    public function update(Request $request, $id)
    {
        $compra = Compra::with(['detalles', 'pagos'])->findOrFail($id);
        $validated = $this->validarCompra($request);
        $datos = $this->prepararCompra($validated);

        DB::transaction(function () use ($compra, $validated, $datos) {
            $compra->update([
                'fecha' => $validated['fecha'],
                'proveedor_id' => $validated['proveedor_id'] ?? null,
                'total' => $datos['total'],
                'total_pagado' => $datos['total_pagado'],
                'estado' => $datos['estado'],
            ]);

            $compra->detalles()->delete();
            $compra->pagos()->delete();
            $this->guardarDetalleYPagos($compra, $datos);
        });

        return redirect('/compras')->with('success', 'Compra actualizada correctamente.');
    }

    public function destroy($id)
    {
        $compra = Compra::findOrFail($id);
        $compra->delete();

        return redirect('/compras')->with('success', 'Compra eliminada correctamente.');
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
}