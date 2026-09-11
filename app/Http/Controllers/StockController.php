<?php

namespace App\Http\Controllers;

use App\Models\MovimientoStock;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class StockController extends Controller
{
    public function index()
    {
        $productos = Producto::orderBy('nombre')->get()->map(function (Producto $producto) {
            $producto->stock_calculado = $producto->calcularStockActual();
            $producto->estado_stock = $producto->estadoStock($producto->stock_calculado);

            return $producto;
        });

        $resumen = [
            'con_stock' => $productos->where('stock_calculado', '>', 0)->count(),
            'bajo' => $productos->where('estado_stock', 'bajo')->count(),
            'sin_stock' => $productos->where('estado_stock', 'sin-stock')->count(),
            'unidades_totales' => $productos->sum('stock_calculado'),
        ];

        $movimientos = MovimientoStock::with('producto')
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->get();

        $movimientosPorProducto = $movimientos
            ->groupBy('producto_id')
            ->map(function ($items) {
                return $items->map(function (MovimientoStock $movimiento) {
                    return [
                        'fecha' => $movimiento->fecha->format('d/m/Y'),
                        'tipo' => ucfirst($movimiento->tipo),
                        'tipo_clase' => $movimiento->tipo,
                        'cantidad' => $movimiento->cantidad_texto,
                        'motivo' => $movimiento->motivo,
                    ];
                })->values();
            });

        return view('stock', compact('productos', 'resumen', 'movimientos', 'movimientosPorProducto'));
    }

    public function guardarMovimiento(Request $request)
    {
        $validated = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'tipo' => 'required|in:entrada,salida,ajuste',
            'cantidad' => 'required|integer|min:0',
            'motivo' => 'required|string|max:255',
            'fecha' => 'required|date',
        ]);

        // Para entrada/salida la cantidad es la variación; para ajuste es el stock resultante deseado.
        if ($validated['tipo'] !== 'ajuste' && $validated['cantidad'] < 1) {
            throw ValidationException::withMessages([
                'cantidad' => 'La cantidad debe ser mayor que 0.',
            ]);
        }

        $producto = Producto::findOrFail($validated['producto_id']);

        if ($validated['tipo'] === 'salida') {
            $stockDisponible = $producto->calcularStockActual();

            if ($validated['cantidad'] > $stockDisponible) {
                throw ValidationException::withMessages([
                    'cantidad' => "La salida no puede superar el stock actual ({$stockDisponible}).",
                ]);
            }
        }

        MovimientoStock::create($validated);

        return redirect()->route('stock.index')
            ->with('success', 'El movimiento de stock fue registrado correctamente.');
    }
}
