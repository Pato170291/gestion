<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientoCaja extends Model
{
    use HasFactory;

    protected $table = 'movimientos_caja';

    protected $fillable = [
        'caja_id',
        'venta_id',
        'venta_anulada_id',
        'compra_id',
        'compra_anulada_id',
        'tipo',
        'concepto',
        'medio',
        'monto',
        'observacion',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
    ];

    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function ventaAnulada()
    {
        return $this->belongsTo(Venta::class, 'venta_anulada_id');
    }

    public function compra()
    {
        return $this->belongsTo(Compra::class);
    }

    public function compraAnulada()
    {
        return $this->belongsTo(Compra::class, 'compra_anulada_id');
    }

    public function pagoCuentaCorriente()
    {
        return $this->hasOne(PagoCuentaCorriente::class);
    }
}