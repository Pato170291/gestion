<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedores';

    public const CONDICIONES_IVA = ['Consumidor Final', 'Monotributista', 'Responsable Inscripto', 'Exento'];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function pagosCuentaCorriente()
    {
        return $this->hasMany(PagoCuentaCorriente::class);
    }
}