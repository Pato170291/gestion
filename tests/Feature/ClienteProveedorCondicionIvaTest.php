<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Proveedor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClienteProveedorCondicionIvaTest extends TestCase
{
    use RefreshDatabase;

    public function test_crear_cliente_con_cuit_y_condicion_iva(): void
    {
        $response = $this->post('/clientes', [
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'telefono' => '2231234567',
            'email' => 'juan@example.com',
            'cuit' => '20304050607',
            'condicion_iva' => 'Responsable Inscripto',
        ]);

        $response->assertRedirect('/clientes');

        $cliente = Cliente::where('email', 'juan@example.com')->firstOrFail();
        $this->assertSame('20304050607', $cliente->cuit);
        $this->assertSame('Responsable Inscripto', $cliente->condicion_iva);
    }

    public function test_crear_cliente_sin_condicion_queda_consumidor_final(): void
    {
        $this->post('/clientes', [
            'nombre' => 'Ana',
            'apellido' => 'Gómez',
            'telefono' => '2231112233',
            'email' => 'ana@example.com',
        ])->assertRedirect('/clientes');

        $cliente = Cliente::where('email', 'ana@example.com')->firstOrFail();
        $this->assertSame('Consumidor Final', $cliente->condicion_iva);
        $this->assertNull($cliente->cuit);
    }

    public function test_editar_cliente_permite_modificar_cuit_y_condicion(): void
    {
        $cliente = Cliente::factory()->create(['cuit' => null, 'condicion_iva' => 'Consumidor Final']);

        $this->put("/clientes/{$cliente->id}", [
            'nombre' => $cliente->nombre,
            'apellido' => $cliente->apellido,
            'telefono' => $cliente->telefono,
            'email' => $cliente->email,
            'cuit' => '27111222333',
            'condicion_iva' => 'Monotributista',
        ])->assertRedirect('/clientes');

        $cliente->refresh();
        $this->assertSame('27111222333', $cliente->cuit);
        $this->assertSame('Monotributista', $cliente->condicion_iva);
    }

    public function test_clientes_existentes_quedan_con_consumidor_final_por_defecto(): void
    {
        $cliente = Cliente::factory()->create();

        $this->assertSame('Consumidor Final', $cliente->fresh()->condicion_iva);
    }

    public function test_crear_y_editar_proveedor_con_condicion_iva(): void
    {
        $this->post('/proveedores', [
            'empresa' => 'Distribuidora Sur',
            'telefono' => '2235556677',
            'condicion_iva' => 'Exento',
        ])->assertRedirect('/proveedores');

        $proveedor = Proveedor::where('empresa', 'Distribuidora Sur')->firstOrFail();
        $this->assertSame('Exento', $proveedor->condicion_iva);

        $this->put("/proveedores/{$proveedor->id}", [
            'empresa' => $proveedor->empresa,
            'telefono' => $proveedor->telefono,
            'condicion_iva' => 'Responsable Inscripto',
            'activo' => 1,
        ])->assertRedirect('/proveedores');

        $this->assertSame('Responsable Inscripto', $proveedor->fresh()->condicion_iva);
    }

    public function test_proveedor_sin_condicion_queda_consumidor_final(): void
    {
        $this->post('/proveedores', [
            'empresa' => 'Mayorista Norte',
            'telefono' => '2239998877',
        ])->assertRedirect('/proveedores');

        $proveedor = Proveedor::where('empresa', 'Mayorista Norte')->firstOrFail();
        $this->assertSame('Consumidor Final', $proveedor->condicion_iva);
    }
}
