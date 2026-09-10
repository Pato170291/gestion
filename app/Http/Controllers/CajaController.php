<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class CajaController extends Controller
{
    public function index()
    {
        $fechaActual = now()->toDateString();
        $caja = Caja::whereDate('fecha', $fechaActual)->first();

        return view('caja', compact('caja', 'fechaActual'));
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

        $ingresos = 0.00;
        $egresos = 0.00;
        $saldoFinal = round((float) $caja->saldo_inicial + $ingresos - $egresos, 2);
        $dineroContado = round((float) $validated['dinero_contado'], 2);
        $diferencia = round($dineroContado - $saldoFinal, 2);

        $caja->update([
            'ingresos' => $ingresos,
            'egresos' => $egresos,
            'saldo_final' => $saldoFinal,
            'dinero_contado' => $dineroContado,
            'diferencia' => $diferencia,
            'fecha_cierre' => now(),
            'estado' => 'cerrada',
        ]);

        return redirect()->route('caja.index')
            ->with('success', 'La caja fue cerrada correctamente.');
    }
}
