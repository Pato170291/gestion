<?php

namespace Tests\Feature;

use App\Models\Caja;
use App\Models\Compra;
use App\Models\MovimientoCaja;
use App\Models\MovimientoStock;
use App\Models\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompraStockTest extends TestCase
{
    use RefreshDatabase;

    private function abrirCaja(): void
    {
        Caja::create([
            'fecha' => now()->toDateString(),
            'saldo_inicial' => 0,
            'estado' => 'abierta',
        ]);
    }

    public function test_compra_simple_genera_entrada_de_stock(): void
    {
        $this->abrirCaja();
        $producto = Producto::factory()->create(['stock_actual' => 0]);

        $response = $this->post(route('compras.store'), [
            'fecha' => now()->toDateString(),
            'producto_id' => $producto->id,
            'cantidad' => 10,
            'precio' => 100,
            'formas_pago' => ['efectivo'],
        ]);

        $response->assertRedirect('/compras');

        $compra = Compra::latest('id')->first();
        $this->assertSame(10, $producto->fresh()->calcularStockActual());

        $movimiento = MovimientoStock::where('producto_id', $producto->id)->first();
        $this->assertNotNull($movimiento);
        $this->assertSame('entrada', $movimiento->tipo);
        $this->assertSame($compra->id, $movimiento->compra_id);
        $this->assertSame('Compra #' . $compra->id, $movimiento->motivo);
    }

    public function test_compra_a_cuenta_corriente_aumenta_stock_sin_generar_movimiento_de_caja(): void
    {
        $producto = Producto::factory()->create(['stock_actual' => 0]);

        $response = $this->post(route('compras.store'), [
            'fecha' => now()->toDateString(),
            'producto_id' => $producto->id,
            'cantidad' => 6,
            'precio' => 50,
            'formas_pago' => ['cuenta_corriente'],
        ]);

        $response->assertRedirect('/compras');

        $this->assertSame(6, $producto->fresh()->calcularStockActual());
        $this->assertSame(0, MovimientoCaja::count());
    }

    public function test_editar_compra_no_duplica_movimientos_de_stock(): void
    {
        $this->abrirCaja();
        $producto = Producto::factory()->create(['stock_actual' => 0]);

        $this->post(route('compras.store'), [
            'fecha' => now()->toDateString(),
            'producto_id' => $producto->id,
            'cantidad' => 10,
            'precio' => 100,
            'formas_pago' => ['efectivo'],
        ]);

        $compra = Compra::latest('id')->first();
        $this->assertSame(10, $producto->fresh()->calcularStockActual());

        $this->put(route('compras.update', $compra->id), [
            'fecha' => now()->toDateString(),
            'producto_id' => $producto->id,
            'cantidad' => 15,
            'precio' => 100,
            'formas_pago' => ['efectivo'],
        ])->assertRedirect('/compras');

        $this->assertSame(15, $producto->fresh()->calcularStockActual());
        $this->assertSame(1, MovimientoStock::where('compra_id', $compra->id)->count());
    }

    public function test_compra_con_otro_producto_no_genera_movimiento_de_stock(): void
    {
        $this->abrirCaja();

        $response = $this->post(route('compras.store'), [
            'fecha' => now()->toDateString(),
            'producto_id' => 'otro',
            'cantidad' => 3,
            'precio' => 200,
            'formas_pago' => ['efectivo'],
        ]);

        $response->assertRedirect('/compras');

        $this->assertSame(0, MovimientoStock::count());
    }

    public function test_anular_compra_genera_movimiento_inverso_y_revierte_el_stock(): void
    {
        $this->abrirCaja();
        $producto = Producto::factory()->create(['stock_actual' => 0]);

        // Stock previo de otra compra, para verificar que solo se revierte lo de la compra anulada.
        $this->post(route('compras.store'), [
            'fecha' => now()->toDateString(),
            'producto_id' => $producto->id,
            'cantidad' => 20,
            'precio' => 100,
            'formas_pago' => ['efectivo'],
        ]);
        $compraA = Compra::latest('id')->first();

        $this->post(route('compras.store'), [
            'fecha' => now()->toDateString(),
            'producto_id' => $producto->id,
            'cantidad' => 5,
            'precio' => 100,
            'formas_pago' => ['efectivo'],
        ]);

        $this->assertSame(25, $producto->fresh()->calcularStockActual());

        $this->post(route('compras.anular', $compraA->id), [
            'motivo_anulacion' => 'Error de carga',
        ])->assertRedirect('/compras');

        $this->assertSame(5, $producto->fresh()->calcularStockActual());

        $this->assertSame(2, MovimientoStock::where('compra_id', $compraA->id)->count());
        $reversion = MovimientoStock::where('compra_id', $compraA->id)->where('tipo', 'salida')->first();
        $this->assertNotNull($reversion);
        $this->assertSame(20, $reversion->cantidad);
        $this->assertSame('Anulación Compra #' . $compraA->id, $reversion->motivo);
    }

    public function test_no_se_puede_anular_dos_veces_la_misma_compra(): void
    {
        $this->abrirCaja();
        $producto = Producto::factory()->create(['stock_actual' => 0]);

        $this->post(route('compras.store'), [
            'fecha' => now()->toDateString(),
            'producto_id' => $producto->id,
            'cantidad' => 10,
            'precio' => 100,
            'formas_pago' => ['efectivo'],
        ]);
        $compra = Compra::latest('id')->first();

        $this->post(route('compras.anular', $compra->id), ['motivo_anulacion' => 'Motivo 1']);
        $this->assertSame(0, $producto->fresh()->calcularStockActual());

        $this->post(route('compras.anular', $compra->id), ['motivo_anulacion' => 'Motivo 2'])
            ->assertRedirect('/compras');

        $this->assertSame(0, $producto->fresh()->calcularStockActual());
        $this->assertSame(2, MovimientoStock::where('compra_id', $compra->id)->count());
    }
}
