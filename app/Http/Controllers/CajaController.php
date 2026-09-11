<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\CierreCaja;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CajaController extends Controller
{
    public function index()
    {
        $fechaActual = now()->toDateString();
        $caja = Caja::whereDate('fecha', $fechaActual)->first();
        $movimientos = $caja ? $caja->movimientos()->latest()->get() : collect();
        $ingresos = $movimientos->where('tipo', 'ingreso')->sum('monto');
        $egresos = $movimientos->where('tipo', 'egreso')->sum('monto');
        $saldoActual = (float) ($caja?->saldo_inicial ?? 0) + $ingresos - $egresos;
        $cierres = CierreCaja::with(['caja.movimientos' => function ($query) {
            $query->latest();
        }])->latest('fecha')->latest('id')->get();

        return view('caja', compact('caja', 'fechaActual', 'movimientos', 'ingresos', 'egresos', 'saldoActual', 'cierres'));
    }

    public function abrir(Request $request)
    {
        $validated = $request->validate([
            'saldo_inicial' => 'required|numeric|min:0',
        ]);

        $fechaActual = now()->toDateString();

        if (Caja::whereDate('fecha', $fechaActual)->where('estado', 'abierta')->exists()) {
            return redirect()->route('caja.index')
                ->with('error', 'Ya existe una caja abierta para la jornada de hoy.');
        }

        if (Caja::whereDate('fecha', $fechaActual)->exists()) {
            return redirect()->route('caja.index')
                ->with('error', 'Ya existe una caja registrada para la jornada de hoy.');
        }

        try {
            Caja::create([
                'fecha' => $fechaActual,
                'saldo_inicial' => $validated['saldo_inicial'],
                'estado' => 'abierta',
            ]);
        } catch (QueryException $exception) {
            return redirect()->route('caja.index')
                ->with('error', 'No se pudo abrir otra caja para la jornada de hoy.');
        }

        return redirect()->route('caja.index')
            ->with('success', 'La caja fue abierta correctamente.');
    }

    public function cerrar(Request $request)
    {
        $fechaActual = now()->toDateString();
        $caja = Caja::whereDate('fecha', $fechaActual)->first();

        if (!$caja || $caja->estado !== 'abierta') {
            return redirect()->route('caja.index')
                ->with('error', 'No hay una caja abierta para la jornada actual.');
        }

        $validated = $request->validate([
            'dinero_contado' => 'required|numeric|min:0',
        ]);

        try {
            DB::transaction(function () use ($caja, $validated) {
                if ($caja->cierre()->exists()) {
                    throw new \RuntimeException('La caja ya tiene un cierre registrado.');
                }

                $ingresos = $caja->movimientos()->where('tipo', 'ingreso')->sum('monto');
                $egresos = $caja->movimientos()->where('tipo', 'egreso')->sum('monto');
                $saldoInicial = (float) $caja->saldo_inicial;
                $saldoEsperado = round($saldoInicial + $ingresos - $egresos, 2);
                $dineroContado = round((float) $validated['dinero_contado'], 2);
                $diferencia = round($dineroContado - $saldoEsperado, 2);

                $caja->cierre()->create([
                    'fecha' => $caja->fecha,
                    'saldo_inicial' => $saldoInicial,
                    'total_ingresos' => $ingresos,
                    'total_egresos' => $egresos,
                    'saldo_esperado' => $saldoEsperado,
                    'dinero_contado' => $dineroContado,
                    'diferencia' => $diferencia,
                    'estado' => 'cerrada',
                ]);

                $caja->update([
                    'ingresos' => $ingresos,
                    'egresos' => $egresos,
                    'saldo_final' => $saldoEsperado,
                    'dinero_contado' => $dineroContado,
                    'diferencia' => $diferencia,
                    'fecha_cierre' => now(),
                    'estado' => 'cerrada',
                ]);
            });
        } catch (\RuntimeException $exception) {
            return redirect()->route('caja.index')->with('error', $exception->getMessage());
        }

        return redirect()->route('caja.index')
            ->with('success', 'La caja fue cerrada correctamente.');
    }

    public function guardarMovimiento(Request $request)
    {
        $fechaActual = now()->toDateString();
        $caja = Caja::whereDate('fecha', $fechaActual)->first();

        if (!$caja || $caja->estado !== 'abierta') {
            return redirect()->route('caja.index')
                ->with('error', 'Primero debe abrir la caja para registrar movimientos.');
        }

        $validated = $request->validate([
            'tipo' => 'required|in:ingreso,egreso',
            'concepto' => 'required|string',
            'medio' => 'required|in:efectivo,tarjeta,transferencia',
            'monto' => 'required|numeric|gt:0',
            'observacion' => 'nullable|string',
        ]);

        $caja->movimientos()->create($validated);

        return redirect()->route('caja.index')
            ->with('success', 'El movimiento fue registrado correctamente.');
    }
}
