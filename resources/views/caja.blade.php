@extends('layouts.app')

@section('title', 'Caja')

@section('content')

    <style>

        .caja-pagina {
            max-width: 1280px;
        }

        .caja-encabezado {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 24px;
            margin-bottom: 28px;
        }

        .caja-encabezado h1 {
            margin: 0 0 8px;
        }

        .caja-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px 18px;
            color: #555;
            font-size: 14px;
        }

        .caja-estado {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-weight: bold;
            color: #176b3a;
        }

        .caja-estado::before {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: #28a745;
            content: '';
        }

        .caja-estado.cerrada {
            color: #a83a3a;
        }

        .caja-estado.cerrada::before {
            background: #dc3545;
        }

        .caja-mensaje {
            margin-bottom: 20px;
            padding: 12px 16px;
            border: 1px solid;
        }

        .caja-mensaje-exito {
            position: fixed;
            top: 20px;
            right: 20px;
            margin-bottom: 0;
            padding: 15px 20px;
            border: 0;
            border-radius: 8px;
            background: #28a745;
            color: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, .2);
            z-index: 1000;
        }

        .caja-mensaje-error {
            border-color: #f5c2c7;
            background: #f8d7da;
            color: #842029;
        }

        .caja-acciones {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .boton-caja {
            display: inline-block;
            padding: 11px 16px;
            background-color: #2563eb;
            color: black;
            text-decoration: none;
            border: 0;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            margin-right: 5px;
            font-weight: bold;
            transition:
                background-color 0.2s ease,
                box-shadow 0.2s ease,
                transform 0.2s ease;
        }

        .boton-caja:hover {
            background: #1d4ed8 !important;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3) !important;
            transform: translateY(-2px) !important;
        }

        .boton-caja-principal {
            display: inline-block;
            padding: 11px 16px;
            background-color: #2563eb;
            color: black;
            text-decoration: none;
            border: 0;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            margin-right: 5px;
            font-weight: bold;
            transition:
                background-color 0.2s ease,
                box-shadow 0.2s ease,
                transform 0.2s ease;
        }

        .boton-caja-abrir {
            display: inline-block;
            padding: 11px 16px;
            background-color: #2563eb;
            color: black;
            text-decoration: none;
            border: 0;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            margin-right: 5px;
            font-weight: bold;
            transition:
                background-color 0.2s ease,
                box-shadow 0.2s ease,
                transform 0.2s ease;
        }

        .boton-caja-abrir:hover {
            background: #1d4ed8 !important;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3) !important;
            transform: translateY(-2px) !important;
        }

        .caja-resumen {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 32px;
        }

        .caja-tarjeta {
            min-height: 124px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .05);
        }

        .caja-tarjeta-etiqueta {
            margin-bottom: 14px;
            color: #666;
            font-size: 14px;
        }

        .caja-tarjeta-valor {
            font-size: 25px;
            font-weight: bold;
            color: #222;
        }

        .caja-tarjeta.ingresos .caja-tarjeta-valor {
            color: #176b3a;
        }

        .caja-tarjeta.egresos .caja-tarjeta-valor {
            color: #a83a3a;
        }

        .caja-seccion {
            margin-top: 28px;
            padding: 24px;
            border: 1px solid #ddd;
            background: #fff;
        }

        .caja-seccion h2 {
            margin: 0 0 18px;
            font-size: 21px;
        }

        .caja-herramientas {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
            margin-bottom: 18px;
        }

        .caja-busqueda {
            width: min(300px, 100%);
            padding: 9px 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font: inherit;
        }

        .caja-filtro {
            padding: 9px 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background: #fff;
            font: inherit;
        }

        .caja-herramientas .boton-caja {
            margin-left: auto;
        }

        .caja-tabla {
            width: 100%;
            border-collapse: collapse;
        }

        .caja-tabla th,
        .caja-tabla td {
            padding: 11px 10px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        .caja-tabla th {
            color: #555;
            font-size: 13px;
            font-weight: bold;
            background-color: #83e7f2;
        }

        .caja-tabla tbody tr:hover {
            background: #f8fbfe;
        }

        .caja-tipo {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }

        .caja-tipo.ingreso {
            background: #d1e7dd;
            color: #0f5132;
        }

        .caja-tipo.egreso {
            background: #f8d7da;
            color: #842029;
        }

        .caja-monto.ingreso {
            color: #176b3a;
            font-weight: bold;
        }

        .caja-monto.egreso {
            color: #a83a3a;
            font-weight: bold;
        }

        .caja-accion-ojo {
            display: inline-block;
            padding: 11px 16px;
            background-color: #2563eb;
            color: black;
            text-decoration: none;
            border: 0;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            margin-right: 5px;
            font-weight: bold;
            transition:
                background-color 0.2s ease,
                box-shadow 0.2s ease,
                transform 0.2s ease;
        }

        .caja-accion-ojo:hover {
            background: #1d4ed8 !important;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3) !important;
            transform: translateY(-2px) !important;
        }

        .caja-vacia {
            padding: 24px 10px;
            color: #666;
            text-align: center;
        }

        /*
        |--------------------------------------------------------------------------
        | Paginación
        |--------------------------------------------------------------------------
        */

        #paginacion-movimientos-caja,
        #paginacion-cierres-caja {
            margin-top: 20px;
        }

        #paginacion-movimientos-caja nav,
        #paginacion-cierres-caja nav {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }

        #paginacion-movimientos-caja a,
        #paginacion-movimientos-caja span,
        #paginacion-cierres-caja a,
        #paginacion-cierres-caja span {
            display: inline-block;
            padding: 6px 10px;
            margin-right: 5px;
            border: 1px solid #cccccc;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
        }

        #paginacion-movimientos-caja a,
        #paginacion-cierres-caja a {
            background-color: #eeeeee;
            color: black;
        }

        #paginacion-movimientos-caja span,
        #paginacion-cierres-caja span {
            background-color: #cccccc;
            color: black;
        }

        .modal-caja {
            position: fixed;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: rgba(0, 0, 0, .35);
            z-index: 900;
        }

        .modal-caja.oculto {
            display: none;
        }

        .modal-caja-contenido {
            width: min(620px, 100%);
            max-height: 90vh;
            overflow-y: auto;
            padding: 28px;
            background: #fff;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .25);
        }

        .modal-caja-encabezado {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
        }

        .modal-caja-encabezado h2 {
            margin: 0;
        }

        .cerrar-modal-caja {
            margin: 0;
            padding: 4px 10px;
            font-size: 22px;
            background: transparent;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .cerrar-modal-caja:hover {
            background: #1d4ed8 !important;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3) !important;
            transform: translateY(-2px) !important;
        }

        .campo-caja {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 16px;
        }

        .campo-caja input,
        .campo-caja select,
        .campo-caja textarea {
            width: 100%;
            padding: 9px 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font: inherit;
        }

        .campo-caja textarea {
            min-height: 90px;
            resize: vertical;
        }

        .caja-resumen-cierre {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px 20px;
            margin-bottom: 22px;
        }

        .caja-resumen-cierre div {
            display: flex;
            justify-content: space-between;
            padding: 9px 0;
            border-bottom: 1px solid #eee;
        }

        .caja-resumen-cierre strong {
            color: #222;
        }

        .caja-diferencia-negativa {
            color: #a83a3a !important;
        }

        .caja-diferencia-positiva {
            color: #176b3a !important;
        }

        @media (max-width: 850px) {

            .caja-encabezado {
                align-items: flex-start;
                flex-direction: column;
            }

            .caja-resumen {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .caja-herramientas .boton-caja {
                margin-left: 0;
            }
        }

        @media (max-width: 600px) {

            .caja-resumen {
                grid-template-columns: 1fr;
            }

            .caja-seccion {
                padding: 16px;
                overflow-x: auto;
            }

            .caja-tabla {
                min-width: 680px;
            }

            .caja-resumen-cierre {
                grid-template-columns: 1fr;
            }
        }

    </style>


    @php

        $saldoInicial = $caja
            ? (float) $caja->saldo_inicial
            : 0;

        $cajaAbierta = $caja
            && $caja->estado === 'abierta';

        $saldoEsperado =
            $saldoInicial
            + $ingresos
            - $egresos;


        /*
        |--------------------------------------------------------------------------
        | Valores seguros para los filtros
        |--------------------------------------------------------------------------
        */

        $buscarMovimientoActual = $buscarMovimiento ?? '';

        $tipoMovimientoActual = $tipoMovimiento ?? 'Todos';

        $medioMovimientoActual = $medioMovimiento ?? 'Todos';


        /*
        |--------------------------------------------------------------------------
        | Datos JSON de los movimientos de la página actual
        |--------------------------------------------------------------------------
        */

        $movimientosJson = $movimientos
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


        /*
        |--------------------------------------------------------------------------
        | Datos JSON de los cierres de la página actual
        |--------------------------------------------------------------------------
        */

        $cierresJson = $cierres
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


    @if (session('success'))

        <div
            id="mensaje-exito-caja"
            class="caja-mensaje caja-mensaje-exito"
        >
            ✓ {{ session('success') }}
        </div>

        <script>

            setTimeout(function () {

                const mensaje =
                    document.getElementById('mensaje-exito-caja');

                if (mensaje) {
                    mensaje.style.display = 'none';
                }

            }, 3000);

        </script>

    @endif


    @if (session('error'))

        <div class="caja-mensaje caja-mensaje-error">
            {{ session('error') }}
        </div>

    @endif


    @if ($errors->any())

        <div class="caja-mensaje caja-mensaje-error">
            {{ $errors->first() }}
        </div>

    @endif


    <div class="caja-pagina">

        <div class="caja-encabezado">

            <div>

                <h1>Caja</h1>

                <div class="caja-meta">

                    <span>
                        Fecha:
                        {{ \Carbon\Carbon::parse($fechaActual)->format('d/m/Y') }}
                    </span>

                    <span class="caja-estado {{ $cajaAbierta ? '' : 'cerrada' }}">
                        {{ $cajaAbierta ? 'Caja abierta' : 'Caja cerrada' }}
                    </span>

                </div>

            </div>


            <div class="caja-acciones">

                @if ($cajaAbierta)

                    <button
                        type="button"
                        class="boton-caja"
                        data-abrir-modal="modal-cierre-caja"
                    >
                        Cerrar caja
                    </button>

                @elseif (!$caja)

                    <button
                        type="button"
                        class="boton-caja boton-caja-abrir"
                        data-abrir-modal="modal-apertura-caja"
                    >
                        Abrir caja
                    </button>

                @endif

            </div>

        </div>


        <section
            class="caja-resumen"
            aria-label="Resumen de caja"
        >

            <article class="caja-tarjeta">

                <div class="caja-tarjeta-etiqueta">
                    Saldo inicial
                </div>

                <div class="caja-tarjeta-valor">
                    ${{ number_format($saldoInicial, 0, ',', '.') }}
                </div>

            </article>


            <article class="caja-tarjeta ingresos">

                <div class="caja-tarjeta-etiqueta">
                    Ingresos
                </div>

                <div class="caja-tarjeta-valor">
                    ${{ number_format($ingresos, 2, ',', '.') }}
                </div>

            </article>


            <article class="caja-tarjeta egresos">

                <div class="caja-tarjeta-etiqueta">
                    Egresos
                </div>

                <div class="caja-tarjeta-valor">
                    ${{ number_format($egresos, 2, ',', '.') }}
                </div>

            </article>


            <article class="caja-tarjeta">

                <div class="caja-tarjeta-etiqueta">
                    Saldo actual
                </div>

                <div class="caja-tarjeta-valor">
                    ${{ number_format($saldoActual, 2, ',', '.') }}
                </div>

            </article>

        </section>


        {{-- ============================================================
             MOVIMIENTOS DE CAJA
        ============================================================ --}}

        <section class="caja-seccion">

            <h2>Movimientos de caja</h2>


            <div class="caja-herramientas">

                <input
                    type="search"
                    id="buscar-movimiento-caja"
                    class="caja-busqueda"
                    value="{{ $buscarMovimientoActual }}"
                    placeholder="Buscar por concepto o medio"
                >


                <select
                    id="filtro-tipo-caja"
                    class="caja-filtro"
                    aria-label="Filtrar por tipo"
                >

                    <option
                        value="Todos"
                        @selected($tipoMovimientoActual === 'Todos')
                    >
                        Todos
                    </option>

                    <option
                        value="ingreso"
                        @selected($tipoMovimientoActual === 'ingreso')
                    >
                        Ingresos
                    </option>

                    <option
                        value="egreso"
                        @selected($tipoMovimientoActual === 'egreso')
                    >
                        Egresos
                    </option>

                </select>


                <select
                    id="filtro-medio-caja"
                    class="caja-filtro"
                    aria-label="Filtrar por medio de pago"
                >

                    <option
                        value="Todos"
                        @selected($medioMovimientoActual === 'Todos')
                    >
                        Todos
                    </option>

                    <option
                        value="efectivo"
                        @selected($medioMovimientoActual === 'efectivo')
                    >
                        Efectivo
                    </option>

                    <option
                        value="tarjeta"
                        @selected($medioMovimientoActual === 'tarjeta')
                    >
                        Tarjeta
                    </option>

                    <option
                        value="transferencia"
                        @selected($medioMovimientoActual === 'transferencia')
                    >
                        Transferencia
                    </option>

                </select>


                @if ($cajaAbierta)

                    <button
                        type="button"
                        class="boton-caja"
                        data-abrir-modal="modal-movimiento-caja"
                    >
                        + Movimiento
                    </button>

                @endif

            </div>


            <table class="caja-tabla">

                <thead>

                    <tr>
                        <th>Fecha</th>
                        <th>Concepto</th>
                        <th>Tipo</th>
                        <th>Medio</th>
                        <th>Monto</th>
                        <th>Acciones</th>
                    </tr>

                </thead>


                <tbody id="filas-movimientos-caja">

                    @include('partials.caja-rows', [
                        'movimientos' => $movimientos
                    ])

                </tbody>

            </table>


            <div id="paginacion-movimientos-caja">

                @include('partials.paginacion-caja', [
                    'paginador' => $movimientos
                ])

            </div>

        </section>


        {{-- ============================================================
             HISTORIAL DE CIERRES
        ============================================================ --}}

        <section class="caja-seccion">

            <h2>Historial de cierres</h2>


            <table class="caja-tabla">

                <thead>

                    <tr>
                        <th></th>
                        <th>Fecha</th>
                        <th>Saldo inicial</th>
                        <th>Ingresos</th>
                        <th>Egresos</th>
                        <th>Saldo final</th>
                        <th>Diferencia</th>
                        <th>Estado</th>
                    </tr>

                </thead>


                <tbody id="filas-cierres-caja">

                    @include('partials.cierres-caja-rows', [
                        'cierres' => $cierres
                    ])

                </tbody>

            </table>


            <div id="paginacion-cierres-caja">

                @include('partials.paginacion-cierres-caja', [
                    'paginador' => $cierres
                ])

            </div>

        </section>

    </div>


    {{-- ================================================================
         MODAL NUEVO MOVIMIENTO
    ================================================================= --}}

    <div
        id="modal-movimiento-caja"
        class="modal-caja oculto"
        aria-hidden="true"
    >

        <section
            class="modal-caja-contenido"
            role="dialog"
            aria-modal="true"
            aria-labelledby="titulo-movimiento-caja"
        >

            <div class="modal-caja-encabezado">

                <h2 id="titulo-movimiento-caja">
                    Nuevo movimiento
                </h2>

                <button
                    type="button"
                    class="cerrar-modal-caja"
                    data-cerrar-modal="modal-movimiento-caja"
                    aria-label="Cerrar"
                >
                    &times;
                </button>

            </div>


            <form
                id="form-movimiento-caja"
                method="POST"
                action="{{ route('caja.movimientos.store') }}"
            >

                @csrf

                <div class="campo-caja">

                    <label for="tipo-movimiento-caja">
                        Tipo
                    </label>

                    <select
                        id="tipo-movimiento-caja"
                        name="tipo"
                    >

                        <option value="ingreso">
                            Ingreso
                        </option>

                        <option value="egreso">
                            Egreso
                        </option>

                    </select>

                </div>


                <div class="campo-caja">

                    <label for="concepto-movimiento-caja">
                        Concepto
                    </label>

                    <input
                        id="concepto-movimiento-caja"
                        name="concepto"
                        required
                    >

                </div>


                <div class="campo-caja">

                    <label for="medio-movimiento-caja">
                        Medio de pago
                    </label>

                    <select
                        id="medio-movimiento-caja"
                        name="medio"
                    >

                        <option value="efectivo">
                            Efectivo
                        </option>

                        <option value="tarjeta">
                            Tarjeta
                        </option>

                        <option value="transferencia">
                            Transferencia
                        </option>

                    </select>

                </div>


                <div class="campo-caja">

                    <label for="monto-movimiento-caja">
                        Monto
                    </label>

                    <input
                        id="monto-movimiento-caja"
                        name="monto"
                        type="number"
                        min="0"
                        step="0.01"
                        required
                    >

                </div>


                <div class="campo-caja">

                    <label for="observacion-movimiento-caja">
                        Observación (opcional)
                    </label>

                    <textarea
                        id="observacion-movimiento-caja"
                        name="observacion"
                    ></textarea>

                </div>


                <button
                    type="button"
                    class="boton-caja"
                    data-cerrar-modal="modal-movimiento-caja"
                >
                    Cancelar
                </button>

                <button
                    type="submit"
                    class="boton-caja boton-caja-principal"
                >
                    Guardar
                </button>

            </form>

        </section>

    </div>


    {{-- ================================================================
         MODAL APERTURA
    ================================================================= --}}

    <div
        id="modal-apertura-caja"
        class="modal-caja oculto"
        aria-hidden="true"
    >

        <section
            class="modal-caja-contenido"
            role="dialog"
            aria-modal="true"
            aria-labelledby="titulo-apertura-caja"
        >

            <div class="modal-caja-encabezado">

                <h2 id="titulo-apertura-caja">
                    Abrir caja
                </h2>

                <button
                    type="button"
                    class="cerrar-modal-caja"
                    data-cerrar-modal="modal-apertura-caja"
                    aria-label="Cerrar"
                >
                    &times;
                </button>

            </div>


            <p>
                Ingresá el saldo inicial de la jornada de hoy.
            </p>


            <form
                method="POST"
                action="{{ route('caja.abrir') }}"
            >

                @csrf

                <div class="campo-caja">

                    <label for="saldo-inicial-caja">
                        Saldo inicial
                    </label>

                    <input
                        id="saldo-inicial-caja"
                        name="saldo_inicial"
                        type="number"
                        min="0"
                        step="0.01"
                        value="{{ old('saldo_inicial', '50000') }}"
                        required
                    >

                </div>


                <button
                    type="button"
                    class="boton-caja"
                    data-cerrar-modal="modal-apertura-caja"
                >
                    Cancelar
                </button>

                <button
                    type="submit"
                    class="boton-caja boton-caja-principal"
                >
                    Confirmar apertura
                </button>

            </form>

        </section>

    </div>


    {{-- ================================================================
         MODAL CIERRE
    ================================================================= --}}

    <div
        id="modal-cierre-caja"
        class="modal-caja oculto"
        aria-hidden="true"
    >

        <section
            class="modal-caja-contenido"
            role="dialog"
            aria-modal="true"
            aria-labelledby="titulo-cierre-caja"
        >

            <div class="modal-caja-encabezado">

                <h2 id="titulo-cierre-caja">
                    Cerrar caja
                </h2>

                <button
                    type="button"
                    class="cerrar-modal-caja"
                    data-cerrar-modal="modal-cierre-caja"
                    aria-label="Cerrar"
                >
                    &times;
                </button>

            </div>


            <div class="caja-resumen-cierre">

                <div>
                    <span>Saldo inicial</span>

                    <strong>
                        ${{ number_format($saldoInicial, 2, ',', '.') }}
                    </strong>
                </div>


                <div>
                    <span>Ingresos</span>

                    <strong class="caja-monto ingreso">
                        +${{ number_format($ingresos, 2, ',', '.') }}
                    </strong>
                </div>


                <div>
                    <span>Egresos</span>

                    <strong class="caja-monto egreso">
                        -${{ number_format($egresos, 2, ',', '.') }}
                    </strong>
                </div>


                <div>
                    <span>Saldo esperado</span>

                    <strong>
                        ${{ number_format($saldoEsperado, 2, ',', '.') }}
                    </strong>
                </div>

            </div>


            <form
                id="form-cierre-caja"
                method="POST"
                action="{{ route('caja.cerrar') }}"
                data-saldo-esperado="{{ number_format($saldoEsperado, 2, '.', '') }}"
            >

                @csrf

                <div class="campo-caja">

                    <label for="dinero-contado-caja">
                        Dinero contado
                    </label>

                    <input
                        id="dinero-contado-caja"
                        name="dinero_contado"
                        type="number"
                        min="0"
                        step="0.01"
                        value="{{ old('dinero_contado', number_format($saldoEsperado, 2, '.', '')) }}"
                        required
                    >

                </div>


                <div class="caja-resumen-cierre">

                    <div>

                        <span>Diferencia</span>

                        <strong id="diferencia-cierre-caja">
                            $0,00
                        </strong>

                    </div>

                </div>


                <button
                    type="button"
                    class="boton-caja"
                    data-cerrar-modal="modal-cierre-caja"
                >
                    Cancelar
                </button>

                <button
                    type="submit"
                    class="boton-caja boton-caja-principal"
                >
                    Confirmar cierre
                </button>

            </form>

        </section>

    </div>


    {{-- ================================================================
         MODAL DETALLE MOVIMIENTO
    ================================================================= --}}

    <div
        id="modal-detalle-movimiento-caja"
        class="modal-caja oculto"
        aria-hidden="true"
    >

        <section class="modal-caja-contenido">

            <div class="modal-caja-encabezado">

                <h2>
                    Detalle del movimiento
                </h2>

                <button
                    type="button"
                    class="cerrar-modal-caja"
                    data-cerrar-modal="modal-detalle-movimiento-caja"
                    aria-label="Cerrar"
                >
                    &times;
                </button>

            </div>


            <div id="contenido-detalle-movimiento-caja"></div>

        </section>

    </div>


    {{-- ================================================================
         MODAL DETALLE CIERRE
    ================================================================= --}}

    <div
        id="modal-detalle-cierre-caja"
        class="modal-caja oculto"
        aria-hidden="true"
    >

        <section class="modal-caja-contenido">

            <div class="modal-caja-encabezado">

                <h2>
                    Detalle del cierre
                </h2>

                <button
                    type="button"
                    class="cerrar-modal-caja"
                    data-cerrar-modal="modal-detalle-cierre-caja"
                    aria-label="Cerrar"
                >
                    &times;
                </button>

            </div>


            <div id="contenido-detalle-cierre-caja"></div>

        </section>

    </div>


    {{-- ================================================================
         DATOS JSON INICIALES
    ================================================================= --}}

    <script
        type="application/json"
        id="datos-movimientos-caja"
    >
        @json($movimientosJson)
    </script>


    <script
        type="application/json"
        id="datos-cierres-caja"
    >
        @json($cierresJson)
    </script>


    <script>

        (function () {

            /*
            |--------------------------------------------------------------------------
            | Datos actuales
            |--------------------------------------------------------------------------
            */

            let movimientos = JSON.parse(
                document.getElementById('datos-movimientos-caja').textContent
            );

            let cierres = JSON.parse(
                document.getElementById('datos-cierres-caja').textContent
            );


            /*
            |--------------------------------------------------------------------------
            | Elementos
            |--------------------------------------------------------------------------
            */

            const filasMovimientos =
                document.getElementById('filas-movimientos-caja');

            const filasCierres =
                document.getElementById('filas-cierres-caja');

            const paginacionMovimientos =
                document.getElementById('paginacion-movimientos-caja');

            const paginacionCierres =
                document.getElementById('paginacion-cierres-caja');

            const busqueda =
                document.getElementById('buscar-movimiento-caja');

            const filtroTipo =
                document.getElementById('filtro-tipo-caja');

            const filtroMedio =
                document.getElementById('filtro-medio-caja');

            const modales =
                document.querySelectorAll('.modal-caja');

            const formularioCierre =
                document.getElementById('form-cierre-caja');

            const dineroContado =
                document.getElementById('dinero-contado-caja');

            const diferenciaCierre =
                document.getElementById('diferencia-cierre-caja');


            /*
            |--------------------------------------------------------------------------
            | Formatear dinero
            |--------------------------------------------------------------------------
            */

            function dinero(valor) {

                return '$' + Number(valor).toLocaleString(
                    'es-AR',
                    {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 2
                    }
                );

            }


            /*
            |--------------------------------------------------------------------------
            | Diferencia del cierre
            |--------------------------------------------------------------------------
            */

            function actualizarDiferencia() {

                if (
                    !formularioCierre ||
                    !dineroContado ||
                    !diferenciaCierre
                ) {
                    return;
                }

                const saldoEsperado =
                    Number(formularioCierre.dataset.saldoEsperado);

                const contado =
                    Number(dineroContado.value || 0);

                const diferencia =
                    Math.round(
                        (contado - saldoEsperado) * 100
                    ) / 100;

                const signo =
                    diferencia > 0
                        ? '+'
                        : diferencia < 0
                            ? '-'
                            : '';

                diferenciaCierre.textContent =
                    signo + dinero(Math.abs(diferencia));

                diferenciaCierre.classList.toggle(
                    'caja-diferencia-negativa',
                    diferencia < 0
                );

                diferenciaCierre.classList.toggle(
                    'caja-diferencia-positiva',
                    diferencia > 0
                );

            }


            /*
            |--------------------------------------------------------------------------
            | Abrir modal
            |--------------------------------------------------------------------------
            */

            function abrir(id) {

                const modal =
                    document.getElementById(id);

                if (!modal) {
                    return;
                }

                modal.classList.remove('oculto');

                modal.setAttribute(
                    'aria-hidden',
                    'false'
                );

            }


            /*
            |--------------------------------------------------------------------------
            | Cerrar modal
            |--------------------------------------------------------------------------
            */

            function cerrar(id) {

                const modal =
                    document.getElementById(id);

                if (!modal) {
                    return;
                }

                modal.classList.add('oculto');

                modal.setAttribute(
                    'aria-hidden',
                    'true'
                );

            }


            /*
            |--------------------------------------------------------------------------
            | Botones para abrir modales
            |--------------------------------------------------------------------------
            */

            document
                .querySelectorAll('[data-abrir-modal]')
                .forEach(function (boton) {

                    boton.addEventListener(
                        'click',
                        function () {

                            abrir(
                                boton.dataset.abrirModal
                            );

                        }
                    );

                });


            /*
            |--------------------------------------------------------------------------
            | Botones para cerrar modales
            |--------------------------------------------------------------------------
            */

            document
                .querySelectorAll('[data-cerrar-modal]')
                .forEach(function (boton) {

                    boton.addEventListener(
                        'click',
                        function () {

                            cerrar(
                                boton.dataset.cerrarModal
                            );

                        }
                    );

                });


            /*
            |--------------------------------------------------------------------------
            | Cerrar haciendo click fuera
            |--------------------------------------------------------------------------
            */

            modales.forEach(function (modal) {

                modal.addEventListener(
                    'click',
                    function (event) {

                        if (event.target === modal) {
                            cerrar(modal.id);
                        }

                    }
                );

            });


            /*
            |--------------------------------------------------------------------------
            | Cerrar con Escape
            |--------------------------------------------------------------------------
            */

            document.addEventListener(
                'keydown',
                function (event) {

                    if (event.key === 'Escape') {

                        modales.forEach(function (modal) {

                            if (!modal.classList.contains('oculto')) {
                                cerrar(modal.id);
                            }

                        });

                    }

                }
            );


            /*
            |--------------------------------------------------------------------------
            | AJAX - cargar Caja
            |--------------------------------------------------------------------------
            */

            function cargarCaja(url) {

                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })

                .then(function (response) {

                    if (!response.ok) {

                        throw new Error(
                            'No se pudo cargar la información de Caja.'
                        );

                    }

                    return response.text();

                })

                .then(function (html) {

                    const documento =
                        new DOMParser()
                            .parseFromString(
                                html,
                                'text/html'
                            );


                    /*
                    |--------------------------------------------------------------------------
                    | Nuevas filas
                    |--------------------------------------------------------------------------
                    */

                    const nuevasFilasMovimientos =
                        documento.querySelector(
                            '#filas-movimientos-caja-ajax'
                        );

                    const nuevasFilasCierres =
                        documento.querySelector(
                            '#filas-cierres-caja-ajax'
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | Nueva paginación
                    |--------------------------------------------------------------------------
                    */

                    const nuevaPaginacionMovimientos =
                        documento.querySelector(
                            '#paginacion-movimientos-caja'
                        );

                    const nuevaPaginacionCierres =
                        documento.querySelector(
                            '#paginacion-cierres-caja'
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | Nuevos datos JSON
                    |--------------------------------------------------------------------------
                    */

                    const nuevosDatosMovimientos =
                        documento.querySelector(
                            '#datos-movimientos-caja-ajax'
                        );

                    const nuevosDatosCierres =
                        documento.querySelector(
                            '#datos-cierres-caja-ajax'
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | Actualizar movimientos
                    |--------------------------------------------------------------------------
                    */

                    if (nuevasFilasMovimientos) {

                        filasMovimientos.innerHTML =
                            nuevasFilasMovimientos.innerHTML;

                    }

                    if (nuevaPaginacionMovimientos) {

                        paginacionMovimientos.innerHTML =
                            nuevaPaginacionMovimientos.innerHTML;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Actualizar cierres
                    |--------------------------------------------------------------------------
                    */

                    if (nuevasFilasCierres) {

                        filasCierres.innerHTML =
                            nuevasFilasCierres.innerHTML;

                    }

                    if (nuevaPaginacionCierres) {

                        paginacionCierres.innerHTML =
                            nuevaPaginacionCierres.innerHTML;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Actualizar datos
                    |--------------------------------------------------------------------------
                    */

                    if (nuevosDatosMovimientos) {

                        movimientos =
                            JSON.parse(
                                nuevosDatosMovimientos.textContent
                            );

                    }

                    if (nuevosDatosCierres) {

                        cierres =
                            JSON.parse(
                                nuevosDatosCierres.textContent
                            );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Actualizar URL
                    |--------------------------------------------------------------------------
                    */

                    history.pushState(
                        {},
                        '',
                        url
                    );

                })

                .catch(function (error) {

                    console.error(error);

                });

            }


            /*
            |--------------------------------------------------------------------------
            | Paginación movimientos
            |--------------------------------------------------------------------------
            */

            paginacionMovimientos.addEventListener(
                'click',
                function (event) {

                    const enlace =
                        event.target.closest('a');

                    if (!enlace) {
                        return;
                    }

                    event.preventDefault();

                    cargarCaja(
                        enlace.href
                    );

                }
            );


            /*
            |--------------------------------------------------------------------------
            | Paginación cierres
            |--------------------------------------------------------------------------
            */

            paginacionCierres.addEventListener(
                'click',
                function (event) {

                    const enlace =
                        event.target.closest('a');

                    if (!enlace) {
                        return;
                    }

                    event.preventDefault();

                    cargarCaja(
                        enlace.href
                    );

                }
            );


            /*
            |--------------------------------------------------------------------------
            | Buscar y filtrar movimientos
            |--------------------------------------------------------------------------
            */

            function aplicarFiltros() {

                const parametros =
                    new URLSearchParams(
                        window.location.search
                    );

                const texto =
                    busqueda.value.trim();

                const tipo =
                    filtroTipo.value;

                const medio =
                    filtroMedio.value;


                /*
                |--------------------------------------------------------------------------
                | Texto
                |--------------------------------------------------------------------------
                */

                if (texto !== '') {

                    parametros.set(
                        'buscar_movimiento',
                        texto
                    );

                } else {

                    parametros.delete(
                        'buscar_movimiento'
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | Tipo
                |--------------------------------------------------------------------------
                */

                if (tipo !== 'Todos') {

                    parametros.set(
                        'tipo_movimiento',
                        tipo
                    );

                } else {

                    parametros.delete(
                        'tipo_movimiento'
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | Medio
                |--------------------------------------------------------------------------
                */

                if (medio !== 'Todos') {

                    parametros.set(
                        'medio_movimiento',
                        medio
                    );

                } else {

                    parametros.delete(
                        'medio_movimiento'
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | Volver a página 1 de movimientos
                |--------------------------------------------------------------------------
                */

                parametros.delete(
                    'movimientos_page'
                );


                const query =
                    parametros.toString();

                const url =
                    query
                        ? '/caja?' + query
                        : '/caja';


                cargarCaja(url);

            }


            /*
            |--------------------------------------------------------------------------
            | Búsqueda con espera
            |--------------------------------------------------------------------------
            */

            let tiempoBusqueda;

            busqueda.addEventListener(
                'input',
                function () {

                    clearTimeout(
                        tiempoBusqueda
                    );

                    tiempoBusqueda =
                        setTimeout(
                            aplicarFiltros,
                            300
                        );

                }
            );


            /*
            |--------------------------------------------------------------------------
            | Filtros
            |--------------------------------------------------------------------------
            */

            filtroTipo.addEventListener(
                'change',
                aplicarFiltros
            );

            filtroMedio.addEventListener(
                'change',
                aplicarFiltros
            );


            /*
            |--------------------------------------------------------------------------
            | Detalle de movimiento
            |--------------------------------------------------------------------------
            */

            filasMovimientos.addEventListener(
                'click',
                function (event) {

                    const boton =
                        event.target.closest(
                            '[data-movimiento]'
                        );

                    if (!boton) {
                        return;
                    }


                    const movimiento =
                        movimientos.find(
                            function (item) {

                                return String(item.id)
                                    === boton.dataset.movimiento;

                            }
                        );


                    if (!movimiento) {
                        return;
                    }


                    const tipo =
                        movimiento.tipo === 'ingreso'
                            ? 'Ingreso'
                            : 'Egreso';


                    const medio =
                        movimiento.medio.charAt(0).toUpperCase()
                        + movimiento.medio.slice(1);


                    const contenido =
                        document.getElementById(
                            'contenido-detalle-movimiento-caja'
                        );


                    contenido.textContent = '';


                    const detalles = [

                        [
                            'Concepto',
                            movimiento.concepto
                        ],

                        [
                            'Fecha',
                            movimiento.fecha
                        ],

                        [
                            'Tipo',
                            tipo
                        ],

                        [
                            'Medio',
                            medio
                        ],

                        [
                            'Monto',
                            dinero(movimiento.monto)
                        ],

                        [
                            'Observación',
                            movimiento.observacion
                                || 'Sin observación'
                        ]

                    ];


                    detalles.forEach(
                        function (detalle) {

                            const parrafo =
                                document.createElement('p');

                            const etiqueta =
                                document.createElement('strong');

                            etiqueta.textContent =
                                detalle[0] + ': ';

                            parrafo.append(
                                etiqueta,
                                detalle[1]
                            );

                            contenido.appendChild(
                                parrafo
                            );

                        }
                    );


                    abrir(
                        'modal-detalle-movimiento-caja'
                    );

                }
            );


            /*
            |--------------------------------------------------------------------------
            | Detalle de cierre
            |--------------------------------------------------------------------------
            */

            filasCierres.addEventListener(
                'click',
                function (event) {

                    const boton =
                        event.target.closest(
                            '[data-cierre]'
                        );

                    if (!boton) {
                        return;
                    }


                    const cierre =
                        cierres.find(
                            function (item) {

                                return String(item.id)
                                    === boton.dataset.cierre;

                            }
                        );


                    if (!cierre) {
                        return;
                    }


                    const contenido =
                        document.getElementById(
                            'contenido-detalle-cierre-caja'
                        );


                    contenido.textContent = '';


                    const detalles = [

                        [
                            'Fecha',
                            cierre.fecha
                        ],

                        [
                            'Saldo inicial',
                            dinero(cierre.inicial)
                        ],

                        [
                            'Total de ingresos',
                            dinero(cierre.ingresos)
                        ],

                        [
                            'Total de egresos',
                            dinero(cierre.egresos)
                        ],

                        [
                            'Saldo esperado',
                            dinero(cierre.esperado)
                        ],

                        [
                            'Dinero contado',
                            dinero(cierre.contado)
                        ],

                        [
                            'Diferencia',
                            dinero(cierre.diferencia)
                        ],

                        [
                            'Estado',
                            cierre.estado
                        ]

                    ];


                    detalles.forEach(
                        function (detalle) {

                            const parrafo =
                                document.createElement('p');

                            const etiqueta =
                                document.createElement('strong');

                            etiqueta.textContent =
                                detalle[0] + ': ';

                            parrafo.append(
                                etiqueta,
                                detalle[1]
                            );

                            contenido.appendChild(
                                parrafo
                            );

                        }
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | Movimientos del cierre
                    |--------------------------------------------------------------------------
                    */

                    const titulo =
                        document.createElement('h3');

                    titulo.textContent =
                        'Movimientos de la jornada';

                    contenido.appendChild(
                        titulo
                    );


                    const tabla =
                        document.createElement('table');

                    tabla.className =
                        'caja-tabla';


                    tabla.innerHTML = `
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Concepto</th>
                                <th>Tipo</th>
                                <th>Medio</th>
                                <th>Monto</th>
                                <th>Observación</th>
                            </tr>
                        </thead>
                    `;


                    const cuerpo =
                        document.createElement('tbody');


                    cierre.movimientos.forEach(
                        function (movimiento) {

                            const fila =
                                document.createElement('tr');


                            const tipo =
                                movimiento.tipo === 'ingreso'
                                    ? 'Ingreso'
                                    : 'Egreso';


                            const medio =
                                movimiento.medio
                                    .charAt(0)
                                    .toUpperCase()
                                + movimiento.medio.slice(1);


                            const valores = [

                                movimiento.fecha,

                                movimiento.concepto,

                                tipo,

                                medio,

                                dinero(movimiento.monto),

                                movimiento.observacion || ''

                            ];


                            valores.forEach(
                                function (valor) {

                                    const celda =
                                        document.createElement('td');

                                    celda.textContent =
                                        valor;

                                    fila.appendChild(
                                        celda
                                    );

                                }
                            );


                            cuerpo.appendChild(
                                fila
                            );

                        }
                    );


                    if (!cierre.movimientos.length) {

                        const fila =
                            document.createElement('tr');


                        fila.innerHTML = `
                            <td colspan="6" class="caja-vacia">
                                No hay movimientos asociados a este cierre.
                            </td>
                        `;


                        cuerpo.appendChild(
                            fila
                        );

                    }


                    tabla.appendChild(
                        cuerpo
                    );


                    contenido.appendChild(
                        tabla
                    );


                    abrir(
                        'modal-detalle-cierre-caja'
                    );

                }
            );


            /*
            |--------------------------------------------------------------------------
            | Diferencia del cierre
            |--------------------------------------------------------------------------
            */

            if (dineroContado) {

                dineroContado.addEventListener(
                    'input',
                    actualizarDiferencia
                );

            }


            /*
            |--------------------------------------------------------------------------
            | Estado inicial
            |--------------------------------------------------------------------------
            */

            actualizarDiferencia();

        })();

    </script>

@endsection