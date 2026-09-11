<?php

namespace App\Services;

use App\Models\Caja;
use App\Models\Cliente;
use App\Models\Compra;
use App\Models\MovimientoCaja;
use App\Models\PagoCuentaCorriente;
use App\Models\Proveedor;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CuentaCorrienteService
{
    public function resumenCliente(Cliente $cliente): array
    {
        $movimientos = [];
        $ventas = Venta::with('pagos')
            ->where('cliente_id', $cliente->id)
            ->where('estado', '!=', 'Anulada')
            ->get();

        foreach ($ventas as $venta) {
            $monto = (float) $venta->pagos
                ->where('forma_pago', 'cuenta_corriente')
                ->sum('monto');
            if ($monto > 0) {
                $movimientos[] = [
                    'fecha' => $venta->fecha->format('d/m/Y'),
                    'orden' => $venta->fecha->format('Y-m-d') . '-' . $venta->id,
                    'concepto' => 'Venta #' . $venta->id,
                    'debe' => $monto,
                    'haber' => 0,
                ];
            }
        }

        foreach ($cliente->pagosCuentaCorriente()->latest()->get() as $pago) {
            $movimientos[] = [
                'fecha' => $pago->created_at->format('d/m/Y'),
                'orden' => $pago->created_at->format('Y-m-d H:i:s') . '-' . $pago->id,
                'concepto' => 'Pago recibido',
                'debe' => 0,
                'haber' => (float) $pago->monto,
                'medio' => $pago->medio,
                'observacion' => $pago->observacion,
            ];
        }

        return $this->ordenarYCalcular($movimientos);
    }

    public function resumenProveedor(Proveedor $proveedor): array
    {
        $movimientos = [];
        $compras = Compra::with('pagos')
            ->where('proveedor_id', $proveedor->id)
            ->where('estado', '!=', 'Anulada')
            ->get();

        foreach ($compras as $compra) {
            $monto = (float) $compra->pagos
                ->where('forma_pago', 'cuenta_corriente')
                ->sum('monto');
            if ($monto > 0) {
                $movimientos[] = [
                    'fecha' => $compra->fecha->format('d/m/Y'),
                    'orden' => $compra->fecha->format('Y-m-d') . '-' . $compra->id,
                    'concepto' => 'Compra #' . $compra->id,
                    'debe' => $monto,
                    'haber' => 0,
                ];
            }
        }

        foreach ($proveedor->pagosCuentaCorriente()->latest()->get() as $pago) {
            $movimientos[] = [
                'fecha' => $pago->created_at->format('d/m/Y'),
                'orden' => $pago->created_at->format('Y-m-d H:i:s') . '-' . $pago->id,
                'concepto' => 'Pago realizado',
                'debe' => 0,
                'haber' => (float) $pago->monto,
                'medio' => $pago->medio,
                'observacion' => $pago->observacion,
            ];
        }

        return $this->ordenarYCalcular($movimientos);
    }

    public function registrarPagoCliente(Cliente $cliente, float $monto, string $medio, ?string $observacion): void
    {
        DB::transaction(function () use ($cliente, $monto, $medio, $observacion) {
            $cliente = Cliente::lockForUpdate()->findOrFail($cliente->id);
            $resumen = $this->resumenCliente($cliente);
            $saldo = $this->saldo($resumen);
            $caja = $this->cajaAbierta();

            $this->validarPago($saldo, $monto, $caja);

            $pago = $cliente->pagosCuentaCorriente()->create([
                'monto' => $monto,
                'medio' => $medio,
                'observacion' => $observacion,
            ]);

            $movimiento = MovimientoCaja::create([
                'caja_id' => $caja->id,
                'tipo' => 'ingreso',
                'concepto' => 'Pago cuenta corriente - ' . $cliente->nombre . ' ' . $cliente->apellido,
                'medio' => $medio,
                'monto' => $monto,
                'observacion' => $observacion,
            ]);

            $pago->update(['movimiento_caja_id' => $movimiento->id]);
        });
    }

    public function registrarPagoProveedor(Proveedor $proveedor, float $monto, string $medio, ?string $observacion): void
    {
        DB::transaction(function () use ($proveedor, $monto, $medio, $observacion) {
            $proveedor = Proveedor::lockForUpdate()->findOrFail($proveedor->id);
            $resumen = $this->resumenProveedor($proveedor);
            $saldo = $this->saldo($resumen);
            $caja = $this->cajaAbierta();

            $this->validarPago($saldo, $monto, $caja);

            $pago = $proveedor->pagosCuentaCorriente()->create([
                'monto' => $monto,
                'medio' => $medio,
                'observacion' => $observacion,
            ]);

            $movimiento = MovimientoCaja::create([
                'caja_id' => $caja->id,
                'tipo' => 'egreso',
                'concepto' => 'Pago cuenta corriente - ' . $proveedor->empresa,
                'medio' => $medio,
                'monto' => $monto,
                'observacion' => $observacion,
            ]);

            $pago->update(['movimiento_caja_id' => $movimiento->id]);
        });
    }

    public function saldo(array $resumen): float
    {
        return round((float) ($resumen['saldo'] ?? 0), 2);
    }

    public function saldoCliente(Cliente $cliente): float
    {
        return $this->saldo($this->resumenCliente($cliente));
    }

    public function saldoProveedor(Proveedor $proveedor): float
    {
        return $this->saldo($this->resumenProveedor($proveedor));
    }

    private function ordenarYCalcular(array $movimientos): array
    {
        usort($movimientos, fn ($a, $b) => strcmp($a['orden'], $b['orden']));
        $saldo = 0;
        foreach ($movimientos as &$movimiento) {
            $saldo = round($saldo + $movimiento['debe'] - $movimiento['haber'], 2);
            $movimiento['saldo'] = $saldo;
        }
        unset($movimiento);

        usort($movimientos, fn ($a, $b) => strcmp($b['orden'], $a['orden']));
        return ['saldo' => $saldo, 'movimientos' => $movimientos];
    }

    private function cajaAbierta(): Caja
    {
        $caja = Caja::whereDate('fecha', now()->toDateString())
            ->where('estado', 'abierta')
            ->lockForUpdate()
            ->first();

        if (!$caja) {
            throw ValidationException::withMessages([
                'monto' => 'No se puede registrar el pago porque no hay una caja abierta para la jornada actual.',
            ]);
        }

        return $caja;
    }

    private function validarPago(float $saldo, float $monto, Caja $caja): void
    {
        if ($monto <= 0) {
            throw ValidationException::withMessages(['monto' => 'El monto debe ser mayor que 0.']);
        }
        if ($monto > $saldo) {
            throw ValidationException::withMessages(['monto' => 'El monto no puede superar el saldo pendiente.']);
        }
    }
}
