<?php

namespace Tests\Feature;

use App\Models\MovimientoStock;
use App\Models\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductoStockDesacopladoTest extends TestCase
{
    use RefreshDatabase;

    public function test_editar_producto_no_modifica_el_stock_actual(): void
    {
        $producto = Producto::factory()->create(['stock_actual' => 0, 'stock_minimo' => 2]);

        MovimientoStock::create([
            'producto_id' => $producto->id,
            'tipo' => 'entrada',
            'cantidad' => 15,
            'motivo' => 'Compra',
            'fecha' => now()->toDateString(),
        ]);

        $this->assertSame(15, $producto->fresh()->calcularStockActual());

        $response = $this->put(route('productos.update', $producto->id), [
            'nombre' => 'Nombre editado',
            'marca' => 'Marca nueva',
            'precio_compra' => 500,
            'precio_venta' => 800,
            'stock_minimo' => 9,
            'tiene_vencimiento' => false,
        ]);

        $response->assertRedirect('/productos');

        $producto->refresh();
        $this->assertSame('Nombre editado', $producto->nombre);
        $this->assertSame(9, $producto->stock_minimo);
        $this->assertSame(15, $producto->calcularStockActual());
        $this->assertSame(1, MovimientoStock::where('producto_id', $producto->id)->count());
    }

    public function test_no_se_puede_enviar_stock_actual_en_la_edicion(): void
    {
        $producto = Producto::factory()->create(['stock_actual' => 0]);

        MovimientoStock::create([
            'producto_id' => $producto->id,
            'tipo' => 'entrada',
            'cantidad' => 5,
            'motivo' => 'Compra',
            'fecha' => now()->toDateString(),
        ]);

        $this->put(route('productos.update', $producto->id), [
            'nombre' => $producto->nombre,
            'precio_compra' => $producto->precio_compra,
            'stock_actual' => 999,
            'tiene_vencimiento' => false,
        ])->assertRedirect('/productos');

        $this->assertSame(5, $producto->fresh()->calcularStockActual());
    }
}
