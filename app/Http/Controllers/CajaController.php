<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\CierreCaja;
use App\Models\MovimientoCaja;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CajaController extends Controller
{
    public function index(Request $request)
    {
        $fechaActual = now()->toDateString();

        $caja = Caja::whereDate('fecha', $fechaActual)->first();

        /*
        |--------------------------------------------------------------------------
        | RESUMEN DE CAJA
        |--------------------------------------------------------------------------
        | Estos valores se calculan con TODOS los movimientos de la caja,
        | independientemente de la página o los filtros.
        */

        $ingresos = $caja
            ? (float) $caja->movimientos()
                ->where('tipo', 'ingreso')
                ->sum('monto')
            : 0;

        $egresos = $caja
            ? (float) $caja->movimientos()
                ->where('tipo', 'egreso')
                ->sum('monto')
            : 0;

        $saldoActual = (float) ($caja?->saldo_inicial ?? 0)
            + $ingresos
            - $egresos;


        /*
        |--------------------------------------------------------------------------
        | FILTROS DE MOVIMIENTOS
        |--------------------------------------------------------------------------
        */

        $buscarMovimiento = trim(
            $request->input('buscar_movimiento', '')
        );

        $tipoMovimiento = $request->input(
            'tipo_movimiento',
            'Todos'
        );

        $medioMovimiento = $request->input(
            'medio_movimiento',
            'Todos'
        );

        // Validamos tipo
        if (!in_array(
            $tipoMovimiento,
            ['Todos', 'ingreso', 'egreso'],
            true
        )) {
            $tipoMovimiento = 'Todos';
        }

        // Validamos medio
        if (!in_array(
            $medioMovimiento,
            ['Todos', 'efectivo', 'tarjeta', 'transferencia'],
            true
        )) {
            $medioMovimiento = 'Todos';
        }


        /*
        |--------------------------------------------------------------------------
        | MOVIMIENTOS PAGINADOS
        |--------------------------------------------------------------------------
        */

        if ($caja) {
            $consultaMovimientos = $caja->movimientos();
        } else {
            $consultaMovimientos = MovimientoCaja::query()
                ->whereRaw('1 = 0');
        }

        // Buscar por concepto o medio
        if ($buscarMovimiento !== '') {
            $consultaMovimientos->where(function ($query) use ($buscarMovimiento) {
                $query->where(
                    'concepto',
                    'like',
                    "%{$buscarMovimiento}%"
                )
                ->orWhere(
                    'medio',
                    'like',
                    "%{$buscarMovimiento}%"
                );
            });
        }

        // Filtrar por ingreso/egreso
        if ($tipoMovimiento !== 'Todos') {
            $consultaMovimientos->where(
                'tipo',
                $tipoMovimiento
            );
        }

        // Filtrar por medio de pago
        if ($medioMovimiento !== 'Todos') {
            $consultaMovimientos->where(
                'medio',
                $medioMovimiento
            );
        }

        /*
        |--------------------------------------------------------------------------
        | IMPORTANTE:
        | paginate() devuelve LengthAwarePaginator.
        | Eso permite usar firstItem(), lastItem(), getCollection(), etc.
        |--------------------------------------------------------------------------
        */

        $movimientos = $consultaMovimientos
            ->latest()
            ->paginate(
                6,
                ['*'],
                'movimientos_page'
            )
            ->appends(
                $request->except('movimientos_page')
            );


        /*
        |--------------------------------------------------------------------------
        | CIERRES PAGINADOS
        |--------------------------------------------------------------------------
        */

        $cierres = CierreCaja::with([
            'caja.movimientos' => function ($query) {
                $query->latest();
            }
        ])
            ->latest('fecha')
            ->latest('id')
            ->paginate(
                6,
                ['*'],
                'cierres_page'
            )
            ->appends(
                $request->except('cierres_page')
            );


        /*
        |--------------------------------------------------------------------------
        | PETICIÓN AJAX
        |--------------------------------------------------------------------------
        */

        if ($request->ajax()) {
            return view(
                'partials.caja-ajax',
                compact(
                    'movimientos',
                    'cierres'
                )
            );
        }


        /*
        |--------------------------------------------------------------------------
        | VISTA NORMAL
        |--------------------------------------------------------------------------
        */

        return view(
            'caja',
            compact(
                'caja',
                'fechaActual',
                'movimientos',
                'ingresos',
                'egresos',
                'saldoActual',
                'cierres',
                'buscarMovimiento',
                'tipoMovimiento',
                'medioMovimiento'
            )
        );
    }


    public function abrir(Request $request)
    {
        $validated = $request->validate([
            'saldo_inicial' => 'required|numeric|min:0',
        ]);

        $fechaActual = now()->toDateString();

        if (
            Caja::whereDate('fecha', $fechaActual)
                ->where('estado', 'abierta')
                ->exists()
        ) {
            return redirect()->route('caja.index')
                ->with(
                    'error',
                    'Ya existe una caja abierta para la jornada de hoy.'
                );
        }

        if (
            Caja::whereDate('fecha', $fechaActual)
                ->exists()
        ) {
            return redirect()->route('caja.index')
                ->with(
                    'error',
                    'Ya existe una caja registrada para la jornada de hoy.'
                );
        }

        try {
            Caja::create([
                'fecha' => $fechaActual,
                'saldo_inicial' => $validated['saldo_inicial'],
                'estado' => 'abierta',
            ]);
        } catch (QueryException $exception) {
            return redirect()->route('caja.index')
                ->with(
                    'error',
                    'No se pudo abrir otra caja para la jornada de hoy.'
                );
        }

        return redirect()->route('caja.index')
            ->with(
                'success',
                'La caja fue abierta correctamente.'
            );
    }


    public function cerrar(Request $request)
    {
        $fechaActual = now()->toDateString();

        $caja = Caja::whereDate(
            'fecha',
            $fechaActual
        )->first();

        if (
            !$caja ||
            $caja->estado !== 'abierta'
        ) {
            return redirect()->route('caja.index')
                ->with(
                    'error',
                    'No hay una caja abierta para la jornada actual.'
                );
        }

        $validated = $request->validate([
            'dinero_contado' => 'required|numeric|min:0',
        ]);

        try {
            DB::transaction(function () use (
                $caja,
                $validated
            ) {
                if ($caja->cierre()->exists()) {
                    throw new \RuntimeException(
                        'La caja ya tiene un cierre registrado.'
                    );
                }

                $ingresos = $caja->movimientos()
                    ->where('tipo', 'ingreso')
                    ->sum('monto');

                $egresos = $caja->movimientos()
                    ->where('tipo', 'egreso')
                    ->sum('monto');

                $saldoInicial = (float) $caja->saldo_inicial;

                $saldoEsperado = round(
                    $saldoInicial
                    + $ingresos
                    - $egresos,
                    2
                );

                $dineroContado = round(
                    (float) $validated['dinero_contado'],
                    2
                );

                $diferencia = round(
                    $dineroContado
                    - $saldoEsperado,
                    2
                );

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
            return redirect()->route('caja.index')
                ->with(
                    'error',
                    $exception->getMessage()
                );
        }

        return redirect()->route('caja.index')
            ->with(
                'success',
                'La caja fue cerrada correctamente.'
            );
    }


    public function guardarMovimiento(Request $request)
    {
        $fechaActual = now()->toDateString();

        $caja = Caja::whereDate(
            'fecha',
            $fechaActual
        )->first();

        if (
            !$caja ||
            $caja->estado !== 'abierta'
        ) {
            return redirect()->route('caja.index')
                ->with(
                    'error',
                    'Primero debe abrir la caja para registrar movimientos.'
                );
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
            ->with(
                'success',
                'El movimiento fue registrado correctamente.'
            );
    }
}