<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Caja;
use App\Models\MovimientoCaja;
use App\Models\MovimientoStock;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\VentaPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\CuentaCorrienteService;

class VentaController extends Controller
{
    public function index(Request $request)
    {
        $clientes = Cliente::orderBy('nombre')
            ->orderBy('apellido')
            ->get();

        $buscar = trim($request->input('buscar', ''));

        $consulta = Venta::with([
            'cliente',
            'detalles',
            'pagos'
        ])
            ->orderByDesc('updated_at')
            ->orderByDesc('id');

        if ($buscar !== '') {
            $consulta->where(function ($query) use ($buscar) {
                $query->where('fecha', 'like', "%{$buscar}%")
                    ->orWhere('estado', 'like', "%{$buscar}%")
                    ->orWhere('total', 'like', "%{$buscar}%")
                    ->orWhereHas('cliente', function ($clienteQuery) use ($buscar) {
                        $clienteQuery
                            ->where('nombre', 'like', "%{$buscar}%")
                            ->orWhere('apellido', 'like', "%{$buscar}%");
                    });
            });
        }

        $ventas = $consulta
            ->paginate(6)
            ->appends([
                'buscar' => $buscar
            ]);

        if ($request->ajax()) {
            return view('partials.ventas-ajax', compact('ventas'));
        }

        return view('ventas', compact(
            'clientes',
            'ventas'
        ));
    }

    public function crear(
        CuentaCorrienteService $cuentaCorriente
    ) {
        $clientes = Cliente::orderBy('nombre')
            ->orderBy('apellido')
            ->get();

        foreach ($clientes as $cliente) {
            $cliente->saldo_a_favor =
                $cuentaCorriente->saldoAFavorCliente($cliente);
        }

        $productos = Producto::orderBy('nombre')->get();

        return view(
            'crear-venta',
            compact(
                'clientes',
                'productos'
            )
        );
    }

