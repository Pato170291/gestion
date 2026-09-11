<?php

namespace Tests\Feature;

use App\Models\MovimientoStock;
use App\Models\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockMovimientoTest extends TestCase
{
    use RefreshDatabase;

    public function test_entrada_salida_y_entrada_calculan_el_stock_correctamente(): void
    {
        $producto = Producto::factory()->create(['stock_actual' => 0, 'stock_minimo' => 5]);

        $this->post(route('stock.movimientos.store'), [
            'producto_id' => $producto->id,
            'tipo' => 'entrada',
            'cantidad' => 10,
            'motivo' => 'Compra',
            'fecha' => now()->toDateString(),
        ])->assertRedirect(route('stock.index'));

        $this->assertSame(10, $producto->fresh()->calcularStockActual());

        $this->post(route('stock.movimientos.store'), [
            'producto_id' => $producto->id,
            'tipo' => 'salida',
            'cantidad' => 3,
            'motivo' => 'Venta',
            'fecha' => now()->toDateString(),
        ])->assertRedirect(route('stock.index'));

        $this->assertSame(7, $producto->fresh()->calcularStockActual());

        $this->post(route('stock.movimientos.store'), [
            'producto_id' => $producto->id,
            'tipo' => 'entrada',
            'cantidad' => 5,
            'motivo' => 'Compra',
            'fecha' => now()->toDateString(),
        ])->assertRedirect(route('stock.index'));

        $this->assertSame(12, $producto->fresh()->calcularStockActual());
        $this->assertSame(3, MovimientoStock::where('producto_id', $producto->id)->count());
    }

    public function test_historial_se_ordena_del_mas_reciente_al_mas_antiguo(): void
    {
        $producto = Producto::factory()->create();

        MovimientoStock::create(['producto_id' => $producto->id, 'tipo' => 'entrada', 'cantidad' => 10, 'motivo' => 'Compra', 'fecha' => '2026-09-01']);
        MovimientoStock::create(['producto_id' => $producto->id, 'tipo' => 'salida', 'cantidad' => 2, 'motivo' => 'Venta', 'fecha' => '2026-09-05']);

        $response = $this->get(route('stock.index'));

        $response->assertOk();
        $movimientos = $response->viewData('movimientos');
        $this->assertTrue($movimientos->first()->fecha->toDateString() === '2026-09-05');
    }

    public function test_ajuste_fija_el_stock_al_valor_indicado(): void
    {
        $producto = Producto::factory()->create(['stock_actual' => 0]);

        MovimientoStock::create(['producto_id' => $producto->id, 'tipo' => 'entrada', 'cantidad' => 10, 'motivo' => 'Compra', 'fecha' => now()->toDateString()]);
        $this->assertSame(10, $producto->fresh()->calcularStockActual());

        $this->post(route('stock.movimientos.store'), [
            'producto_id' => $producto->id,
            'tipo' => 'ajuste',
            'cantidad' => 8,
            'motivo' => 'Corrección de inventario',
            'fecha' => now()->toDateString(),
        ])->assertRedirect(route('stock.index'));

        $this->assertSame(8, $producto->fresh()->calcularStockActual());
    }

    public function test_salida_mayor_al_stock_disponible_es_rechazada(): void
    {
        $producto = Producto::factory()->create(['stock_actual' => 0]);

        MovimientoStock::create(['producto_id' => $producto->id, 'tipo' => 'entrada', 'cantidad' => 5, 'motivo' => 'Compra', 'fecha' => now()->toDateString()]);

        $this->post(route('stock.movimientos.store'), [
            'producto_id' => $producto->id,
            'tipo' => 'salida',
            'cantidad' => 20,
            'motivo' => 'Venta',
            'fecha' => now()->toDateString(),
        ])->assertSessionHasErrors('cantidad');

        $this->assertSame(5, $producto->fresh()->calcularStockActual());
        $this->assertSame(1, MovimientoStock::where('producto_id', $producto->id)->count());
    }

    public function test_estados_se_determinan_segun_stock_y_minimo(): void
    {
        $sinStock = Producto::factory()->create(['stock_actual' => 0, 'stock_minimo' => 5]);
        $bajo = Producto::factory()->create(['stock_actual' => 0, 'stock_minimo' => 5]);
        $normal = Producto::factory()->create(['stock_actual' => 0, 'stock_minimo' => 5]);

        MovimientoStock::create(['producto_id' => $bajo->id, 'tipo' => 'entrada', 'cantidad' => 5, 'motivo' => 'Compra', 'fecha' => now()->toDateString()]);
        MovimientoStock::create(['producto_id' => $normal->id, 'tipo' => 'entrada', 'cantidad' => 6, 'motivo' => 'Compra', 'fecha' => now()->toDateString()]);

        $this->assertSame('sin-stock', $sinStock->estadoStock());
        $this->assertSame('bajo', $bajo->estadoStock());
        $this->assertSame('normal', $normal->estadoStock());
    }

    public function test_creacion_de_producto_genera_movimiento_de_stock_inicial(): void
    {
        $response = $this->post('/productos', [
            'nombre' => 'Producto de prueba',
            'precio_compra' => 100,
            'precio_venta' => 150,
            'cantidad_inicial' => 20,
            'stock_minimo' => 5,
            'tiene_vencimiento' => false,
        ]);

        $response->assertRedirect('/productos');

        $producto = Producto::where('nombre', 'Producto de prueba')->firstOrFail();
        $this->assertSame(20, $producto->calcularStockActual());
        $this->assertSame(1, $producto->movimientosStock()->count());
        $this->assertSame('entrada', $producto->movimientosStock()->first()->tipo);
    }
}
