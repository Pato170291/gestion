@extends('layouts.app')

@section('title', 'Nueva venta')

@section('content')

    <style>
        .formulario-venta {
            max-width: 760px;
        }

        .grupo-venta {
            margin: 0 0 24px;
            padding: 20px;
            border: 1px solid #dddddd;
            background: #fafafa;
        }

        .campo-venta {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 16px;
        }

        .campo-venta:last-child {
            margin-bottom: 0;
        }

        .campo-venta input,
        .campo-venta select,
        .campo-venta textarea {
            max-width: 500px;
            padding: 9px 10px;
            border: 1px solid #cccccc;
            border-radius: 4px;
            font: inherit;
        }

        .selector-cliente-linea {
            display: flex;
            align-items: flex-end;
            gap: 10px;
        }

        .selector-cliente-linea .campo-venta {
            flex: 1;
        }

        #abrir-nuevo-producto {
            margin-top: 14px;
        }

        #abrir-nuevo-cliente {
            margin-bottom: 16px;
            white-space: nowrap;
        }

        .boton-venta,
        .boton-cliente-modal {
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

        .boton-venta:hover,
        .boton-cliente-modal:hover {
            background: #1d4ed8 !important;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3) !important;
            transform: translateY(-2px) !important;
        }

        .mensaje-cliente-exito,
        .mensaje-producto-exito {
            position: fixed;
            top: 20px;
            right: 20px;
            display: block;
            padding: 15px 20px;
            background: #28a745;
            border: 0;
            border-radius: 8px;
            color: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }

        .mensaje-cliente-exito {
            display: none;
        }

        .mensaje-cliente-exito.visible,
        .errores-cliente-modal.visible {
            display: block;
        }

        .modal-cliente {
            position: fixed;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: rgba(0, 0, 0, 0.35);
            z-index: 900;
        }

        .modal-cliente.oculto {
            display: none;
        }

        .modal-cliente-contenido {
            width: min(620px, 100%);
            padding: 28px;
            background: white;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
        }

        .modal-cliente-encabezado {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .modal-cliente-encabezado h2 {
            margin: 0;
        }

        .cerrar-modal-cliente,
        .cerrar-modal-producto {
            margin: 0;
            padding: 4px 10px;
            font-size: 22px;
            background: transparent;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .cerrar-modal-cliente:hover,
        .cerrar-modal-producto:hover {
            background: #1d4ed8 !important;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3) !important;
            transform: translateY(-2px) !important;
        }

        .campo-cliente-modal {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 16px;
        }

        .campo-cliente-modal input {
            max-width: 500px;
            padding: 9px 10px;
            border: 1px solid #cccccc;
            border-radius: 4px;
            font: inherit;
        }

        .errores-cliente-modal {
            display: none;
            margin-bottom: 18px;
            padding: 12px 16px;
            background: #f8d7da;
            border: 1px solid #f1aeb5;
            color: #842029;
        }

        .mensaje-producto-exito,
        .errores-producto-modal {
            display: none;
            margin-bottom: 18px;
            padding: 12px 16px;
        }

        .mensaje-producto-exito.visible {
            display: block;
            margin-bottom: 0;
        }

        .errores-producto-modal.visible {
            display: block;
            background: #f8d7da;
            border: 1px solid #f1aeb5;
            color: #842029;
        }

        .fila-producto-venta {
            display: grid;
            grid-template-columns: minmax(180px, 1fr) 120px 150px 150px;
            gap: 12px;
            align-items: start;
        }

        .fila-producto-venta .campo-venta {
            margin-bottom: 0;
        }

        .resumen-venta {
            margin-top: 20px;
            margin-left: auto;
            max-width: 320px;
        }

        .linea-resumen-venta {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            padding: 6px 0;
            font-size: 17px;
        }

        .saldo-resumen {
            color: #187a3d;
        }

        .total-resumen-venta {
            margin-top: 6px;
            padding-top: 12px;
            border-top: 2px solid #cccccc;
            font-size: 21px;
            font-weight: bold;
        }

        .linea-resumen-venta.oculto {
            display: none;
        }

        .montos-pago {
            display: grid;
            gap: 12px;
            margin-top: 16px;
        }

        .campo-precio-venta,
        .campo-subtotal-venta,
        .campo-monto-pago {
            position: relative;
        }

        .campo-precio-venta input,
        .campo-subtotal-venta input,
        .campo-monto-pago input {
            padding-left: 26px;
        }

        .campo-precio-venta::before,
        .campo-subtotal-venta::before,
        .campo-monto-pago::before {
            position: absolute;
            left: 9px;
            bottom: 10px;
            z-index: 1;
            color: #555555;
            content: '$';
        }

        .formas-pago-opciones {
            display: grid;
            gap: 10px;
            margin-top: 8px;
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

        .monto-pago-info {
            padding: 12px 14px;
            border: 1px solid #cccccc;
            background: #f7f7f7;
            border-radius: 4px;
            font-size: 16px;
        }

        .monto-pago-info strong {
            font-size: 18px;
        }

        .aviso-pago-cero {
            padding: 10px 14px;
            border: 1px solid #b7dfc2;
            background: #e9f7ed;
            color: #187a3d;
            border-radius: 4px;
        }

        @media (max-width: 700px) {
            .fila-producto-venta {
                grid-template-columns: 1fr 1fr;
            }

            .selector-cliente-linea {
                align-items: stretch;
                flex-direction: column;
            }

            #abrir-nuevo-cliente {
                align-self: flex-start;
                margin-bottom: 0;
            }
        }

        .modal-producto-contenido {
            width: min(760px, 100%);
            max-height: 90vh;
            overflow-y: auto;
        }

        .grupo-producto-modal {
            margin: 0 0 18px;
            padding: 16px;
            border: 1px solid #dddddd;
            background: #fafafa;
        }

        .campo-producto-modal {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 14px;
        }

        .campo-producto-modal:last-child {
            margin-bottom: 0;
        }

        .campo-producto-modal input,
        .campo-producto-modal select,
        .campo-producto-modal textarea {
            width: 100%;
            max-width: 500px;
            padding: 9px 10px;
            border: 1px solid #cccccc;
            border-radius: 4px;
            font: inherit;
        }

        .campo-producto-modal textarea {
            min-height: 70px;
            resize: vertical;
        }

        .vencimiento-producto-modal.oculto {
            display: none;
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

        .saldo-favor-cliente {
            margin-top: -8px;
            margin-bottom: 16px;
            padding: 10px 14px;
            border: 1px solid #b7dfc2;
            background: #e9f7ed;
            color: #187a3d;
            font-weight: 600;
            border-radius: 4px;
        }

        .saldo-favor-cliente.oculto {
            display: none;
        }
    </style>

    <h1>Nueva venta</h1>

    @if ($errors->any())
        <div class="errores-formulario">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form class="formulario-venta" id="form-nueva-venta" method="POST" action="/ventas">
        @csrf

        <fieldset class="grupo-venta">
            <legend>Información de la venta</legend>

            <div class="campo-venta">
                <label for="fecha-venta">Fecha</label>
                <input
                    type="date"
                    id="fecha-venta"
                    name="fecha"
                    value="{{ now()->format('Y-m-d') }}"
                >
            </div>

            <div class="selector-cliente-linea">
                <div class="campo-venta">
                    <label for="cliente-venta">Cliente (opcional)</label>

                    <select id="cliente-venta" name="cliente_id">
                        <option value="">No es cliente</option>

                        @foreach ($clientes as $cliente)
                            <option
                                value="{{ $cliente->id }}"
                                data-saldo-a-favor="{{ $cliente->saldo_a_favor }}"
                            >
                                {{ $cliente->nombre }} {{ $cliente->apellido }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button
                    type="button"
                    class="boton-venta"
                    id="abrir-nuevo-cliente"
                >
                    + Nuevo cliente
                </button>
            </div>

            <div id="saldo-favor-cliente" class="saldo-favor-cliente oculto"></div>
        </fieldset>


        <fieldset class="grupo-venta">
            <legend>Productos</legend>

            <div class="fila-producto-venta">

                <div class="campo-venta">
                    <label for="producto-venta">Producto</label>

                    <select id="producto-venta" name="producto_id" required>
                        <option value="">Seleccione un producto</option>
                        <option value="otro">Otro</option>

                        @foreach ($productos as $producto)
                            <option
                                value="{{ $producto->id }}"
                                data-precio="{{ $producto->precio_venta }}"
                            >
                                {{ $producto->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="campo-venta">
                    <label for="cantidad-venta">Cantidad</label>

                    <input
                        type="number"
                        id="cantidad-venta"
                        name="cantidad"
                        min="1"
                        step="1"
                        value="1"
                        required
                    >
                </div>

                <div class="campo-venta campo-precio-venta">
                    <label for="precio-venta">Precio</label>

                    <input
                        type="number"
                        id="precio-venta"
                        name="precio"
                        min="0"
                        step="0.01"
                        required
                    >
                </div>

                <div class="campo-venta campo-subtotal-venta">
                    <label for="subtotal-venta">Subtotal</label>

                    <input
                        type="text"
                        id="subtotal-venta"
                        name="subtotal"
                        value="$0,00"
                        readonly
                    >
                </div>

            </div>

            <button
                type="button"
                class="boton-venta"
                id="abrir-nuevo-producto"
            >
                + Nuevo producto
            </button>


            <div class="resumen-venta">

                <div class="linea-resumen-venta">
                    <span>Subtotal:</span>
                    <span id="resumen-subtotal">$0,00</span>
                </div>

                <div
                    id="linea-saldo-favor"
                    class="linea-resumen-venta saldo-resumen oculto"
                >
                    <span>Saldo a favor:</span>
                    <span id="resumen-saldo-favor">-$0,00</span>
                </div>

                <div class="linea-resumen-venta total-resumen-venta">
                    <span>Total:</span>
                    <span id="total-venta">$0,00</span>
                </div>

            </div>

        </fieldset>


        <fieldset class="grupo-venta">
            <legend>Forma de pago</legend>

            <div class="campo-venta">

                <span>Formas de pago</span>

                <div
                    class="formas-pago-opciones"
                    id="formas-pago-venta"
                >

                    <label class="forma-pago-opcion">
                        <input
                            type="checkbox"
                            name="formas_pago[]"
                            value="efectivo"
                        >
                        <span>Efectivo</span>
                    </label>

                    <label class="forma-pago-opcion">
                        <input
                            type="checkbox"
                            name="formas_pago[]"
                            value="tarjeta"
                        >
                        <span>Tarjeta</span>
                    </label>

                    <label class="forma-pago-opcion">
                        <input
                            type="checkbox"
                            name="formas_pago[]"
                            value="transferencia"
                        >
                        <span>Transferencia</span>
                    </label>

                    <label class="forma-pago-opcion">
                        <input
                            type="checkbox"
                            name="formas_pago[]"
                            value="cuenta_corriente"
                        >
                        <span>Cuenta corriente</span>
                    </label>

                </div>

                <div
                    id="error-forma-pago"
                    class="error-forma-pago"
                >
                    Seleccione al menos una forma de pago.
                </div>

            </div>

            <div
                id="montos-pago"
                class="montos-pago"
            ></div>

        </fieldset>


        <button
            type="submit"
            class="boton-venta"
            id="guardar-venta"
        >
            Guardar
        </button>

        <a
            href="/ventas"
            class="boton-venta"
        >
            Cancelar
        </a>

    </form>


    {{-- MODAL NUEVO CLIENTE --}}

    <div
        id="modal-nuevo-cliente"
        class="modal-cliente oculto"
        aria-hidden="true"
    >
        <section
            class="modal-cliente-contenido"
            role="dialog"
            aria-modal="true"
            aria-labelledby="nuevo-cliente-titulo"
        >

            <div class="modal-cliente-encabezado">

                <h2 id="nuevo-cliente-titulo">
                    Nuevo cliente
                </h2>

                <button
                    type="button"
                    class="cerrar-modal-cliente"
                    aria-label="Cerrar nuevo cliente"
                >
                    &times;
                </button>

            </div>

            <div
                id="errores-cliente-modal"
                class="errores-cliente-modal"
            ></div>

            <form id="form-nuevo-cliente">

                @csrf

                <div class="campo-cliente-modal">
                    <label for="cliente-nombre">Nombre</label>

                    <input
                        type="text"
                        id="cliente-nombre"
                        name="nombre"
                        required
                    >
                </div>

                <div class="campo-cliente-modal">
                    <label for="cliente-apellido">Apellido</label>

                    <input
                        type="text"
                        id="cliente-apellido"
                        name="apellido"
                        required
                    >
                </div>

                <div class="campo-cliente-modal">
                    <label for="cliente-telefono">Teléfono</label>

                    <input
                        type="text"
                        id="cliente-telefono"
                        name="telefono"
                        required
                    >
                </div>

                <div class="campo-cliente-modal">
                    <label for="cliente-email">Email (opcional)</label>

                    <input
                        type="email"
                        id="cliente-email"
                        name="email"
                    >
                </div>

                <button
                    type="submit"
                    class="boton-cliente-modal"
                >
                    Guardar cliente
                </button>

                <button
                    type="button"
                    class="boton-cliente-modal"
                    id="cerrar-nuevo-cliente"
                >
                    Cancelar
                </button>

            </form>

        </section>
    </div>


    {{-- MODAL NUEVO PRODUCTO --}}

    <div
        id="modal-nuevo-producto"
        class="modal-cliente oculto"
        aria-hidden="true"
    >
        <section
            class="modal-cliente-contenido modal-producto-contenido"
            role="dialog"
            aria-modal="true"
            aria-labelledby="nuevo-producto-titulo"
        >

            <div class="modal-cliente-encabezado">

                <h2 id="nuevo-producto-titulo">
                    Nuevo producto
                </h2>

                <button
                    type="button"
                    class="cerrar-modal-producto"
                    aria-label="Cerrar nuevo producto"
                >
                    &times;
                </button>

            </div>

            <div
                id="errores-producto-modal"
                class="errores-producto-modal"
            ></div>

            <form id="form-nuevo-producto">

                @csrf

                <fieldset class="grupo-producto-modal">

                    <legend>Información</legend>

                    <div class="campo-producto-modal">
                        <label for="producto-nombre">
                            Nombre del producto
                        </label>

                        <input
                            type="text"
                            id="producto-nombre"
                            name="nombre"
                            required
                        >
                    </div>

                    <div class="campo-producto-modal">
                        <label for="producto-marca">
                            Marca (opcional)
                        </label>

                        <input
                            type="text"
                            id="producto-marca"
                            name="marca"
                        >
                    </div>

                    <div class="campo-producto-modal">
                        <label for="producto-descripcion">
                            Descripción (opcional)
                        </label>

                        <textarea
                            id="producto-descripcion"
                            name="descripcion"
                        ></textarea>
                    </div>

                </fieldset>


                <fieldset class="grupo-producto-modal">

                    <legend>Precios y stock</legend>

                    <div class="campo-producto-modal">
                        <label for="producto-precio-compra">
                            Precio de compra
                        </label>

                        <input
                            type="number"
                            id="producto-precio-compra"
                            name="precio_compra"
                            min="0"
                            step="0.01"
                            required
                        >
                    </div>

                    <div class="campo-producto-modal">
                        <label for="producto-precio-venta">
                            Precio de venta (opcional)
                        </label>

                        <input
                            type="number"
                            id="producto-precio-venta"
                            name="precio_venta"
                            min="0"
                            step="0.01"
                        >
                    </div>

                    <div class="campo-producto-modal">
                        <label for="producto-stock-inicial">
                            Cantidad inicial
                        </label>

                        <input
                            type="number"
                            id="producto-stock-inicial"
                            name="cantidad_inicial"
                            min="0"
                            required
                        >
                    </div>

                    <div class="campo-producto-modal">
                        <label for="producto-stock-minimo">
                            Stock mínimo (opcional)
                        </label>

                        <input
                            type="number"
                            id="producto-stock-minimo"
                            name="stock_minimo"
                            min="0"
                            value="1"
                        >
                    </div>

                    <div class="campo-producto-modal">
                        <label for="producto-unidad">
                            Unidad de medida (opcional)
                        </label>

                        <select
                            id="producto-unidad"
                            name="unidad"
                        >
                            <option value="Unidad">Unidad</option>
                            <option value="kg">Kilogramo</option>
                            <option value="litro">Litro</option>
                            <option value="caja">Caja</option>
                        </select>
                    </div>

                </fieldset>


                <fieldset class="grupo-producto-modal">

                    <legend>Proveedor y vencimiento</legend>

                    <div class="campo-producto-modal">
                        <label for="producto-proveedor">
                            Proveedor (opcional)
                        </label>

                        <input
                            type="text"
                            id="producto-proveedor"
                            name="proveedor"
                        >
                    </div>

                    <div class="campo-producto-modal">
                        <label for="producto-tiene-vencimiento">
                            ¿Tiene vencimiento? (opcional)
                        </label>

                        <select
                            id="producto-tiene-vencimiento"
                            name="tiene_vencimiento"
                        >
                            <option value="0">No</option>
                            <option value="1">Sí</option>
                        </select>
                    </div>

                    <div
                        id="vencimiento-producto-modal"
                        class="campo-producto-modal vencimiento-producto-modal oculto"
                    >
                        <label for="producto-fecha-vencimiento">
                            Fecha de vencimiento (opcional)
                        </label>

                        <input
                            type="date"
                            id="producto-fecha-vencimiento"
                            name="fecha_vencimiento"
                            disabled
                        >
                    </div>

                </fieldset>


                <button
                    type="submit"
                    class="boton-cliente-modal"
                >
                    Guardar producto
                </button>

                <button
                    type="button"
                    class="boton-cliente-modal"
                    id="cerrar-nuevo-producto"
                >
                    Cancelar
                </button>

            </form>

        </section>
    </div>


    <script>

        const modalNuevoCliente =
            document.getElementById('modal-nuevo-cliente');

        const selectorClienteVenta =
            document.getElementById('cliente-venta');

        const saldoFavorCliente =
            document.getElementById('saldo-favor-cliente');

        const formularioNuevoCliente =
            document.getElementById('form-nuevo-cliente');

        const erroresClienteModal =
            document.getElementById('errores-cliente-modal');

        const selectorProductoVenta =
            document.getElementById('producto-venta');

        const cantidadVenta =
            document.getElementById('cantidad-venta');

        const precioVenta =
            document.getElementById('precio-venta');

        const subtotalVenta =
            document.getElementById('subtotal-venta');

        const totalVenta =
            document.getElementById('total-venta');

        const formasPagoVenta =
            document.querySelectorAll(
                '#formas-pago-venta input[type="checkbox"]'
            );

        const montosPago =
            document.getElementById('montos-pago');

        const errorFormaPago =
            document.getElementById('error-forma-pago');

        const modalNuevoProducto =
            document.getElementById('modal-nuevo-producto');

        const formularioNuevoProducto =
            document.getElementById('form-nuevo-producto');

        const erroresProductoModal =
            document.getElementById('errores-producto-modal');

        const resumenSubtotal =
            document.getElementById('resumen-subtotal');

        const lineaSaldoFavor =
            document.getElementById('linea-saldo-favor');

        const resumenSaldoFavor =
            document.getElementById('resumen-saldo-favor');


        // ==========================================================
        // FORMATO DE PESOS
        // ==========================================================

        function formatearPesos(valor) {

            return '$' + Number(valor).toLocaleString('es-AR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

        }


        // ==========================================================
        // SALDO A FAVOR DEL CLIENTE
        // ==========================================================

        function obtenerSaldoFavor() {

            const opcion =
                selectorClienteVenta.selectedOptions[0];

            if (!opcion || !selectorClienteVenta.value) {
                return 0;
            }

            return Number(
                opcion.dataset.saldoAFavor || 0
            );

        }


        function actualizarSaldoFavorCliente() {

            const opcion =
                selectorClienteVenta.selectedOptions[0];

            if (!opcion || !selectorClienteVenta.value) {

                saldoFavorCliente.textContent = '';

                saldoFavorCliente.classList.add('oculto');

                actualizarSubtotal();

                return;
            }

            const saldo =
                Number(opcion.dataset.saldoAFavor || 0);

            if (saldo <= 0) {

                saldoFavorCliente.textContent = '';

                saldoFavorCliente.classList.add('oculto');

                actualizarSubtotal();

                return;
            }

            saldoFavorCliente.textContent =
                'Saldo a favor: ' +
                formatearPesos(saldo);

            saldoFavorCliente.classList.remove('oculto');

            actualizarSubtotal();

        }


        // ==========================================================
        // CÁLCULO DE VENTA
        // ==========================================================

        function obtenerDatosVenta() {

            const cantidad =
                Number(cantidadVenta.value) || 0;

            const precio =
                Number(precioVenta.value) || 0;

            const subtotal =
                cantidad * precio;

            const saldoFavor =
                obtenerSaldoFavor();

            const saldoFavorUsado =
                Math.min(saldoFavor, subtotal);

            const total =
                Math.max(0, subtotal - saldoFavorUsado);

            return {
                cantidad: cantidad,
                precio: precio,
                subtotal: subtotal,
                saldoFavor: saldoFavor,
                saldoFavorUsado: saldoFavorUsado,
                total: total
            };

        }


        function actualizarSubtotal() {

            const datos =
                obtenerDatosVenta();

            subtotalVenta.value =
                formatearPesos(datos.subtotal);

            resumenSubtotal.textContent =
                formatearPesos(datos.subtotal);

            if (datos.saldoFavorUsado > 0) {

                lineaSaldoFavor.classList.remove('oculto');

                resumenSaldoFavor.textContent =
                    '-' + formatearPesos(
                        datos.saldoFavorUsado
                    );

            } else {

                lineaSaldoFavor.classList.add('oculto');

                resumenSaldoFavor.textContent =
                    '-$0,00';

            }

            totalVenta.textContent =
                formatearPesos(datos.total);

            actualizarMontosPago();

        }


        // ==========================================================
        // PRECIO AUTOMÁTICO DEL PRODUCTO
        // ==========================================================

        function actualizarPrecioProducto() {

            const opcion =
                selectorProductoVenta.selectedOptions[0];

            const esOtro =
                selectorProductoVenta.value === 'otro';

            const precioCargado =
                opcion ? opcion.dataset.precio : '';

            if (
                !esOtro &&
                precioCargado !== undefined &&
                precioCargado !== ''
            ) {

                precioVenta.value =
                    precioCargado;

                precioVenta.readOnly = true;

            } else {

                if (!esOtro) {
                    precioVenta.value = '';
                }

                precioVenta.readOnly = false;

            }

            actualizarSubtotal();

        }


        // ==========================================================
        // FORMAS DE PAGO
        // ==========================================================

        function actualizarMontosPago() {

            const formasSeleccionadas =
                Array.from(formasPagoVenta)
                    .filter(function (opcion) {
                        return opcion.checked;
                    });

            montosPago.innerHTML = '';

            errorFormaPago.classList.toggle(
                'visible',
                formasSeleccionadas.length === 0
            );

            if (formasSeleccionadas.length === 0) {
                return;
            }

            const datos =
                obtenerDatosVenta();

            /*
             * Si el saldo a favor cubre toda la venta,
             * no necesitamos pedir dinero al cliente.
             */

            if (datos.total <= 0) {

                const aviso =
                    document.createElement('div');

                aviso.className =
                    'aviso-pago-cero';

                aviso.textContent =
                    'El saldo a favor cubre el total de la venta. No se debe ingresar ningún pago adicional.';

                montosPago.appendChild(aviso);

                return;
            }


            /*
             * Una sola forma de pago:
             *
             * El backend asigna automáticamente
             * todo el importe restante a esa forma.
             *
             * Mostramos el monto solamente como información.
             */

            if (formasSeleccionadas.length === 1) {

                const opcion =
                    formasSeleccionadas[0];

                const medio =
                    opcion.parentElement
                        .textContent
                        .trim();

                const campo =
                    document.createElement('div');

                campo.className =
                    'monto-pago-info';

                campo.innerHTML =
                    '<span>Monto en ' +
                    medio +
                    ': </span>' +
                    '<strong>' +
                    formatearPesos(datos.total) +
                    '</strong>';

                montosPago.appendChild(campo);

                return;
            }


            /*
             * Dos o más formas:
             *
             * El usuario reparte manualmente
             * el importe restante.
             */

            formasSeleccionadas.forEach(function (opcion) {

                const campo =
                    document.createElement('div');

                campo.className =
                    'campo-venta campo-monto-pago';

                const medio =
                    opcion.parentElement
                        .textContent
                        .trim();

                campo.innerHTML = `
                    <label for="monto-${opcion.value}">
                        Monto en ${medio}
                    </label>

                    <input
                        type="number"
                        id="monto-${opcion.value}"
                        name="montos_pago[${opcion.value}]"
                        min="0"
                        step="0.01"
                        value="0.00"
                        required
                    >
                `;

                montosPago.appendChild(campo);

            });

        }


        // ==========================================================
        // EVENTOS DE VENTA
        // ==========================================================

        selectorClienteVenta.addEventListener(
            'change',
            function () {

                actualizarSaldoFavorCliente();
                actualizarMontosPago();

            }
        );


        selectorProductoVenta.addEventListener(
            'change',
            actualizarPrecioProducto
        );


        cantidadVenta.addEventListener(
            'input',
            function () {

                actualizarSubtotal();

            }
        );


        precioVenta.addEventListener(
            'input',
            function () {

                actualizarSubtotal();

            }
        );


        formasPagoVenta.forEach(
            function (opcion) {

                opcion.addEventListener(
                    'change',
                    actualizarMontosPago
                );

            }
        );


        // ==========================================================
        // VALIDACIÓN ANTES DE GUARDAR
        // ==========================================================

        document
            .getElementById('form-nueva-venta')
            .addEventListener(
                'submit',
                function (event) {

                    const hayFormaDePago =
                        Array.from(formasPagoVenta)
                            .some(function (opcion) {
                                return opcion.checked;
                            });

                    errorFormaPago.classList.toggle(
                        'visible',
                        !hayFormaDePago
                    );

                    if (!hayFormaDePago) {

                        event.preventDefault();

                        formasPagoVenta[0].focus();

                        return;
                    }


                    /*
                     * Si hay varias formas de pago,
                     * comprobamos que el reparto coincida
                     * con el total restante.
                     */

                    const formasSeleccionadas =
                        Array.from(formasPagoVenta)
                            .filter(function (opcion) {
                                return opcion.checked;
                            });

                    if (formasSeleccionadas.length > 1) {

                        const datos =
                            obtenerDatosVenta();

                        let sumaPagos = 0;

                        formasSeleccionadas.forEach(
                            function (opcion) {

                                const campo =
                                    document.getElementById(
                                        'monto-' + opcion.value
                                    );

                                if (campo) {
                                    sumaPagos +=
                                        Number(campo.value) || 0;
                                }

                            }
                        );

                        sumaPagos =
                            Math.round(
                                sumaPagos * 100
                            ) / 100;

                        const total =
                            Math.round(
                                datos.total * 100
                            ) / 100;


                        if (Math.abs(sumaPagos - total) > 0.01) {

                            event.preventDefault();

                            alert(
                                'Los montos de las formas de pago deben sumar ' +
                                formatearPesos(total) +
                                '. Actualmente suman ' +
                                formatearPesos(sumaPagos) +
                                '.'
                            );

                            return;
                        }

                    }

                }
            );


        // ==========================================================
        // MODAL NUEVO CLIENTE
        // ==========================================================

        function mostrarNuevoCliente() {

            modalNuevoCliente.classList.remove('oculto');

            modalNuevoCliente.setAttribute(
                'aria-hidden',
                'false'
            );

        }


        function ocultarNuevoCliente() {

            modalNuevoCliente.classList.add('oculto');

            modalNuevoCliente.setAttribute(
                'aria-hidden',
                'true'
            );

        }


        document
            .getElementById('abrir-nuevo-cliente')
            .addEventListener(
                'click',
                mostrarNuevoCliente
            );


        document
            .querySelector('.cerrar-modal-cliente')
            .addEventListener(
                'click',
                ocultarNuevoCliente
            );


        document
            .getElementById('cerrar-nuevo-cliente')
            .addEventListener(
                'click',
                ocultarNuevoCliente
            );


        modalNuevoCliente.addEventListener(
            'click',
            function (event) {

                if (event.target === modalNuevoCliente) {
                    ocultarNuevoCliente();
                }

            }
        );


        document.addEventListener(
            'keydown',
            function (event) {

                if (event.key === 'Escape') {

                    ocultarNuevoCliente();

                    ocultarNuevoProducto();

                }

            }
        );


        formularioNuevoCliente.addEventListener(
            'submit',
            function (event) {

                event.preventDefault();

                erroresClienteModal.classList.remove(
                    'visible'
                );

                fetch('/clientes', {

                    method: 'POST',

                    headers: {
                        'X-CSRF-TOKEN':
                            formularioNuevoCliente
                                .querySelector(
                                    'input[name="_token"]'
                                ).value,

                        'X-Requested-With':
                            'XMLHttpRequest',

                        'Accept':
                            'application/json'
                    },

                    body:
                        new FormData(
                            formularioNuevoCliente
                        )

                })

                .then(
                    async function (response) {

                        const datos =
                            await response.json();

                        if (!response.ok) {
                            throw datos;
                        }

                        return datos;

                    }
                )

                .then(
                    function (datos) {

                        const cliente =
                            datos.cliente;

                        const opcion =
                            document.createElement(
                                'option'
                            );

                        opcion.value =
                            cliente.id;

                        opcion.textContent =
                            `${cliente.nombre} ${cliente.apellido}`;

                        /*
                         * Un cliente recién creado
                         * comienza sin saldo a favor.
                         */

                        opcion.dataset.saldoAFavor =
                            '0';

                        opcion.selected =
                            true;

                        selectorClienteVenta
                            .appendChild(opcion);

                        formularioNuevoCliente.reset();

                        ocultarNuevoCliente();

                        actualizarSaldoFavorCliente();

                        const aviso =
                            document.createElement(
                                'div'
                            );

                        aviso.className =
                            'mensaje-cliente-exito visible';

                        aviso.textContent =
                            '✓ ' + datos.message;

                        document.body.appendChild(
                            aviso
                        );

                        setTimeout(
                            function () {
                                aviso.remove();
                            },
                            4000
                        );

                    }
                )

                .catch(
                    function (datos) {

                        const mensajes =
                            datos.errors
                                ? Object.values(
                                    datos.errors
                                ).flat()
                                : [
                                    'No se pudo cargar el cliente.'
                                ];

                        erroresClienteModal.innerHTML =
                            mensajes
                                .map(
                                    mensaje =>
                                        `<p>${mensaje}</p>`
                                )
                                .join('');

                        erroresClienteModal.classList.add(
                            'visible'
                        );

                    }
                );

            }
        );


        // ==========================================================
        // MODAL NUEVO PRODUCTO
        // ==========================================================

        function mostrarNuevoProducto() {

            modalNuevoProducto.classList.remove(
                'oculto'
            );

            modalNuevoProducto.setAttribute(
                'aria-hidden',
                'false'
            );

        }


        function ocultarNuevoProducto() {

            modalNuevoProducto.classList.add(
                'oculto'
            );

            modalNuevoProducto.setAttribute(
                'aria-hidden',
                'true'
            );

        }


        document
            .getElementById('abrir-nuevo-producto')
            .addEventListener(
                'click',
                mostrarNuevoProducto
            );


        document
            .querySelector('.cerrar-modal-producto')
            .addEventListener(
                'click',
                ocultarNuevoProducto
            );


        document
            .getElementById('cerrar-nuevo-producto')
            .addEventListener(
                'click',
                ocultarNuevoProducto
            );


        modalNuevoProducto.addEventListener(
            'click',
            function (event) {

                if (event.target === modalNuevoProducto) {
                    ocultarNuevoProducto();
                }

            }
        );


        // ==========================================================
        // VENCIMIENTO DEL PRODUCTO
        // ==========================================================

        document
            .getElementById('producto-tiene-vencimiento')
            .addEventListener(
                'change',
                function (event) {

                    const campoFecha =
                        document.getElementById(
                            'vencimiento-producto-modal'
                        );

                    const fecha =
                        document.getElementById(
                            'producto-fecha-vencimiento'
                        );

                    const tieneVencimiento =
                        event.target.value === '1';

                    campoFecha.classList.toggle(
                        'oculto',
                        !tieneVencimiento
                    );

                    fecha.disabled =
                        !tieneVencimiento;

                }
            );


        // ==========================================================
        // GUARDAR PRODUCTO DESDE MODAL
        // ==========================================================

        formularioNuevoProducto.addEventListener(
            'submit',
            function (event) {

                event.preventDefault();

                erroresProductoModal.classList.remove(
                    'visible'
                );

                fetch('/productos', {

                    method: 'POST',

                    headers: {
                        'X-CSRF-TOKEN':
                            formularioNuevoProducto
                                .querySelector(
                                    'input[name="_token"]'
                                ).value,

                        'X-Requested-With':
                            'XMLHttpRequest',

                        'Accept':
                            'application/json'
                    },

                    body:
                        new FormData(
                            formularioNuevoProducto
                        )

                })

                .then(
                    async function (response) {

                        const datos =
                            await response.json();

                        if (!response.ok) {
                            throw datos;
                        }

                        return datos;

                    }
                )

                .then(
                    function (datos) {

                        const producto =
                            datos.producto;

                        const opcion =
                            document.createElement(
                                'option'
                            );

                        opcion.value =
                            producto.id;

                        opcion.dataset.precio =
                            producto.precio_venta || '';

                        opcion.textContent =
                            producto.nombre;

                        opcion.selected =
                            true;

                        selectorProductoVenta
                            .appendChild(opcion);

                        formularioNuevoProducto.reset();

                        ocultarNuevoProducto();

                        actualizarPrecioProducto();

                        const aviso =
                            document.createElement(
                                'div'
                            );

                        aviso.className =
                            'mensaje-producto-exito visible';

                        aviso.textContent =
                            '✓ ' + datos.message;

                        document.body.appendChild(
                            aviso
                        );

                        setTimeout(
                            function () {
                                aviso.remove();
                            },
                            4000
                        );

                    }
                )

                .catch(
                    function (datos) {

                        const mensajes =
                            datos.errors
                                ? Object.values(
                                    datos.errors
                                ).flat()
                                : [
                                    'No se pudo cargar el producto.'
                                ];

                        erroresProductoModal.innerHTML =
                            mensajes
                                .map(
                                    mensaje =>
                                        `<p>${mensaje}</p>`
                                )
                                .join('');

                        erroresProductoModal.classList.add(
                            'visible'
                        );

                    }
                );

            }
        );


        // ==========================================================
        // INICIALIZACIÓN
        // ==========================================================

        actualizarPrecioProducto();
        actualizarSaldoFavorCliente();
        actualizarMontosPago();

    </script>

@endsection