    public function store(
        Request $request,
        CuentaCorrienteService $cuentaCorriente
    ) {
        $validated = $request->validate([
            'fecha' => 'required|date',
            'cliente_id' => 'nullable|exists:clientes,id',

            'producto_id' => 'required',
            'cantidad' => 'required|integer|min:1',
            'precio' => 'required|numeric|min:0',

            'formas_pago' => 'required|array|min:1',
            'formas_pago.*' => [
                'required',
                'in:efectivo,tarjeta,transferencia,cuenta_corriente'
            ],

            'montos_pago' => 'nullable|array',
            'montos_pago.*' => 'nullable|numeric|min:0',
        ], [
            'formas_pago.required' =>
                'Seleccione al menos una forma de pago.',

            'formas_pago.min' =>
                'Seleccione al menos una forma de pago.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | TOTAL DE LA VENTA
        |--------------------------------------------------------------------------
        */

        $cantidad = (int) $validated['cantidad'];

        $precio = round(
            (float) $validated['precio'],
            2
        );

        $total = round(
            $cantidad * $precio,
            2
        );

        /*
        |--------------------------------------------------------------------------
        | SALDO A FAVOR
        |--------------------------------------------------------------------------
        */

        $saldoAFavorDisponible = 0;

        if (!empty($validated['cliente_id'])) {
            $cliente = Cliente::findOrFail(
                $validated['cliente_id']
            );

            $saldoAFavorDisponible = round(
                $cuentaCorriente->saldoAFavorCliente($cliente),
                2
            );
        }

        $saldoAFavorUsado = min(
            $saldoAFavorDisponible,
            $total
        );

        $restante = round(
            $total - $saldoAFavorUsado,
            2
        );

        /*
        |--------------------------------------------------------------------------
        | FORMAS DE PAGO
        |--------------------------------------------------------------------------
        */

        $formasPago = $validated['formas_pago'];

        $montos = [];

        if ($restante > 0) {

            if (count($formasPago) === 1) {

                $montos[$formasPago[0]] = $restante;

            } else {

                foreach ($formasPago as $formaPago) {

                    if (!array_key_exists(
                        $formaPago,
                        $validated['montos_pago'] ?? []
                    )) {
                        return back()
                            ->withErrors([
                                'montos_pago' =>
                                    'Debe indicar el monto de cada forma de pago.'
                            ])
                            ->withInput();
                    }

                    $montos[$formaPago] = round(
                        (float) $validated['montos_pago'][$formaPago],
                        2
                    );
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDAR SUMA DE PAGOS
        |--------------------------------------------------------------------------
        */

        $totalPagadoSinSaldoFavor = round(
            array_sum($montos),
            2
        );

        if ($totalPagadoSinSaldoFavor > $restante) {
            return back()
                ->withErrors([
                    'montos_pago' =>
                        'La suma de los pagos no puede superar el importe restante de la venta.'
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | TOTAL PAGADO
        |--------------------------------------------------------------------------
        */

        $totalPagado = round(
            $saldoAFavorUsado +
            $totalPagadoSinSaldoFavor,
            2
        );

        /*
        |--------------------------------------------------------------------------
        | ESTADO
        |--------------------------------------------------------------------------
        */

        $estado = $totalPagado >= $total
            ? 'Pagado'
            : (
                $totalPagado > 0
                    ? 'Parcial'
                    : 'Pendiente'
            );

        /*
        |--------------------------------------------------------------------------
        | PRODUCTO Y STOCK
        |--------------------------------------------------------------------------
        */

        $productoId =
            $validated['producto_id'] === 'otro'
                ? null
                : $validated['producto_id'];

        $productoNombre = 'Otro';

        if ($productoId !== null) {

            $producto = Producto::findOrFail(
                $productoId
            );

            $productoNombre = $producto->nombre;

            $stockDisponible =
                $producto->calcularStockActual();

            if ($cantidad > $stockDisponible) {

                return back()
                    ->withErrors([
                        'cantidad' =>
                            "No hay stock suficiente. Stock disponible: {$stockDisponible}."
                    ])
                    ->withInput();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | CAJA
        |--------------------------------------------------------------------------
        |
        | Solamente efectivo, tarjeta y transferencia
        | necesitan pasar por Caja.
        |
        | Cuenta corriente NO entra en Caja.
        |
        | Saldo a favor NO entra en Caja.
        |--------------------------------------------------------------------------
        */

        $montosCaja = $this->montosCaja(
            $montos
        );

        $caja = $this->cajaAbiertaParaPagos(
            $montosCaja
        );

        if ($montosCaja && !$caja) {

            return redirect('/ventas')
                ->with(
                    'error',
                    'No se puede realizar la venta porque la caja está cerrada. Para registrar un pago en efectivo, tarjeta o transferencia debe haber una caja abierta.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | GUARDAR VENTA
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use (
            $validated,
            $total,
            $totalPagado,
            $estado,
            $productoId,
            $productoNombre,
            $cantidad,
            $precio,
            $montos,
            $montosCaja,
            $caja,
            $saldoAFavorUsado
        ) {

            $venta = Venta::create([
                'fecha' =>
                    $validated['fecha'],

                'cliente_id' =>
                    $validated['cliente_id'] ?? null,

                'total' =>
                    $total,

                'total_pagado' =>
                    $totalPagado,

                'estado' =>
                    $estado,
            ]);

            /*
            |--------------------------------------------------------------------------
            | DETALLE
            |--------------------------------------------------------------------------
            */

            VentaDetalle::create([
                'venta_id' =>
                    $venta->id,

                'producto_id' =>
                    $productoId,

                'producto_nombre' =>
                    $productoNombre,

                'cantidad' =>
                    $cantidad,

                'precio' =>
                    $precio,

                'subtotal' =>
                    $total,
            ]);

            /*
            |--------------------------------------------------------------------------
            | SALDO A FAVOR UTILIZADO
            |--------------------------------------------------------------------------
            */

            if ($saldoAFavorUsado > 0) {

                VentaPago::create([
                    'venta_id' =>
                        $venta->id,

                    'forma_pago' =>
                        'saldo_a_favor',

                    'monto' =>
                        $saldoAFavorUsado,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | FORMAS DE PAGO
            |--------------------------------------------------------------------------
            */

            foreach ($montos as $formaPago => $monto) {

                if ($monto <= 0) {
                    continue;
                }

                VentaPago::create([
                    'venta_id' =>
                        $venta->id,

                    'forma_pago' =>
                        $formaPago,

                    'monto' =>
                        $monto,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | SALIDA DE STOCK
            |--------------------------------------------------------------------------
            */

            if ($productoId !== null) {

                MovimientoStock::create([
                    'producto_id' =>
                        $productoId,

                    'venta_id' =>
                        $venta->id,

                    'tipo' =>
                        'salida',

                    'cantidad' =>
                        $cantidad,

                    'motivo' =>
                        'Venta #' . $venta->id,

                    'fecha' =>
                        $venta->fecha,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | MOVIMIENTOS DE CAJA
            |--------------------------------------------------------------------------
            */

            $this->crearMovimientosIngreso(
                $venta,
                $caja,
                $montosCaja
            );
        });

        return redirect('/ventas')
            ->with(
                'success',
                'Venta guardada correctamente.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | MOSTRAR
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        return view(
            'detalle-venta',
            compact('id')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | EDITAR
    |--------------------------------------------------------------------------
    */

    public function edit($id)
    {
        $venta = Venta::with([
            'detalles',
            'pagos'
        ])->findOrFail($id);

        if ($venta->estado === 'Anulada') {

            return redirect('/ventas')
                ->with(
                    'error',
                    'Una venta anulada no se puede editar.'
                );
        }

        $clientes = Cliente::orderBy('nombre')
            ->orderBy('apellido')
            ->get();

        $productos = Producto::orderBy('nombre')
            ->get();

        return view(
            'editar-venta',
            compact(
                'venta',
                'clientes',
                'productos'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        $id,
        CuentaCorrienteService $cuentaCorriente
    ) {
        $venta = Venta::with([
            'detalles',
            'pagos'
        ])->findOrFail($id);

        if ($venta->estado === 'Anulada') {

            return redirect('/ventas')
                ->with(
                    'error',
                    'Una venta anulada no se puede editar.'
                );
        }

        $validated = $request->validate([
            'fecha' => 'required|date',
            'cliente_id' => 'nullable|exists:clientes,id',

            'producto_id' => 'required',
            'cantidad' => 'required|integer|min:1',
            'precio' => 'required|numeric|min:0',

            'formas_pago' => 'required|array|min:1',
            'formas_pago.*' => [
                'required',
                'in:efectivo,tarjeta,transferencia,cuenta_corriente'
            ],

            'montos_pago' => 'nullable|array',
            'montos_pago.*' => 'nullable|numeric|min:0',
        ], [
            'formas_pago.required' =>
                'Seleccione al menos una forma de pago.',

            'formas_pago.min' =>
                'Seleccione al menos una forma de pago.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | TOTAL
        |--------------------------------------------------------------------------
        */

        $cantidad = (int) $validated['cantidad'];

        $precio = round(
            (float) $validated['precio'],
            2
        );

        $total = round(
            $cantidad * $precio,
            2
        );

        /*
        |--------------------------------------------------------------------------
        | RECUPERAR SALDO A FAVOR USADO POR ESTA VENTA
        |--------------------------------------------------------------------------
        */

        $saldoAFavorAnterior = (float) $venta->pagos
            ->where(
                'forma_pago',
                'saldo_a_favor'
            )
            ->sum('monto');

        $saldoAFavorDisponible = 0;

        if (!empty($validated['cliente_id'])) {

            $cliente = Cliente::findOrFail(
                $validated['cliente_id']
            );

            $saldoAFavorDisponible = round(
                $cuentaCorriente->saldoAFavorCliente($cliente),
                2
            );

            if (
                (int) $validated['cliente_id']
                ===
                (int) $venta->cliente_id
            ) {

                $saldoAFavorDisponible = round(
                    $saldoAFavorDisponible +
                    $saldoAFavorAnterior,
                    2
                );
            }
        }

        $saldoAFavorUsado = min(
            $saldoAFavorDisponible,
            $total
        );

        $restante = round(
            $total - $saldoAFavorUsado,
            2
        );

        /*
        |--------------------------------------------------------------------------
        | FORMAS DE PAGO
        |--------------------------------------------------------------------------
        */

        $formasPago =
            $validated['formas_pago'];

        $montos = [];

        if ($restante > 0) {

            if (count($formasPago) === 1) {

                $montos[$formasPago[0]] =
                    $restante;

            } else {

                foreach ($formasPago as $formaPago) {

                    if (!array_key_exists(
                        $formaPago,
                        $validated['montos_pago'] ?? []
                    )) {

                        return back()
                            ->withErrors([
                                'montos_pago' =>
                                    'Debe indicar el monto de cada forma de pago.'
                            ])
                            ->withInput();
                    }

                    $montos[$formaPago] =
                        round(
                            (float) $validated['montos_pago'][$formaPago],
                            2
                        );
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDAR PAGOS
        |--------------------------------------------------------------------------
        */

        $totalPagadoSinSaldoFavor =
            round(
                array_sum($montos),
                2
            );

        if (
            $totalPagadoSinSaldoFavor
            >
            $restante
        ) {

            return back()
                ->withErrors([
                    'montos_pago' =>
                        'La suma de los pagos no puede superar el importe restante de la venta.'
                ])
                ->withInput();
        }

        $totalPagado =
            round(
                $saldoAFavorUsado +
                $totalPagadoSinSaldoFavor,
                2
            );

        /*
        |--------------------------------------------------------------------------
        | ESTADO
        |--------------------------------------------------------------------------
        */

        $estado = $totalPagado >= $total
            ? 'Pagado'
            : (
                $totalPagado > 0
                    ? 'Parcial'
                    : 'Pendiente'
            );

        /*
        |--------------------------------------------------------------------------
        | PRODUCTO
        |--------------------------------------------------------------------------
        */

        $productoId =
            $validated['producto_id'] === 'otro'
                ? null
                : $validated['producto_id'];

        $productoNombre = 'Otro';

        if ($productoId !== null) {

            $producto = Producto::findOrFail(
                $productoId
            );

            $productoNombre =
                $producto->nombre;
        }

        /*
        |--------------------------------------------------------------------------
        | CAJA
        |--------------------------------------------------------------------------
        */

        $montosCaja =
            $this->montosCaja($montos);

        $caja =
            $this->cajaAbiertaParaPagos(
                $montosCaja
            );

        if (
            $montosCaja
            &&
            !$caja
        ) {

            return redirect('/ventas')
                ->with(
                    'error',
                    'No se puede realizar la venta porque la caja está cerrada. Para registrar un pago en efectivo, tarjeta o transferencia debe haber una caja abierta.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | MOVIMIENTOS DE CAJA ANTERIORES
        |--------------------------------------------------------------------------
        */

        $movimientosExistentes =
            MovimientoCaja::with('caja')
                ->where(
                    'venta_id',
                    $venta->id
                )
                ->get();

        if (
            $movimientosExistentes->contains(
                function ($movimiento) {
                    return $movimiento->caja
                        &&
                        $movimiento->caja->estado !== 'abierta';
                }
            )
        ) {

            return redirect('/ventas')
                ->with(
                    'error',
                    'No se puede editar una venta cuyos movimientos pertenecen a una caja cerrada.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | ACTUALIZAR
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use (
            $venta,
            $validated,
            $total,
            $totalPagado,
            $estado,
            $productoId,
            $productoNombre,
            $cantidad,
            $precio,
            $montos,
            $montosCaja,
            $caja,
            $saldoAFavorUsado
        ) {

            $venta->update([
                'fecha' =>
                    $validated['fecha'],

                'cliente_id' =>
                    $validated['cliente_id'] ?? null,

                'total' =>
                    $total,

                'total_pagado' =>
                    $totalPagado,

                'estado' =>
                    $estado,
            ]);

            /*
            |--------------------------------------------------------------------------
            | DETALLE
            |--------------------------------------------------------------------------
            */

            $venta->detalles()->delete();

            $venta->detalles()->create([
                'producto_id' =>
                    $productoId,

                'producto_nombre' =>
                    $productoNombre,

                'cantidad' =>
                    $cantidad,

                'precio' =>
                    $precio,

                'subtotal' =>
                    $total,
            ]);

            /*
            |--------------------------------------------------------------------------
            | PAGOS
            |--------------------------------------------------------------------------
            */

            $venta->pagos()->delete();

            /*
            |--------------------------------------------------------------------------
            | MOVIMIENTOS DE CAJA ANTERIORES
            |--------------------------------------------------------------------------
            */

            MovimientoCaja::where(
                'venta_id',
                $venta->id
            )->delete();

            /*
            |--------------------------------------------------------------------------
            | SALDO A FAVOR
            |--------------------------------------------------------------------------
            */

            if ($saldoAFavorUsado > 0) {

                $venta->pagos()->create([
                    'forma_pago' =>
                        'saldo_a_favor',

                    'monto' =>
                        $saldoAFavorUsado,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | NUEVOS PAGOS
            |--------------------------------------------------------------------------
            */

            foreach ($montos as $formaPago => $monto) {

                if ($monto <= 0) {
                    continue;
                }

                $venta->pagos()->create([
                    'forma_pago' =>
                        $formaPago,

                    'monto' =>
                        $monto,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | MOVIMIENTOS DE CAJA
            |--------------------------------------------------------------------------
            */

            $this->crearMovimientosIngreso(
                $venta,
                $caja,
                $montosCaja
            );
        });

        return redirect('/ventas')
            ->with(
                'success',
                'Venta actualizada correctamente.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | ANULAR
    |--------------------------------------------------------------------------
    */

    public function anular(
        Request $request,
        $id
    ) {
        $venta = Venta::findOrFail($id);

        if ($venta->estado === 'Anulada') {

            return redirect('/ventas')
                ->with(
                    'error',
                    'La venta ya está anulada.'
                );
        }

        $validated = $request->validate([
            'motivo_anulacion' =>
                'nullable|string',
        ]);

        $montosPagados = $venta->pagos()
            ->pluck(
                'monto',
                'forma_pago'
            )
            ->all();

        /*
        |--------------------------------------------------------------------------
        | CAJA
        |--------------------------------------------------------------------------
        |
        | Solamente se revierten los pagos reales:
        | efectivo, tarjeta y transferencia.
        |
        | Cuenta corriente y saldo a favor no generan
        | movimientos de Caja.
        |--------------------------------------------------------------------------
        */

        $montosCaja =
            $this->montosCaja(
                $montosPagados
            );

        $caja =
            $this->cajaAbiertaParaPagos(
                $montosCaja
            );

        if (
            $montosCaja
            &&
            !$caja
        ) {

            return redirect('/ventas')
                ->with(
                    'error',
                    'Debe abrir la caja antes de anular una venta con pagos registrados.'
                );
        }

        try {

            DB::transaction(function () use (
                $id,
                $validated,
                $montosCaja,
                $caja
            ) {

                $venta = Venta::lockForUpdate()
                    ->findOrFail($id);

                if (
                    $venta->estado === 'Anulada'
                ) {

                    throw new \RuntimeException(
                        'La venta ya está anulada.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | VOLVER A COMPROBAR CAJA
                |--------------------------------------------------------------------------
                */

                if ($caja) {

                    $caja = Caja::whereKey($caja->id)
                        ->where('estado', 'abierta')
                        ->lockForUpdate()
                        ->first();

                    if (!$caja) {

                        throw new \RuntimeException(
                            'No se puede anular esta venta porque la caja ya no está abierta para registrar la reversión.'
                        );
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | ANULAR VENTA
                |--------------------------------------------------------------------------
                */

                $venta->update([
                    'estado' =>
                        'Anulada',

                    'motivo_anulacion' =>
                        $validated['motivo_anulacion'] ?? null,

                    'fecha_anulacion' =>
                        now(),
                ]);

                /*
                |--------------------------------------------------------------------------
                | DEVOLVER PAGOS A CAJA
                |--------------------------------------------------------------------------
                */

                if ($caja) {

                    foreach (
                        $montosCaja
                        as $medio => $monto
                    ) {

                        MovimientoCaja::create([
                            'caja_id' =>
                                $caja->id,

                            'venta_anulada_id' =>
                                $venta->id,

                            'tipo' =>
                                'egreso',

                            'concepto' =>
                                'Anulación Venta #' .
                                $venta->id,

                            'medio' =>
                                $medio,

                            'monto' =>
                                $monto,

                            'observacion' =>
                                $validated['motivo_anulacion'] ?? null,
                        ]);
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | SALDO A FAVOR
                |--------------------------------------------------------------------------
                |
                | No se genera movimiento de Caja.
                |
                | Al quedar la venta anulada,
                | CuentaCorrienteService deja de contarla,
                | por lo que el saldo vuelve a quedar disponible.
                |--------------------------------------------------------------------------
                */

                /*
                |--------------------------------------------------------------------------
                | REVERTIR STOCK
                |--------------------------------------------------------------------------
                |
                | La venta original generó una SALIDA.
                | Al anularla generamos una ENTRADA inversa.
                |
                | No borramos el movimiento original.
                |--------------------------------------------------------------------------
                */

                $this->revertirMovimientosStock($venta);
            });

        } catch (\RuntimeException $exception) {

            return redirect('/ventas')
                ->with(
                    'error',
                    $exception->getMessage()
                );
        }

        return redirect('/ventas')
            ->with(
                'success',
                'La venta fue anulada correctamente.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | AUXILIARES
    |--------------------------------------------------------------------------
    */

    private function montosCaja(
        array $montos
    ): array {

        return array_filter(
            $montos,
            function ($monto, $medio) {

                return $medio !== 'cuenta_corriente'
                    &&
                    $medio !== 'saldo_a_favor'
                    &&
                    (float) $monto > 0;
            },
            ARRAY_FILTER_USE_BOTH
        );
    }

    private function cajaAbiertaParaPagos(
        array $montosCaja
    ): ?Caja {

        if (!$montosCaja) {
            return null;
        }

        return Caja::whereDate(
                'fecha',
                now()->toDateString()
            )
            ->where(
                'estado',
                'abierta'
            )
            ->first();
    }

    private function crearMovimientosIngreso(
        Venta $venta,
        ?Caja $caja,
        array $montosCaja
    ): void {

        if (!$caja) {
            return;
        }

        foreach (
            $montosCaja
            as $medio => $monto
        ) {

            MovimientoCaja::create([
                'caja_id' =>
                    $caja->id,

                'venta_id' =>
                    $venta->id,

                'tipo' =>
                    'ingreso',

                'concepto' =>
                    'Venta #' .
                    $venta->id,

                'medio' =>
                    $medio,

                'monto' =>
                    $monto,
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | REVERTIR MOVIMIENTOS DE STOCK
    |--------------------------------------------------------------------------
    |
    | Cada salida de stock generada por la venta
    | se revierte con una entrada.
    |
    | El movimiento original no se elimina,
    | para conservar el historial.
    |--------------------------------------------------------------------------
    */

    private function revertirMovimientosStock(
        Venta $venta
    ): void {

        $salidas = MovimientoStock::where(
                'venta_id',
                $venta->id
            )
            ->where(
                'tipo',
                'salida'
            )
            ->lockForUpdate()
            ->get();

        /*
        |--------------------------------------------------------------------------
        | EVITAR DUPLICAR LA REVERSIÓN
        |--------------------------------------------------------------------------
        */

        $yaRevertido = MovimientoStock::where(
                'venta_id',
                $venta->id
            )
            ->where(
                'tipo',
                'entrada'
            )
            ->exists();

        if ($yaRevertido) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | CREAR ENTRADA INVERSA
        |--------------------------------------------------------------------------
        */

        foreach ($salidas as $salida) {

            MovimientoStock::create([
                'producto_id' =>
                    $salida->producto_id,

                'venta_id' =>
                    $venta->id,

                'tipo' =>
                    'entrada',

                'cantidad' =>
                    $salida->cantidad,

                'motivo' =>
                    'Anulación Venta #' .
                    $venta->id,

                'fecha' =>
                    now()->toDateString(),
            ]);
        }
    }
}