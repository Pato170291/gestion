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

    public function movimientosCaja()
    {
        return $this->hasMany(MovimientoCaja::class);
    }

    public function reversionesCaja()
    {
        return $this->hasMany(MovimientoCaja::class, 'venta_anulada_id');
    }
}
