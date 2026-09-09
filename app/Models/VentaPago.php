<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VentaPago extends Model
{
    protected $fillable = [
        'venta_id',
        'forma_pago',
        'monto',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }
}
