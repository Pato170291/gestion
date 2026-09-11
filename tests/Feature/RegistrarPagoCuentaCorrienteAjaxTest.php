<?php

namespace Tests\Feature;

use App\Models\Caja;
use App\Models\Cliente;
use App\Models\Compra;
use App\Models\Proveedor;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrarPagoCuentaCorrienteAjaxTest extends TestCase
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

    public function test_pago_cliente_con_monto_mayor_al_saldo_devuelve_error_json_sin_registrar_nada(): void
    {
        $this->abrirCaja();
        $cliente = Cliente::factory()->create();
        $venta = Venta::create([
            'fecha' => now()->toDateString(),
            'cliente_id' => $cliente->id,
            'total' => 100000,
            'total_pagado' => 40000,
            'estado' => 'Completada',
        ]);
        $venta->pagos()->create(['forma_pago' => 'efectivo', 'monto' => 40000]);
        $venta->pagos()->create(['forma_pago' => 'cuenta_corriente', 'monto' => 60000]);

        $response = $this->postJson("/clientes/{$cliente->id}/cuenta-corriente/pagos", [
            'monto' => 70000,
            'medio' => 'efectivo',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('monto');
        $this->assertDatabaseCount('pagos_cuenta_corriente', 0);
        $this->assertDatabaseCount('movimientos_caja', 0);
    }

    public function test_pago_cliente_valido_devuelve_json_de_exito(): void
    {
        $this->abrirCaja();
        $cliente = Cliente::factory()->create();
        $venta = Venta::create([
            'fecha' => now()->toDateString(),
            'cliente_id' => $cliente->id,
            'total' => 100000,
            'total_pagado' => 40000,
            'estado' => 'Completada',
        ]);
        $venta->pagos()->create(['forma_pago' => 'efectivo', 'monto' => 40000]);
        $venta->pagos()->create(['forma_pago' => 'cuenta_corriente', 'monto' => 60000]);

        $response = $this->postJson("/clientes/{$cliente->id}/cuenta-corriente/pagos", [
            'monto' => 20000,
            'medio' => 'efectivo',
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);
        $this->assertDatabaseCount('pagos_cuenta_corriente', 1);
    }

    public function test_pago_proveedor_con_monto_mayor_al_saldo_devuelve_error_json_sin_registrar_nada(): void
    {
        $this->abrirCaja();
        $proveedor = Proveedor::factory()->create();
        $compra = Compra::create([
            'fecha' => now()->toDateString(),
            'proveedor_id' => $proveedor->id,
            'total' => 100000,
            'total_pagado' => 30000,
            'estado' => 'Completada',
        ]);
        $compra->pagos()->create(['forma_pago' => 'efectivo', 'monto' => 30000]);
        $compra->pagos()->create(['forma_pago' => 'cuenta_corriente', 'monto' => 70000]);

        $response = $this->postJson("/proveedores/{$proveedor->id}/cuenta-corriente/pagos", [
            'monto' => 80000,
            'medio' => 'efectivo',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('monto');
        $this->assertDatabaseCount('pagos_cuenta_corriente', 0);
        $this->assertDatabaseCount('movimientos_caja', 0);
    }
}
