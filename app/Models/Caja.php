<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha',
        'saldo_inicial',
        'estado',
        'ingresos',
        'egresos',
        'saldo_final',
        'dinero_contado',
        'diferencia',
        'fecha_cierre',
    ];

    protected $casts = [
        'fecha' => 'date',
        'saldo_inicial' => 'decimal:2',
        'ingresos' => 'decimal:2',
        'egresos' => 'decimal:2',
        'saldo_final' => 'decimal:2',
        'dinero_contado' => 'decimal:2',
        'diferencia' => 'decimal:2',
        'fecha_cierre' => 'datetime',
    ];
}
