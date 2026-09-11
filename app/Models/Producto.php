<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';

    protected $casts = [
        'activo' => 'boolean',
        'tiene_vencimiento' => 'boolean',
        'fecha_vencimiento' => 'date',
    ];

    public function movimientosStock()
    {
        return $this->hasMany(MovimientoStock::class);
    }

    /**
     * Recorre los movimientos en orden cronológico: entrada suma, salida resta
     * y ajuste fija el stock al valor indicado (corrección directa).
     */
    public function calcularStockActual(): int
    {
        $stock = 0;

        foreach ($this->movimientosStock()->orderBy('fecha')->orderBy('id')->get() as $movimiento) {
            $stock = match ($movimiento->tipo) {
                'entrada' => $stock + $movimiento->cantidad,
                'salida' => max(0, $stock - $movimiento->cantidad),
                'ajuste' => $movimiento->cantidad,
                default => $stock,
            };
        }

        return $stock;
    }

    public function estadoStock(?int $stockActual = null): string
    {
        $stockActual ??= $this->calcularStockActual();

        if ($stockActual <= 0) {
            return 'sin-stock';
        }

        if ($stockActual <= $this->stock_minimo) {
            return 'bajo';
        }

        return 'normal';
    }
}
