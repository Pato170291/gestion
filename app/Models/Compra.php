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
        'motivo_anulacion',
        'fecha_anulacion',
    ];

    protected $casts = [
        'fecha' => 'date',
        'total' => 'decimal:2',
        'total_pagado' => 'decimal:2',
        'fecha_anulacion' => 'datetime',
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

    public function movimientosCaja()
    {
        return $this->hasMany(MovimientoCaja::class);
    }

    public function reversionesCaja()
    {
        return $this->hasMany(MovimientoCaja::class, 'compra_anulada_id');
    }

    public function movimientosStock()
    {
        return $this->hasMany(MovimientoStock::class);
    }
}