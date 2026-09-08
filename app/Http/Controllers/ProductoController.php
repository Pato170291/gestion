<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::orderBy('id')->get();

        return view('productos', compact('productos'));
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

        return view('editar-producto', compact('producto'));
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
            'stock_actual' => 'nullable|integer|min:0',
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
        $producto->stock_minimo = $validated['stock_minimo'] ?? 0;
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
