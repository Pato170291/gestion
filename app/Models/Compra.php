<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha',
        'proveedor_id',
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
        return $this->hasMany(CompraDetalle::class);
    }

    public function pagos()
    {
        return $this->hasMany(CompraPago::class);
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }
}