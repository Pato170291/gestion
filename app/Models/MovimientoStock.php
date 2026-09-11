<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoStock extends Model
{
    protected $table = 'movimientos_stock';

    protected $fillable = [
        'producto_id',
        'compra_id',
        'tipo',
        'cantidad',
        'motivo',
        'fecha',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'fecha' => 'date',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function compra()
    {
        return $this->belongsTo(Compra::class);
    }

    public function getCantidadTextoAttribute(): string
    {
        $signo = match ($this->tipo) {
            'entrada' => '+',
            'salida' => '-',
            default => '',
        };

        return $signo . $this->cantidad;
    }
}
