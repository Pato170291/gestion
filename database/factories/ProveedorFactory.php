<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProveedorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'empresa' => fake()->company(),
            'contacto' => fake()->name(),
            'telefono' => '223' . fake()->numerify('########'),
            'email' => fake()->unique()->safeEmail(),
            'direccion' => fake()->streetAddress(),
            'cuit' => fake()->numerify('############'),
            'activo' => true,
        ];
    }
}
