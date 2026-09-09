<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha',
        'cliente_id',
        'total',
        'total_pagado',
        'estado',
    ];

    protected $casts = [
        'fecha' => 'date',
        'total' => 'decimal:2',
        'total_pagado' => 'decimal:2',
    ];

    public function detalles()
    {
        return $this->hasMany(VentaDetalle::class);
    }

    public function pagos()
    {
        return $this->hasMany(VentaPago::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
