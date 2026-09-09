<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $buscar = trim($request->input('buscar', ''));

        $consulta = Producto::orderByDesc('updated_at')
            ->orderByDesc('id');

        if ($buscar !== '') {
            $consulta->where(function ($query) use ($buscar) {
                $query->where('nombre', 'like', "%{$buscar}%")
                    ->orWhere('marca', 'like', "%{$buscar}%")
                    ->orWhere('descripcion', 'like', "%{$buscar}%")
                    ->orWhere('proveedor', 'like', "%{$buscar}%")
                    ->orWhere('precio_compra', 'like', "%{$buscar}%")
                    ->orWhere('precio_venta', 'like', "%{$buscar}%")
                    ->orWhere('stock_actual', 'like', "%{$buscar}%")
                    ->orWhere('stock_minimo', 'like', "%{$buscar}%");
            });
        }

        $productos = $consulta->paginate(6)->appends(['buscar' => $buscar]);

        if ($request->ajax()) {
            return view('partials.productos-ajax', compact('productos'));
        }

        return view('productos', compact('productos'));
    }

    public function crear()
    {
        $proveedores = Proveedor::orderBy('empresa')->get();

        return view('crear-producto', compact('proveedores'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'marca' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'nullable|numeric|min:0',
            'stock_actual' => 'required|integer|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
            'unidad' => 'nullable|string|max:255',
            'proveedor' => 'nullable|string|max:255',
            'tiene_vencimiento' => 'required|boolean',
            'fecha_vencimiento' => 'nullable|date',
        ]);

        $producto = new Producto();
        $producto->nombre = $validated['nombre'];
        $producto->marca = $validated['marca'] ?? null;
        $producto->descripcion = $validated['descripcion'] ?? null;
        $producto->precio_compra = $validated['precio_compra'];
        $producto->precio_venta = $validated['precio_venta'] ?? 0;
        $producto->stock_actual = $validated['stock_actual'] ?? 0;
        $producto->stock_minimo = $validated['stock_minimo'] ?? 1;
        $producto->unidad = $validated['unidad'] ?? 'Unidad';
        $producto->proveedor = $validated['proveedor'] ?? null;
        $producto->tiene_vencimiento = $validated['tiene_vencimiento'];
        $producto->fecha_vencimiento = $validated['tiene_vencimiento']
            ? ($validated['fecha_vencimiento'] ?? null)
            : null;
        $producto->save();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Producto cargado con éxito.',
                'producto' => $producto,
            ]);
        }

        return redirect('/productos')->with('success', 'Producto creado correctamente.');
    }

    public function destroy($id)
    {
        $producto = Producto::findOrFail($id);
        $producto->delete();

        return redirect('/productos')->with('success', 'Producto eliminado correctamente.');
    }

    public function edit($id)
    {
        $producto = Producto::findOrFail($id);
        $proveedores = Proveedor::orderBy('empresa')->get();

        return view('editar-producto', compact('producto', 'proveedores'));
    }

    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'marca' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'nullable|numeric|min:0',
            'stock_actual' => 'required|integer|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
            'unidad' => 'nullable|string|max:255',
            'proveedor' => 'nullable|string|max:255',
            'tiene_vencimiento' => 'required|boolean',
            'fecha_vencimiento' => 'nullable|date',
        ]);

        $producto->nombre = $validated['nombre'];
        $producto->marca = $validated['marca'] ?? null;
        $producto->descripcion = $validated['descripcion'] ?? null;
        $producto->precio_compra = $validated['precio_compra'];
        $producto->precio_venta = $validated['precio_venta'] ?? 0;
        $producto->stock_actual = $validated['stock_actual'] ?? 0;
        $producto->stock_minimo = $validated['stock_minimo'] ?? 1;
        $producto->unidad = $validated['unidad'] ?? 'Unidad';
        $producto->proveedor = $validated['proveedor'] ?? null;
        $producto->tiene_vencimiento = $validated['tiene_vencimiento'];
        $producto->fecha_vencimiento = $validated['tiene_vencimiento']
            ? ($validated['fecha_vencimiento'] ?? null)
            : null;
        $producto->save();

        return redirect('/productos')->with('success', 'Producto actualizado correctamente.');
    }
}
