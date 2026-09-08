<?php

namespace Database\Seeders;
    
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cliente;
use App\Models\Proveedor;
use App\Models\Producto;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Cliente::factory(100)->create();

        Proveedor::factory()->count(20)->create();

        Producto::factory()->count(80)->create();
    }
}
