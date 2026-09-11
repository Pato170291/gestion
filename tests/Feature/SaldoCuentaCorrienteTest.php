<?php

namespace Tests\Feature;

use App\Models\Caja;
use App\Models\Cliente;
use App\Models\Compra;
use App\Models\Proveedor;
use App\Models\Venta;
use App\Services\CuentaCorrienteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaldoCuentaCorrienteTest extends TestCase
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

    public function test_saldo_cliente_se_calcula_y_actualiza_con_pagos(): void
    {
        $this->abrirCaja();
        $servicio = new CuentaCorrienteService();
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

        $this->assertEquals(60000.0, $servicio->saldoCliente($cliente));

        $servicio->registrarPagoCliente($cliente, 20000, 'efectivo', null);
        $this->assertEquals(40000.0, $servicio->saldoCliente($cliente));

        $servicio->registrarPagoCliente($cliente, 40000, 'efectivo', null);
        $this->assertEquals(0.0, $servicio->saldoCliente($cliente));
    }

    public function test_venta_anulada_no_genera_deuda_para_cliente(): void
    {
        $servicio = new CuentaCorrienteService();
        $cliente = Cliente::factory()->create();

        $venta = Venta::create([
            'fecha' => now()->toDateString(),
            'cliente_id' => $cliente->id,
            'total' => 100000,
            'total_pagado' => 40000,
            'estado' => 'Anulada',
        ]);
        $venta->pagos()->create(['forma_pago' => 'efectivo', 'monto' => 40000]);
        $venta->pagos()->create(['forma_pago' => 'cuenta_corriente', 'monto' => 60000]);

        $this->assertEquals(0.0, $servicio->saldoCliente($cliente));
    }

    public function test_saldo_proveedor_se_calcula_y_actualiza_con_pagos(): void
    {
        $this->abrirCaja();
        $servicio = new CuentaCorrienteService();
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

        $this->assertEquals(70000.0, $servicio->saldoProveedor($proveedor));

        $servicio->registrarPagoProveedor($proveedor, 20000, 'efectivo', null);
        $this->assertEquals(50000.0, $servicio->saldoProveedor($proveedor));

        $servicio->registrarPagoProveedor($proveedor, 50000, 'efectivo', null);
        $this->assertEquals(0.0, $servicio->saldoProveedor($proveedor));
    }

    public function test_compra_anulada_no_genera_deuda_para_proveedor(): void
    {
        $servicio = new CuentaCorrienteService();
        $proveedor = Proveedor::factory()->create();

        $compra = Compra::create([
            'fecha' => now()->toDateString(),
            'proveedor_id' => $proveedor->id,
            'total' => 100000,
            'total_pagado' => 30000,
            'estado' => 'Anulada',
        ]);
        $compra->pagos()->create(['forma_pago' => 'efectivo', 'monto' => 30000]);
        $compra->pagos()->create(['forma_pago' => 'cuenta_corriente', 'monto' => 70000]);

        $this->assertEquals(0.0, $servicio->saldoProveedor($proveedor));
    }
}
