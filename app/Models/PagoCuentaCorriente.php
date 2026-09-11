<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoCuentaCorriente extends Model
{
    protected $table = 'pagos_cuenta_corriente';

    protected $fillable = [
        'cliente_id',
        'proveedor_id',
        'monto',
        'medio',
        'observacion',
        'movimiento_caja_id',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function movimientoCaja()
    {
        return $this->belongsTo(MovimientoCaja::class);
    }
}