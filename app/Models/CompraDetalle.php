<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompraDetalle extends Model
{
    protected $fillable = [
        'compra_id',
        'producto_id',
        'producto_nombre',
        'cantidad',
        'precio',
        'subtotal',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function compra()
    {
        return $this->belongsTo(Compra::class);
    }
}