<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\CompraDetalle;
use App\Models\CompraPago;
use App\Models\Caja;
use App\Models\MovimientoCaja;
use App\Models\MovimientoStock;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Services\CuentaCorrienteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller
{
    public function index(Request $request)
    {
        $buscar = trim($request->input('buscar', ''));

        $consulta = Compra::with(['proveedor', 'detalles', 'pagos'])
            ->orderByDesc('updated_at')
            ->orderByDesc('id');

        if ($buscar !== '') {
            $consulta->where(function ($query) use ($buscar) {
                $query->where('fecha', 'like', "%{$buscar}%")
                    ->orWhere('estado', 'like', "%{$buscar}%")
                    ->orWhere('total', 'like', "%{$buscar}%")
                    ->orWhereHas('proveedor', function ($proveedorQuery) use ($buscar) {
                        $proveedorQuery->where('empresa', 'like', "%{$buscar}%");
                    });
            });
        }

        $compras = $consulta->paginate(6)->appends(['buscar' => $buscar]);

        if ($request->ajax()) {
            return view('partials.compras-ajax', compact('compras'));
        }

        return view('compras', compact('compras'));
    }

    public function crear(CuentaCorrienteService $cuentaCorrienteService)
    {
        $proveedores = Proveedor::where('activo', true)
            ->orderBy('empresa')
            ->get();

        $productos = Producto::orderBy('nombre')->get();

        /*
         * Agregamos a cada proveedor su saldo a favor disponible.
         * Ejemplo:
         * proveedor_id => saldo a favor
         */
        $saldosAFavor = [];

        foreach ($proveedores as $proveedor) {
            $saldosAFavor[$proveedor->id] =
                $cuentaCorrienteService->saldoAFavorProveedor($proveedor);
        }

        return view('crear-compra', compact(
            'proveedores',
            'productos',
            'saldosAFavor'
        ));
    }

    public function store(
        Request $request,
        CuentaCorrienteService $cuentaCorrienteService
    ) {
        $validated = $this->validarCompra($request);

        $datos = $this->prepararCompra(
            $validated,
            $cuentaCorrienteService
        );

        $montosCaja = $this->montosCaja($datos['montos']);

        $caja = $this->cajaAbiertaParaPagos($montosCaja);

        if ($montosCaja && !$caja) {
            return redirect('/compras')
                ->with(
                    'error',
                    'No se puede realizar la compra porque la caja está cerrada. Para registrar una compra con pago en efectivo, tarjeta o transferencia debe haber una caja abierta.'
                );
        }

        DB::transaction(function () use (
            $validated,
            $datos,
            $montosCaja,
            $caja
        ) {
            $compra = Compra::create([
                'fecha' => $validated['fecha'],
                'proveedor_id' => $validated['proveedor_id'] ?? null,
                'total' => $datos['total'],
                'total_pagado' => $datos['total_pagado'],
                'estado' => $datos['estado'],
            ]);

            $this->guardarDetalleYPagos($compra, $datos);

            $this->crearMovimientosEgreso(
                $compra,
                $caja,
                $montosCaja
            );

            $this->generarMovimientosStock(
                $compra,
                $datos
            );
        });

        return redirect('/compras')
            ->with('success', 'Compra guardada correctamente.');
    }

    public function show($id)
    {
        $compra = Compra::with([
            'proveedor',
            'detalles',
            'pagos'
        ])->findOrFail($id);

        return view('detalle-compra', compact('compra'));
    }

    public function edit($id)
    {
        $compra = Compra::with([
            'detalles',
            'pagos'
        ])->findOrFail($id);

        if ($compra->estado === 'Anulada') {
            return redirect('/compras')
                ->with(
                    'error',
                    'Una compra anulada no se puede editar.'
                );
        }

        $proveedores = Proveedor::where('activo', true)
            ->orderBy('empresa')
            ->get();

        $productos = Producto::orderBy('nombre')->get();

        return view(
            'editar-compra',
            compact(
                'compra',
                'proveedores',
                'productos'
            )
        );
    }

    public function update(
        Request $request,
        $id,
        CuentaCorrienteService $cuentaCorrienteService
    ) {
        $compra = Compra::with([
            'detalles',
            'pagos'
        ])->findOrFail($id);

        if ($compra->estado === 'Anulada') {
            return redirect('/compras')
                ->with(
                    'error',
                    'Una compra anulada no se puede editar.'
                );
        }

        $validated = $this->validarCompra($request);

        /*
         * Al editar una compra tenemos que devolver temporalmente
         * el saldo a favor que esa misma compra había utilizado.
         *
         * Ejemplo:
         *
         * Proveedor tenía $30.000 a favor.
         * Compra anterior utilizó $20.000.
         *
         * Al editar, esos $20.000 vuelven a estar disponibles
         * para calcular correctamente la nueva compra.
         */
        $saldoAFavorActual = 0;

        if ($compra->proveedor_id) {
            $saldoAFavorActual =
                (float) $compra->pagos
                    ->where('forma_pago', 'saldo_a_favor')
                    ->sum('monto');
        }

        $saldoAFavorProveedor = 0;

        if ($validated['proveedor_id'] ?? null) {
            $proveedor = Proveedor::findOrFail(
                $validated['proveedor_id']
            );

            $saldoAFavorProveedor =
                $cuentaCorrienteService->saldoAFavorProveedor($proveedor);
        }

        /*
         * Si seguimos usando el mismo proveedor, recuperamos
         * el crédito que utilizaba la compra anterior.
         */
        if (
            $compra->proveedor_id &&
            ($validated['proveedor_id'] ?? null) == $compra->proveedor_id
        ) {
            $saldoAFavorProveedor += $saldoAFavorActual;
        }

        $datos = $this->prepararCompra(
            $validated,
            $cuentaCorrienteService,
            $saldoAFavorProveedor
        );

        $montosCaja = $this->montosCaja($datos['montos']);

        $caja = $this->cajaAbiertaParaPagos($montosCaja);

        if ($montosCaja && !$caja) {
            return redirect('/compras')
                ->with(
                    'error',
                    'No se puede realizar la compra porque la caja está cerrada. Para registrar una compra con pago en efectivo, tarjeta o transferencia debe haber una caja abierta.'
                );
        }

        $movimientosExistentes = MovimientoCaja::with('caja')
            ->where('compra_id', $compra->id)
            ->get();

        if ($movimientosExistentes->contains(function ($movimiento) {
            return $movimiento->caja
                && $movimiento->caja->estado !== 'abierta';
        })) {
            return redirect('/compras')
                ->with(
                    'error',
                    'No se puede editar una compra cuyos movimientos pertenecen a una caja cerrada.'
                );
        }

        DB::transaction(function () use (
            $compra,
            $validated,
            $datos,
            $montosCaja,
            $caja
        ) {
            $compra->update([
                'fecha' => $validated['fecha'],
                'proveedor_id' => $validated['proveedor_id'] ?? null,
                'total' => $datos['total'],
                'total_pagado' => $datos['total_pagado'],
                'estado' => $datos['estado'],
            ]);

            $compra->detalles()->delete();

            $compra->pagos()->delete();

            MovimientoCaja::where(
                'compra_id',
                $compra->id
            )->delete();

            MovimientoStock::where(
                'compra_id',
                $compra->id
            )->delete();

            $this->guardarDetalleYPagos(
                $compra,
                $datos
            );

            $this->crearMovimientosEgreso(
                $compra,
                $caja,
                $montosCaja
            );

            $this->generarMovimientosStock(
                $compra,
                $datos
            );
        });

        return redirect('/compras')
            ->with(
                'success',
                'Compra actualizada correctamente.'
            );
    }

    public function anular(Request $request, $id)
    {
        $compra = Compra::findOrFail($id);

        if ($compra->estado === 'Anulada') {
            return redirect('/compras')
                ->with(
                    'error',
                    'La compra ya está anulada.'
                );
        }

        $validated = $request->validate([
            'motivo_anulacion' => 'nullable|string',
        ]);

        $movimientosOriginales = MovimientoCaja::where(
            'compra_id',
            $compra->id
        )->get();

        $caja = $movimientosOriginales->isNotEmpty()
            ? $this->cajaAbiertaParaReversion()
            : null;

        if ($movimientosOriginales->isNotEmpty() && !$caja) {
            return redirect('/compras')
                ->with(
                    'error',
                    'No se puede anular esta compra porque tiene movimientos de Caja asociados y actualmente no hay una caja abierta para registrar la reversión.'
                );
        }

        try {
            DB::transaction(function () use (
                $id,
                $validated,
                $movimientosOriginales,
                $caja
            ) {
                $compra = Compra::lockForUpdate()
                    ->findOrFail($id);

                if ($compra->estado === 'Anulada') {
                    throw new \RuntimeException(
                        'La compra ya está anulada.'
                    );
                }

                if ($caja) {
                    $caja = Caja::whereKey($caja->id)
                        ->where('estado', 'abierta')
                        ->lockForUpdate()
                        ->first();

                    if (!$caja) {
                        throw new \RuntimeException(
                            'No se puede anular esta compra porque la caja ya no está abierta para registrar la reversión.'
                        );
                    }
                }

                $compra->update([
                    'estado' => 'Anulada',
                    'motivo_anulacion' =>
                        $validated['motivo_anulacion'] ?? null,
                    'fecha_anulacion' => now(),
                ]);

                if ($caja) {
                    foreach ($movimientosOriginales as $movimiento) {
                        MovimientoCaja::create([
                            'caja_id' => $caja->id,
                            'compra_anulada_id' => $compra->id,
                            'tipo' => 'ingreso',
                            'concepto' =>
                                'Anulación Compra #' . $compra->id,
                            'medio' => $movimiento->medio,
                            'monto' => $movimiento->monto,
                            'observacion' =>
                                $validated['motivo_anulacion'] ?? null,
                        ]);
                    }
                }

                /*
                 * Si la compra utilizó saldo a favor del proveedor,
                 * al quedar anulada deja de formar parte del resumen
                 * de cuenta corriente.
                 *
                 * Por lo tanto ese saldo vuelve a quedar disponible.
                 */

                $this->revertirMovimientosStock($compra);
            });
        } catch (\RuntimeException $exception) {
            return redirect('/compras')
                ->with(
                    'error',
                    $exception->getMessage()
                );
        }

        return redirect('/compras')
            ->with(
                'success',
                'La compra fue anulada correctamente.'
            );
    }

    private function validarCompra(Request $request): array
    {
        return $request->validate([
            'fecha' => 'required|date',
            'proveedor_id' => 'nullable|exists:proveedores,id',
            'producto_id' => 'required',
            'cantidad' => 'required|integer|min:1',
            'precio' => 'required|numeric|min:0',

            'formas_pago' => 'required|array|min:1',

            'formas_pago.*' =>
                'required|in:efectivo,tarjeta,transferencia,cuenta_corriente,saldo_a_favor',

            'montos_pago' => 'nullable|array',

            'montos_pago.*' =>
                'nullable|numeric|min:0',
        ], [
            'formas_pago.required' =>
                'Seleccione al menos una forma de pago.',

            'formas_pago.min' =>
                'Seleccione al menos una forma de pago.',
        ]);
    }

    private function prepararCompra(
        array $validated,
        CuentaCorrienteService $cuentaCorrienteService,
        ?float $saldoAFavorForzado = null
    ): array {
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
         * Obtenemos el saldo a favor disponible del proveedor.
         */
        $saldoAFavorDisponible = 0;

        if ($saldoAFavorForzado !== null) {
            $saldoAFavorDisponible =
                max(0, round($saldoAFavorForzado, 2));
        } elseif (!empty($validated['proveedor_id'])) {
            $proveedor = Proveedor::findOrFail(
                $validated['proveedor_id']
            );

            $saldoAFavorDisponible =
                $cuentaCorrienteService
                    ->saldoAFavorProveedor($proveedor);
        }

        /*
         * El sistema utiliza automáticamente el saldo a favor.
         */
        $saldoAFavorUsado = round(
            min(
                $saldoAFavorDisponible,
                $total
            ),
            2
        );

        /*
         * Este es el importe que realmente queda para pagar.
         */
        $restante = round(
            $total - $saldoAFavorUsado,
            2
        );

        $montos = [];

        /*
         * El saldo a favor se guarda siempre que se haya utilizado.
         */
        if ($saldoAFavorUsado > 0) {
            $montos['saldo_a_favor'] =
                $saldoAFavorUsado;
        }

        $formasPago = $validated['formas_pago'];

        /*
         * Si solamente hay una forma de pago seleccionada,
         * esa forma cubre automáticamente el importe restante.
         *
         * saldo_a_favor no cuenta como una forma que deba
         * recibir un monto manual porque el sistema lo calcula.
         */
        $formasPagoManuales = array_values(
            array_filter(
                $formasPago,
                fn ($forma) =>
                    $forma !== 'saldo_a_favor'
            )
        );

        if ($restante > 0) {
            if (count($formasPagoManuales) === 1) {
                $formaPago = $formasPagoManuales[0];

                $montos[$formaPago] = $restante;
            } else {
                foreach ($formasPagoManuales as $formaPago) {
                    if (
                        !array_key_exists(
                            $formaPago,
                            $validated['montos_pago'] ?? []
                        )
                    ) {
                        abort(
                            422,
                            'Debe indicar el monto de cada forma de pago.'
                        );
                    }

                    $montos[$formaPago] = round(
                        (float) $validated['montos_pago'][$formaPago],
                        2
                    );
                }

                $totalMontosManuales = round(
                    array_sum($montos),
                    2
                );

                /*
                 * En este punto todavía tenemos el saldo a favor
                 * dentro de $montos, por eso calculamos únicamente
                 * las formas de pago que no son saldo a favor.
                 */
                $totalManual = round(
                    array_sum(
                        array_filter(
                            $montos,
                            fn ($formaPago) =>
                                $formaPago !== 'saldo_a_favor'
                        )
                    ),
                    2
                );

                if ($totalManual > $restante) {
                    abort(
                        422,
                        'La suma de los pagos no puede superar el importe restante de la compra.'
                    );
                }

                if ($totalManual < $restante) {
                    abort(
                        422,
                        'La suma de los pagos debe cubrir exactamente el importe restante de la compra.'
                    );
                }
            }
        }

        /*
         * Si el saldo a favor cubrió todo, no necesitamos
         * ninguna otra forma de pago.
         */
        if ($restante <= 0) {
            /*
             * Eliminamos cualquier forma de pago que no corresponda.
             */
            $montos = [
                'saldo_a_favor' => $saldoAFavorUsado,
            ];
        }

        /*
         * El total pagado incluye:
         * - saldo a favor utilizado
         * - dinero efectivamente pagado
         *
         * Cuenta corriente NO se considera dinero pagado.
         */
        $totalPagado = round(
            array_sum(
                array_filter(
                    $montos,
                    fn ($formaPago) =>
                        $formaPago !== 'cuenta_corriente'
                )
            ),
            2
        );

        if ($totalPagado > $total) {
            abort(
                422,
                'La suma de los pagos no puede superar el total de la compra.'
            );
        }

        /*
         * Determinamos producto.
         */
        $productoId =
            $validated['producto_id'] === 'otro'
                ? null
                : $validated['producto_id'];

        $productoNombre = 'Otro';

        if ($productoId !== null) {
            $productoNombre =
                Producto::findOrFail($productoId)->nombre;
        }

        return [
            'cantidad' => $cantidad,
            'precio' => $precio,
            'total' => $total,

            'saldo_a_favor_disponible' =>
                $saldoAFavorDisponible,

            'saldo_a_favor_usado' =>
                $saldoAFavorUsado,

            'restante' =>
                $restante,

            'total_pagado' =>
                $totalPagado,

            'estado' =>
                $totalPagado >= $total
                    ? 'Pagado'
                    : ($totalPagado > 0
                        ? 'Parcial'
                        : 'Pendiente'),

            'producto_id' =>
                $productoId,

            'producto_nombre' =>
                $productoNombre,

            'montos' =>
                $montos,
        ];
    }

    private function guardarDetalleYPagos(
        Compra $compra,
        array $datos
    ): void {
        $compra->detalles()->create([
            'producto_id' =>
                $datos['producto_id'],

            'producto_nombre' =>
                $datos['producto_nombre'],

            'cantidad' =>
                $datos['cantidad'],

            'precio' =>
                $datos['precio'],

            'subtotal' =>
                $datos['total'],
        ]);

        foreach ($datos['montos'] as $formaPago => $monto) {
            if ((float) $monto <= 0) {
                continue;
            }

            $compra->pagos()->create([
                'forma_pago' => $formaPago,
                'monto' => $monto,
            ]);
        }
    }

    private function montosCaja(array $montos): array
    {
        return array_filter(
            $montos,
            function ($monto, $medio) {
                return
                    $medio !== 'cuenta_corriente'
                    && $medio !== 'saldo_a_favor'
                    && (float) $monto > 0;
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
            ->where('estado', 'abierta')
            ->first();
    }

    private function cajaAbiertaParaReversion(): ?Caja
    {
        return Caja::whereDate(
            'fecha',
            now()->toDateString()
        )
            ->where('estado', 'abierta')
            ->first();
    }

    private function crearMovimientosEgreso(
        Compra $compra,
        ?Caja $caja,
        array $montosCaja
    ): void {
        if (!$caja) {
            return;
        }

        foreach ($montosCaja as $medio => $monto) {
            MovimientoCaja::create([
                'caja_id' => $caja->id,
                'compra_id' => $compra->id,
                'tipo' => 'egreso',
                'concepto' =>
                    'Compra #' . $compra->id,
                'medio' => $medio,
                'monto' => $monto,
            ]);
        }
    }

    // "Otro" (producto_id null) no controla Stock:
    // solo se generan entradas para productos reales del catálogo.
    private function generarMovimientosStock(
        Compra $compra,
        array $datos
    ): void {
        if (
            $datos['producto_id'] === null
            || $datos['cantidad'] <= 0
        ) {
            return;
        }

        MovimientoStock::create([
            'producto_id' =>
                $datos['producto_id'],

            'compra_id' =>
                $compra->id,

            'tipo' =>
                'entrada',

            'cantidad' =>
                $datos['cantidad'],

            'motivo' =>
                'Compra #' . $compra->id,

            'fecha' =>
                $compra->fecha,
        ]);
    }

    // Genera el movimiento inverso (salida) de cada entrada
    // original de la compra, sin borrar el historial.
    private function revertirMovimientosStock(
        Compra $compra
    ): void {
        $entradas = MovimientoStock::where(
            'compra_id',
            $compra->id
        )
            ->where('tipo', 'entrada')
            ->lockForUpdate()
            ->get();

        $yaRevertido = MovimientoStock::where(
            'compra_id',
            $compra->id
        )
            ->where('tipo', 'salida')
            ->exists();

        if ($yaRevertido) {
            return;
        }

        foreach ($entradas as $entrada) {
            MovimientoStock::create([
                'producto_id' =>
                    $entrada->producto_id,

                'compra_id' =>
                    $compra->id,

                'tipo' =>
                    'salida',

                'cantidad' =>
                    $entrada->cantidad,

                'motivo' =>
                    'Anulación Compra #' . $compra->id,

                'fecha' =>
                    now()->toDateString(),
            ]);
        }
    }
}