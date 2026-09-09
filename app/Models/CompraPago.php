<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompraPago extends Model
{
    protected $fillable = [
        'compra_id',
        'forma_pago',
        'monto',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
    ];

    public function compra()
    {
        return $this->belongsTo(Compra::class);
    }
}