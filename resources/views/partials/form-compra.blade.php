@php
    $detalle = $compra?->detalles->first();

    $formasPago = [
        'efectivo' => 'Efectivo',
        'tarjeta' => 'Tarjeta',
        'transferencia' => 'Transferencia',
        'cuenta_corriente' => 'Cuenta corriente',
    ];

    $pagosPorForma = $compra
        ? $compra->pagos->keyBy('forma_pago')
        : collect();

    $productoSeleccionado = old(
        'producto_id',
        $detalle?->producto_id ?? 'otro'
    );

    /*
     * Saldos a favor de cada proveedor.
     * Viene del CompraController.
     */
    $saldosAFavor = $saldosAFavor ?? [];
@endphp

<style>
    .formulario-compra {
        max-width: 760px;
    }

    .grupo-compra {
        margin: 0 0 24px;
        padding: 20px;
        border: 1px solid #ddd;
        background: #fafafa;
    }

    .campo-compra {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-bottom: 16px;
    }

    .campo-compra:last-child {
        margin-bottom: 0;
    }

    .campo-compra input,
    .campo-compra select {
        max-width: 500px;
        padding: 9px 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font: inherit;
    }

    .campo-precio-compra,
    .campo-monto-pago {
        position: relative;
        max-width: 500px;
    }

    .campo-precio-compra::before,
    .campo-monto-pago::before {
        position: absolute;
        left: 10px;
        bottom: 10px;
        z-index: 1;
        color: #555;
        content: '$';
    }

    .campo-precio-compra input,
    .campo-monto-pago input {
        width: 100%;
        padding-left: 26px;
    }

    .boton-compra {
        display: inline-block; 
        padding: 11px 16px; 
        background-color:  #2563eb; 
        color: black; 
        text-decoration: none; 
        border: 0; 
        border-radius: 6px; 
        cursor: pointer; 
        font-size: 14px; 
        margin-right: 5px; 
        font-weight: bold; 
        transition: background-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;   
    }

    .boton-compra:hover {
        background: #1d4ed8 !important;
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3) !important;
        transform: translateY(-2px) !important;
    }

    .errores-formulario {
        margin-bottom: 24px;
        padding: 14px 18px;
        border: 1px solid #dc3545;
        background: #f8d7da;
        color: #842029;
    }

    .errores-formulario p {
        margin: 0 0 6px;
    }

    .formas-pago-opciones {
        display: grid;
        gap: 10px;
    }

    .forma-pago-opcion {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .forma-pago-opcion input {
        width: 17px;
        height: 17px;
        margin: 0;
    }

    .error-forma-pago {
        display: none;
        margin-top: 8px;
        color: #842029;
    }

    .error-forma-pago.visible {
        display: block;
    }

    .montos-pago.oculto,
    .monto-pago.oculto {
        display: none;
    }

    .selector-compra-linea {
        display: flex;
        align-items: flex-end;
        gap: 10px;
    }

    .selector-compra-linea .campo-compra {
        flex: 1;
    }

    .selector-compra-linea .boton-compra {
        margin-bottom: 16px;
        white-space: nowrap;
    }

    /* Saldo a favor */

    .saldo-favor-compra {
        display: none;
        margin: 0 0 20px;
        padding: 14px 16px;
        border: 1px solid #198754;
        background: #d1e7dd;
        color: #0f5132;
        border-radius: 4px;
    }

    .saldo-favor-compra.visible {
        display: block;
    }

    .saldo-favor-compra strong {
        font-size: 18px;
    }

    .resumen-compra {
        margin-top: 20px;
        padding: 16px;
        border: 1px solid #ddd;
        background: #fff;
    }

    .resumen-compra-linea {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 8px;
    }

    .resumen-compra-linea.total {
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid #ddd;
        font-size: 18px;
        font-weight: bold;
    }

    .resumen-compra-linea.saldo {
        color: #198754;
    }

    .aviso-compra-sin-pago {
        display: none;
        margin-top: 12px;
        padding: 12px;
        border: 1px solid #198754;
        background: #d1e7dd;
        color: #0f5132;
        border-radius: 4px;
    }

    .aviso-compra-sin-pago.visible {
        display: block;
    }

    .monto-pago-info {
        padding: 10px 12px;
        margin-top: 10px;
        border: 1px solid #ddd;
        background: #fff;
        max-width: 500px;
    }
</style>

@if ($errors->any())
    <div class="errores-formulario">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

<form class="formulario-compra" method="POST" action="{{ $action }}">
    @csrf

    @if ($compra)
        <input type="hidden" name="_method" value="PUT">
    @endif

    <fieldset class="grupo-compra">
        <legend>Información de la compra</legend>

        <div class="campo-compra">
            <label for="fecha-compra">
                Fecha
            </label>

            <input
                type="date"
                id="fecha-compra"
                name="fecha"
                value="{{ old('fecha', $compra?->fecha?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
                required
            >
        </div>

        <div class="selector-compra-linea">
            <div class="campo-compra">
                <label for="proveedor-compra">
                    Proveedor (opcional)
                </label>

                <select
                    id="proveedor-compra"
                    name="proveedor_id"
                >
                    <option value="">
                        Sin proveedor
                    </option>

                    @foreach ($proveedores as $proveedor)
                        <option
                            value="{{ $proveedor->id }}"
                            {{ (string) old('proveedor_id', $compra?->proveedor_id) === (string) $proveedor->id ? 'selected' : '' }}
                        >
                            {{ $proveedor->empresa }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button
                type="button"
                class="boton-compra"
                id="abrir-nuevo-proveedor-compra"
            >
                + Nuevo proveedor
            </button>
        </div>

        {{-- Saldo a favor del proveedor --}}
        <div
            id="saldo-favor-compra"
            class="saldo-favor-compra"
        >
            <div>
                <strong>Saldo a favor disponible</strong>
            </div>

            <div>
                Tenés
                <strong id="saldo-favor-compra-importe">$0,00</strong>
                a favor con este proveedor.
            </div>

            <div style="margin-top: 6px;">
                Se aplicará automáticamente a esta compra.
            </div>
        </div>
    </fieldset>

    <fieldset class="grupo-compra">
        <legend>Producto</legend>

        <div class="selector-compra-linea">
            <div class="campo-compra">
                <label for="producto-compra">
                    Producto
                </label>

                <select
                    id="producto-compra"
                    name="producto_id"
                    required
                >
                    <option
                        value="otro"
                        {{ $productoSeleccionado === 'otro' ? 'selected' : '' }}
                    >
                        Otro
                    </option>

                    @foreach ($productos as $producto)
                        <option
                            value="{{ $producto->id }}"
                            data-precio="{{ $producto->precio_compra }}"
                            {{ (string) $productoSeleccionado === (string) $producto->id ? 'selected' : '' }}
                        >
                            {{ $producto->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button
                type="button"
                class="boton-compra"
                id="abrir-nuevo-producto-compra"
            >
                + Nuevo producto
            </button>
        </div>

        <div class="campo-compra">
            <label for="cantidad-compra">
                Cantidad
            </label>

            <input
                type="number"
                id="cantidad-compra"
                name="cantidad"
                min="1"
                step="1"
                value="{{ old('cantidad', $detalle?->cantidad ?? 1) }}"
                required
            >
        </div>

        <div class="campo-compra campo-precio-compra">
            <label for="precio-compra">
                Precio de compra
            </label>

            <input
                type="number"
                id="precio-compra"
                name="precio"
                min="0"
                step="0.01"
                value="{{ old('precio', $detalle?->precio ?? 0) }}"
                required
            >
        </div>

        {{-- Resumen --}}
        <div class="resumen-compra">
            <div class="resumen-compra-linea">
                <span>Subtotal</span>

                <strong id="resumen-subtotal-compra">
                    $0,00
                </strong>
            </div>

            <div
                class="resumen-compra-linea saldo"
                id="linea-saldo-favor-compra"
                style="display: none;"
            >
                <span>Saldo a favor utilizado</span>

                <strong id="resumen-saldo-favor-compra">
                    -$0,00
                </strong>
            </div>

            <div class="resumen-compra-linea total">
                <span>Resta pagar</span>

                <strong id="resumen-total-compra">
                    $0,00
                </strong>
            </div>

            <div
                id="aviso-compra-sin-pago"
                class="aviso-compra-sin-pago"
            >
                El saldo a favor cubre el total de la compra.
                No es necesario registrar ningún pago adicional.
            </div>
        </div>
    </fieldset>

    <fieldset class="grupo-compra">
        <legend>Forma de pago</legend>

        <div class="formas-pago-opciones">
            <div class="campo-compra">
                <span>Formas de pago</span>

                <div
                    class="formas-pago-opciones"
                    id="formas-pago-compra"
                >
                    @foreach ($formasPago as $valor => $etiqueta)
                        @php
                            $pago = $pagosPorForma->get($valor);

                            $seleccionada = old('formas_pago')
                                ? in_array(
                                    $valor,
                                    old('formas_pago'),
                                    true
                                )
                                : $pago !== null;
                        @endphp

                        <label class="forma-pago-opcion">
                            <input
                                type="checkbox"
                                name="formas_pago[]"
                                value="{{ $valor }}"
                                {{ $seleccionada ? 'checked' : '' }}
                            >

                            <span>{{ $etiqueta }}</span>
                        </label>

                        <div
                            class="campo-compra campo-monto-pago monto-pago oculto"
                            data-forma-pago="{{ $valor }}"
                        >
                            <label for="monto-{{ $valor }}">
                                Monto en {{ $etiqueta }}
                            </label>

                            <input
                                type="number"
                                id="monto-{{ $valor }}"
                                name="montos_pago[{{ $valor }}]"
                                min="0"
                                step="0.01"
                                value="{{ old('montos_pago.' . $valor, $pago?->monto ?? '') }}"
                            >
                        </div>
                    @endforeach
                </div>

                <div
                    id="error-forma-pago-compra"
                    class="error-forma-pago"
                >
                    Seleccione al menos una forma de pago.
                </div>

                <div
                    id="aviso-formas-no-necesarias"
                    class="monto-pago-info"
                    style="display: none;"
                >
                    No se necesita una forma de pago porque el saldo a
                    favor cubre toda la compra.
                </div>
            </div>
        </div>
    </fieldset>

    <button type="submit" class="boton-compra">
        {{ $compra ? 'Guardar cambios' : 'Guardar compra' }}
    </button>

    <a href="/compras" class="boton-compra">
        Cancelar
    </a>
</form>

@include('partials.modal-altas-rapidas', [
    'mostrarProveedor' => true,
    'mostrarProducto' => true,
    'campoPrecioProducto' => 'precio_compra',
    'selectorProducto' => 'producto-compra',
    'selectorProveedor' => 'proveedor-compra',
])

{{-- Datos de Laravel para JavaScript --}}
<script
    type="application/json"
    id="saldos-afavor-proveedores"
>
    {!! json_encode($saldosAFavor) !!}
</script>

<script>
    const selectorProductoCompra =
        document.getElementById('producto-compra');

    const precioProductoCompra =
        document.getElementById('precio-compra');

    const selectorProveedorCompra =
        document.getElementById('proveedor-compra');

    const cantidadCompra =
        document.getElementById('cantidad-compra');

    const opcionesPagoCompra =
        document.querySelectorAll(
            '#formas-pago-compra input[name="formas_pago[]"]'
        );

    const montosPagoCompra =
        document.querySelectorAll('.monto-pago');

    const errorFormaPagoCompra =
        document.getElementById(
            'error-forma-pago-compra'
        );

    const saldoFavorCompra =
        document.getElementById(
            'saldo-favor-compra'
        );

    const saldoFavorCompraImporte =
        document.getElementById(
            'saldo-favor-compra-importe'
        );

    const resumenSubtotalCompra =
        document.getElementById(
            'resumen-subtotal-compra'
        );

    const lineaSaldoFavorCompra =
        document.getElementById(
            'linea-saldo-favor-compra'
        );

    const resumenSaldoFavorCompra =
        document.getElementById(
            'resumen-saldo-favor-compra'
        );

    const resumenTotalCompra =
        document.getElementById(
            'resumen-total-compra'
        );

    const avisoCompraSinPago =
        document.getElementById(
            'aviso-compra-sin-pago'
        );

    const avisoFormasNoNecesarias =
        document.getElementById(
            'aviso-formas-no-necesarias'
        );

    /*
     * Saldos enviados por CompraController.
     *
     * Ejemplo:
     * {
     *   1: 30000,
     *   2: 0,
     *   3: 15000
     * }
     */
    const saldosAFavorProveedores = JSON.parse(
        document.getElementById(
            'saldos-afavor-proveedores'
        ).textContent
    );

    function formatearPesos(valor) {
        return new Intl.NumberFormat(
            'es-AR',
            {
                style: 'currency',
                currency: 'ARS'
            }
        ).format(valor || 0);
    }

    function obtenerSaldoFavorProveedor() {
        const proveedorId =
            selectorProveedorCompra.value;

        if (!proveedorId) {
            return 0;
        }

        return Number(
            saldosAFavorProveedores[proveedorId] || 0
        );
    }

    function obtenerDatosCompra() {
        const cantidad =
            Number(cantidadCompra.value) || 0;

        const precio =
            Number(precioProductoCompra.value) || 0;

        const subtotal =
            cantidad * precio;

        const saldoFavor =
            obtenerSaldoFavorProveedor();

        const saldoFavorUsado =
            Math.min(
                saldoFavor,
                subtotal
            );

        const restante =
            Math.max(
                0,
                subtotal - saldoFavorUsado
            );

        return {
            cantidad,
            precio,
            subtotal,
            saldoFavor,
            saldoFavorUsado,
            restante
        };
    }

    function actualizarSaldoFavorProveedor() {
        const saldoFavor =
            obtenerSaldoFavorProveedor();

        if (saldoFavor > 0) {
            saldoFavorCompra.classList.add('visible');

            saldoFavorCompraImporte.textContent =
                formatearPesos(saldoFavor);
        } else {
            saldoFavorCompra.classList.remove('visible');

            saldoFavorCompraImporte.textContent =
                '$0,00';
        }

        actualizarResumenCompra();
    }

    function actualizarResumenCompra() {
        const datos =
            obtenerDatosCompra();

        resumenSubtotalCompra.textContent =
            formatearPesos(datos.subtotal);

        if (datos.saldoFavorUsado > 0) {
            lineaSaldoFavorCompra.style.display =
                'flex';

            resumenSaldoFavorCompra.textContent =
                '-' +
                formatearPesos(
                    datos.saldoFavorUsado
                );
        } else {
            lineaSaldoFavorCompra.style.display =
                'none';

            resumenSaldoFavorCompra.textContent =
                '-$0,00';
        }

        resumenTotalCompra.textContent =
            formatearPesos(datos.restante);

        if (datos.restante <= 0) {
            avisoCompraSinPago.classList.add(
                'visible'
            );

            avisoFormasNoNecesarias.style.display =
                'block';
        } else {
            avisoCompraSinPago.classList.remove(
                'visible'
            );

            avisoFormasNoNecesarias.style.display =
                'none';
        }

        actualizarMontosCompra();
    }

    function actualizarPrecioCompra() {
        const opcion =
            selectorProductoCompra
                .selectedOptions[0];

        if (
            selectorProductoCompra.value !== 'otro'
            && opcion
        ) {
            precioProductoCompra.value =
                opcion.dataset.precio;
        }

        actualizarResumenCompra();
    }

    function actualizarMontosCompra() {
        const seleccionadas =
            Array.from(
                opcionesPagoCompra
            ).filter(function (opcion) {
                return opcion.checked;
            });

        const datos =
            obtenerDatosCompra();

        /*
         * Si el saldo a favor cubre toda la compra,
         * no hacen falta formas de pago.
         */
        if (datos.restante <= 0) {
            montosPagoCompra.forEach(
                function (campo) {
                    campo.classList.add('oculto');

                    const input =
                        campo.querySelector('input');

                    input.required = false;
                }
            );

            errorFormaPagoCompra.classList.remove(
                'visible'
            );

            return;
        }

        const multiples =
            seleccionadas.length > 1;

        /*
         * Si no hay ninguna forma seleccionada,
         * mostramos el error solamente cuando
         * realmente queda algo por pagar.
         */
        errorFormaPagoCompra.classList.toggle(
            'visible',
            seleccionadas.length === 0
        );

        montosPagoCompra.forEach(
            function (campo) {
                const checkbox =
                    document.querySelector(
                        'input[name="formas_pago[]"][value="' +
                        campo.dataset.formaPago +
                        '"]'
                    );

                const mostrar =
                    multiples &&
                    checkbox &&
                    checkbox.checked;

                campo.classList.toggle(
                    'oculto',
                    !mostrar
                );

                const input =
                    campo.querySelector('input');

                input.required = mostrar;

                /*
                 * Si solamente hay una forma de pago,
                 * el backend le asigna automáticamente
                 * el importe restante.
                 */
            }
        );
    }

    selectorProductoCompra.addEventListener(
        'change',
        actualizarPrecioCompra
    );

    selectorProveedorCompra.addEventListener(
        'change',
        actualizarSaldoFavorProveedor
    );

    cantidadCompra.addEventListener(
        'input',
        actualizarResumenCompra
    );

    precioProductoCompra.addEventListener(
        'input',
        actualizarResumenCompra
    );

    opcionesPagoCompra.forEach(
        function (opcion) {
            opcion.addEventListener(
                'change',
                actualizarMontosCompra
            );
        }
    );

    document
        .querySelector('.formulario-compra')
        .addEventListener(
            'submit',
            function (event) {
                const datos =
                    obtenerDatosCompra();

                const hayFormaDePago =
                    Array.from(
                        opcionesPagoCompra
                    ).some(function (opcion) {
                        return opcion.checked;
                    });

                /*
                 * Si todavía queda dinero por pagar,
                 * tiene que existir al menos una forma.
                 *
                 * Si el saldo a favor cubre todo,
                 * no hace falta ninguna.
                 */
                const formularioValido =
                    datos.restante <= 0
                    || hayFormaDePago;

                errorFormaPagoCompra.classList.toggle(
                    'visible',
                    !formularioValido
                );

                if (!formularioValido) {
                    event.preventDefault();
                }
            }
        );

    actualizarPrecioCompra();
    actualizarSaldoFavorProveedor();
    actualizarMontosCompra();
</script>