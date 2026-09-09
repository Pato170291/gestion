<?php

namespace Database\Factories;

use App\Models\Compra;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Compra>
 */
class CompraFactory extends Factory
{
    protected $model = Compra::class;

    public function definition(): array
    {
        $total = fake()->randomFloat(2, 1000, 100000);
        $estado = fake()->randomElement(['Pagado', 'Parcial', 'Pendiente']);
        $totalPagado = match ($estado) {
            'Pagado' => $total,
            'Parcial' => fake()->randomFloat(2, 1, $total - 0.01),
            default => 0,
        };

        return [
            'fecha' => fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'proveedor_id' => Proveedor::query()->inRandomOrder()->value('id'),
            'total' => $total,
            'total_pagado' => $totalPagado,
            'estado' => $estado,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Compra $compra): void {
            $producto = Producto::query()->inRandomOrder()->first();

            $compra->detalles()->create([
                'producto_id' => $producto?->id,
                'producto_nombre' => $producto?->nombre ?? 'Otro',
                'cantidad' => 1,
                'precio' => $compra->total,
                'subtotal' => $compra->total,
            ]);

            if ((float) $compra->total_pagado > 0) {
                $compra->pagos()->create([
                    'forma_pago' => fake()->randomElement(['efectivo', 'tarjeta', 'transferencia']),
                    'monto' => $compra->total_pagado,
                ]);
            }
        });
    }
}