<table>
    <tbody id="filas-movimientos-caja-ajax">
        @include('partials.caja-rows', [
            'movimientos' => $movimientos
        ])
    </tbody>
</table>

@include('partials.paginacion-caja', [
    'paginador' => $movimientos
])


<table>
    <tbody id="filas-cierres-caja-ajax">
        @include('partials.cierres-caja-rows', [
            'cierres' => $cierres
        ])
    </tbody>
</table>

@include('partials.paginacion-cierres-caja', [
    'paginador' => $cierres
])


@php
    $movimientosJson = $movimientos
        ->getCollection()
        ->map(function ($movimiento) {
            return [
                'id' => $movimiento->id,
                'fecha' => $movimiento->created_at->format('d/m/Y H:i'),
                'concepto' => $movimiento->concepto,
                'tipo' => $movimiento->tipo,
                'medio' => $movimiento->medio,
                'monto' => $movimiento->monto,
                'observacion' => $movimiento->observacion ?? '',
            ];
        })
        ->values();

    $cierresJson = $cierres
        ->getCollection()
        ->map(function ($cierre) {
            return [
                'id' => $cierre->id,
                'fecha' => $cierre->fecha->format('d/m/Y'),
                'inicial' => $cierre->saldo_inicial,
                'ingresos' => $cierre->total_ingresos,
                'egresos' => $cierre->total_egresos,
                'esperado' => $cierre->saldo_esperado,
                'contado' => $cierre->dinero_contado,
                'diferencia' => $cierre->diferencia,
                'estado' => ucfirst($cierre->estado),

                'movimientos' => $cierre->caja->movimientos
                    ->map(function ($movimiento) {
                        return [
                            'fecha' => $movimiento->created_at->format('d/m/Y H:i'),
                            'concepto' => $movimiento->concepto,
                            'tipo' => $movimiento->tipo,
                            'medio' => $movimiento->medio,
                            'monto' => $movimiento->monto,
                            'observacion' => $movimiento->observacion ?? '',
                        ];
                    })
                    ->values(),
            ];
        })
        ->values();
@endphp


<script type="application/json" id="datos-movimientos-caja-ajax">
    @json($movimientosJson)
</script>

<script type="application/json" id="datos-cierres-caja-ajax">
    @json($cierresJson)
</script>