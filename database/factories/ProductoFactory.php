<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductoFactory extends Factory
{
    public function definition(): array
    {
        $tieneVencimiento = fake()->boolean(25);

        return [
            'nombre' => fake()->randomElement([
                'Coca Cola 1.5L',
                'Tornillo 8 mm',
                'Caña de pescar',
                'Arroz 1 kg',
                'Yerba mate 500 g',
                'Pintura blanca 1L',
            ]) . ' ' . fake()->unique()->numberBetween(1, 999),
            'marca' => fake()->company(),
            'descripcion' => fake()->optional()->sentence(6),
            'precio_compra' => fake()->numberBetween(100, 50000),
            'precio_venta' => fake()->numberBetween(150, 70000),
            'stock_actual' => fake()->numberBetween(0, 100),
            'stock_minimo' => fake()->numberBetween(1, 20),
            'unidad' => fake()->randomElement(['Unidad', 'Caja', 'Kilogramo', 'Litro']),
            'proveedor' => fake()->company(),
            'tiene_vencimiento' => $tieneVencimiento,
            'fecha_vencimiento' => $tieneVencimiento ? fake()->dateTimeBetween('+1 month', '+2 years')->format('Y-m-d') : null,
        ];
    }
}
