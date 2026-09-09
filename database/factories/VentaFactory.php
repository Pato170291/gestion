<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Venta>
 */
class VentaFactory extends Factory
{
    protected $model = Venta::class;

    public function definition(): array
    {
        $precio = fake()->randomFloat(2, 100, 50000);
        $cantidad = fake()->numberBetween(1, 8);
        $total = round($precio * $cantidad, 2);

        return [
            'fecha' => fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'cliente_id' => Cliente::query()->inRandomOrder()->value('id'),
            'total' => $total,
            'total_pagado' => $total,
            'estado' => 'Pagado',
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Venta $venta): void {
            $producto = Producto::query()->inRandomOrder()->first();
            $detalle = $venta->detalles()->create([
                'producto_id' => $producto?->id,
                'producto_nombre' => $producto?->nombre ?? 'Otro',
                'cantidad' => 1,
                'precio' => $venta->total,
                'subtotal' => $venta->total,
            ]);

            $venta->pagos()->create([
                'forma_pago' => fake()->randomElement(['efectivo', 'tarjeta', 'transferencia', 'cuenta_corriente']),
                'monto' => $detalle->subtotal,
            ]);
        });
    }
}