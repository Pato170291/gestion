@extends('layouts.app')

@section('title', 'Proveedores')

@section('content')

    @if (session('success'))
        <div id="mensaje-exito">
            ✓ {{ session('success') }}
        </div>

        <style>
            #mensaje-exito {
                position: fixed;
                top: 20px;
                right: 20px;
                background: #28a745;
                color: white;
                padding: 15px 20px;
                border-radius: 8px;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
                z-index: 1000;
            }
        </style>

        <script>
            setTimeout(function () {
                document.getElementById('mensaje-exito').style.display = 'none';
            }, 3000);
        </script>
    @endif

    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #eeeeee;
        }

        tr:nth-child(even) {
            background-color: #f8f8f8;
        }

        th:last-child,
        td:last-child {
            width: 145px;
            white-space: nowrap;
        }

        a,
        button {
            display: inline-block;
            padding: 6px 10px;
            background-color: #eeeeee;
            color: black;
            text-decoration: none;
            border: 1px solid #cccccc;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin-right: 2px;
            padding: 6px 8px;
        }

        form {
            display: inline;
        }

        #paginacion {
            margin-top: 20px;
        }

        #paginacion nav {
            display: flex;
            justify-content: center;
        }

        #paginacion a,
        #paginacion span {
            display: inline-block;
            padding: 6px 10px;
            margin-right: 5px;
            border: 1px solid #cccccc;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
        }

        #paginacion a {
            background-color: #eeeeee;
            color: black;
        }

        #paginacion a:hover {
            background-color: #dddddd;
        }

        #paginacion span {
            background-color: #cccccc;
            color: black;
        }

        #buscar {
            width: 400px;
            padding: 10px 12px;
            font-size: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .enlace-crear-proveedor {
            display: inline-block;
            margin-top: 10px;
            transition: background-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }

        .enlace-crear-proveedor:hover {
            background-color: #d7ebff;
            box-shadow: 0 4px 10px rgba(0, 91, 170, 0.2);
            transform: translateY(-2px);
        }

        #form-busqueda button,
        #tabla-completa tbody td:last-child a,
        #tabla-completa tbody td:last-child button,
        .detalle-proveedor-contenido button:not(.cerrar-detalle-proveedor),
        .modal-pago-cuenta-contenido button {
            transition: background-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }

        #form-busqueda button:hover,
        #tabla-completa tbody td:last-child a:hover,
        #tabla-completa tbody td:last-child button:hover,
        .detalle-proveedor-contenido button:not(.cerrar-detalle-proveedor):hover,
        .modal-pago-cuenta-contenido button:hover {
            background-color: #d7ebff;
            box-shadow: 0 4px 10px rgba(0, 91, 170, 0.2);
            transform: translateY(-2px);
        }

        .detalle-proveedor {
            position: fixed;
            inset: 0;
            display: flex;
            justify-content: flex-end;
            background: rgba(0, 0, 0, 0.35);
            z-index: 900;
        }

        .detalle-proveedor.oculto {
            display: none;
        }

        .detalle-proveedor-contenido {
            width: min(460px, 100%);
            height: 100%;
            padding: 30px;
            overflow-y: auto;
            background: white;
            box-shadow: -4px 0 14px rgba(0, 0, 0, 0.2);
        }

        .detalle-proveedor-encabezado {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
        }

        .detalle-proveedor-encabezado h2 {
            margin: 0;
        }

        .cerrar-detalle-proveedor {
            margin: 0;
            padding: 4px 10px;
            font-size: 22px;
            background: transparent;
            border: none;
            border-radius: 4px;
            transition: background-color 0.2s ease;
        }

        .cerrar-detalle-proveedor:hover {
            background-color: #d0d0d0;
        }

        .saldo-proveedor-destacado {
            margin: 20px 0;
            padding: 18px;
            background: #f8f8f8;
            border: 1px solid #dddddd;
        }

        .saldo-proveedor-destacado strong,
        .saldo-proveedor-final strong {
            display: block;
            margin-top: 8px;
            font-size: 24px;
        }

        .movimientos-proveedor {
            width: 100%;
            margin: 20px 0;
        }

        .movimientos-proveedor td {
            padding: 8px 0;
            border-bottom: 1px solid #eeeeee;
        }

        .saldo-proveedor-final {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 18px;
            border-top: 1px solid #cccccc;
        }

        .registrar-pago-proveedor {
            width: 100%;
            margin-top: 24px;
            padding: 12px;
        }

        .mensaje-error-cuenta { margin: 16px 0; padding: 12px 16px; border: 1px solid #f1aeb5; background: #f8d7da; color: #842029; }

        .error-pago-cuenta { margin: 0; padding: 10px 12px; border: 1px solid #f1aeb5; background: #f8d7da; color: #842029; border-radius: 4px; font-size: 14px; }
        .error-pago-cuenta.oculto { display: none; }

        .formulario-pago-cuenta { display: flex; flex-direction: column; gap: 12px; margin-top: 18px; }
        .formulario-pago-cuenta input, .formulario-pago-cuenta select, .formulario-pago-cuenta textarea { padding: 9px 10px; border: 1px solid #ccc; border-radius: 4px; font: inherit; }
        .modal-pago-cuenta { position: fixed; inset: 0; display: flex; align-items: center; justify-content: center; padding: 20px; background: rgba(0, 0, 0, .35); z-index: 950; }
        .modal-pago-cuenta.oculto { display: none; }
        .modal-pago-cuenta-contenido { width: min(480px, 100%); padding: 28px; background: white; box-shadow: 0 8px 24px rgba(0, 0, 0, .25); }

        #mensaje-exito-ajax {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #28a745;
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            z-index: 2000;
            display: none;
        }
    </style>

    <h1>Listado de proveedores</h1>

    @if (session('error') || $errors->any())
        <div class="mensaje-error-cuenta">{{ session('error') ?: $errors->first() }}</div>
    @endif

    <form method="GET" action="/proveedores" id="form-busqueda">

        <input
            type="text"
            name="buscar"
            id="buscar"
            value="{{ request('buscar') }}"
            placeholder="Buscar por empresa, contacto, teléfono, email o CUIT"
        >

        <button type="submit">Buscar</button>

    </form>

    <br>

    <a href="/proveedores/crear" class="enlace-crear-proveedor">+ Crear nuevo proveedor</a>

    <br><br>

    <table border="1" id="tabla-completa">

        <thead>
            <tr>
                <th>Empresa</th>
                <th>Contacto</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th>Dirección</th>
                <th>CUIT</th>
                <th>Condición</th>
                <th>Saldo</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody id="tabla-proveedores">

            @include('partials.proveedores-rows')

        </tbody>

    </table>

    <div id="paginacion">
        @include('partials.paginacion-generica', ['paginador' => $proveedores])
    </div>

    <div id="detalle-proveedor" class="detalle-proveedor oculto" aria-hidden="true">
        <aside class="detalle-proveedor-contenido" role="dialog" aria-modal="true" aria-labelledby="detalle-proveedor-titulo">
            <div class="detalle-proveedor-encabezado">
                <h2 id="detalle-proveedor-titulo">Proveedor</h2>
                <button type="button" class="cerrar-detalle-proveedor" aria-label="Cerrar detalle">&times;</button>
            </div>

            <div class="saldo-proveedor-destacado">
                <span>SALDO A PAGAR</span>
                <strong>$0</strong>
            </div>

            <h3>CUENTA CORRIENTE</h3>
            <h4>ÚLTIMOS MOVIMIENTOS</h4>

            <table class="movimientos-proveedor">
                <thead><tr><th>Fecha</th><th>Concepto</th><th>Debe</th><th>Haber</th><th>Saldo</th></tr></thead>
                <tbody id="movimientos-proveedor"></tbody>
            </table>

            <div class="saldo-proveedor-final">
                <span>Saldo</span>
                <strong>$0</strong>
            </div>

            <button type="button" class="registrar-pago-proveedor" id="abrir-pago-proveedor">Registrar pago</button>
        </aside>
    </div>

    <div id="modal-pago-proveedor" class="modal-pago-cuenta oculto" aria-hidden="true">
        <section class="modal-pago-cuenta-contenido" role="dialog" aria-modal="true">
            <h2>Registrar pago</h2>
            <p>Proveedor: <strong id="pago-proveedor-nombre"></strong></p>
            <p>Saldo pendiente: <strong id="pago-proveedor-saldo"></strong></p>
            <form id="form-pago-proveedor" class="formulario-pago-cuenta" method="POST">
                @csrf
                <label>Monto a pagar<input name="monto" id="monto-pago-proveedor" type="number" min="0.01" step="0.01" required></label>
                <p id="error-pago-proveedor" class="error-pago-cuenta oculto"></p>
                <label>Medio de pago<select name="medio" required><option value="efectivo">Efectivo</option><option value="tarjeta">Tarjeta</option><option value="transferencia">Transferencia</option></select></label>
                <label>Observación<textarea name="observacion"></textarea></label>
                <div><button type="button" id="cerrar-pago-proveedor">Cancelar</button><button type="submit">Registrar pago</button></div>
            </form>
        </section>
    </div>

    <script>
        const inputBuscar = document.getElementById('buscar');
        const tablaProveedores = document.getElementById('tabla-proveedores');
        const paginacion = document.getElementById('paginacion');
        const detalleProveedor = document.getElementById('detalle-proveedor');
        const detalleProveedorTitulo = document.getElementById('detalle-proveedor-titulo');
        const cerrarDetalleProveedor = detalleProveedor.querySelector('.cerrar-detalle-proveedor');
        const modalPagoProveedor = document.getElementById('modal-pago-proveedor');
        const formPagoProveedor = document.getElementById('form-pago-proveedor');
        const errorPagoProveedor = document.getElementById('error-pago-proveedor');
        const abrirPagoProveedor = document.getElementById('abrir-pago-proveedor');
        let proveedorCuenta = null;

        let tiempoEspera;

        function cerrarDetalleProveedorPanel() {
            detalleProveedor.classList.add('oculto');
            detalleProveedor.setAttribute('aria-hidden', 'true');
        }

        function formatoMoneda(valor) {
            return '$' + Number(valor).toLocaleString('es-AR', { minimumFractionDigits: 2 });
        }

        function mostrarMensajeExito(texto) {
            let elemento = document.getElementById('mensaje-exito-ajax');

            if (!elemento) {
                elemento = document.createElement('div');
                elemento.id = 'mensaje-exito-ajax';
                document.body.appendChild(elemento);
            }

            elemento.textContent = '✓ ' + texto;
            elemento.style.display = 'block';

            clearTimeout(elemento._tiempoOculto);
            elemento._tiempoOculto = setTimeout(function () {
                elemento.style.display = 'none';
            }, 3000);
        }

        function mostrarDetalleProveedor(datos, cuentaUrl) {
            proveedorCuenta = datos;
            proveedorCuenta.cuentaUrl = cuentaUrl;
            detalleProveedorTitulo.textContent = `Proveedor: ${datos.proveedor}`;
            document.querySelector('#detalle-proveedor .saldo-proveedor-destacado strong').textContent = formatoMoneda(datos.saldo);
            document.querySelector('#detalle-proveedor .saldo-proveedor-final strong').textContent = formatoMoneda(datos.saldo);
            document.getElementById('movimientos-proveedor').innerHTML = datos.movimientos.length ? datos.movimientos.map(function (movimiento) { return '<tr><td>' + movimiento.fecha + '</td><td>' + movimiento.concepto + '</td><td>$' + Number(movimiento.debe).toLocaleString('es-AR', { minimumFractionDigits: 2 }) + '</td><td>$' + Number(movimiento.haber).toLocaleString('es-AR', { minimumFractionDigits: 2 }) + '</td><td>$' + Number(movimiento.saldo).toLocaleString('es-AR', { minimumFractionDigits: 2 }) + '</td></tr>'; }).join('') : '<tr><td colspan="5">No hay movimientos registrados.</td></tr>';
            detalleProveedor.classList.remove('oculto');
            detalleProveedor.setAttribute('aria-hidden', 'false');
        }

        tablaProveedores.addEventListener('click', function (event) {
            const boton = event.target.closest('.boton-ver-proveedor');

            if (!boton) {
                return;
            }

            fetch(boton.dataset.cuentaUrl, { headers: { 'Accept': 'application/json' } })
                .then(response => response.json())
                .then(function (datos) {
                    mostrarDetalleProveedor(datos, boton.dataset.cuentaUrl);
                });
        });

        abrirPagoProveedor.addEventListener('click', function () {
            if (!proveedorCuenta || Number(proveedorCuenta.saldo) <= 0) return;
            document.getElementById('pago-proveedor-nombre').textContent = proveedorCuenta.proveedor;
            document.getElementById('pago-proveedor-saldo').textContent = formatoMoneda(proveedorCuenta.saldo);
            formPagoProveedor.action = '/proveedores/' + proveedorCuenta.id + '/cuenta-corriente/pagos';
            errorPagoProveedor.textContent = '';
            errorPagoProveedor.classList.add('oculto');
            formPagoProveedor.reset();
            modalPagoProveedor.classList.remove('oculto');
            modalPagoProveedor.setAttribute('aria-hidden', 'false');
        });
        document.getElementById('cerrar-pago-proveedor').addEventListener('click', function () { modalPagoProveedor.classList.add('oculto'); });

        formPagoProveedor.addEventListener('submit', async function (event) {
            event.preventDefault();

            errorPagoProveedor.textContent = '';
            errorPagoProveedor.classList.add('oculto');

            const respuesta = await fetch(formPagoProveedor.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': formPagoProveedor.querySelector('input[name="_token"]').value,
                },
                body: new FormData(formPagoProveedor),
            });

            const datos = await respuesta.json().catch(function () { return {}; });

            if (!respuesta.ok) {
                errorPagoProveedor.textContent = (datos.errors && datos.errors.monto && datos.errors.monto[0])
                    || datos.message
                    || 'No se pudo registrar el pago.';
                errorPagoProveedor.classList.remove('oculto');
                return;
            }

            modalPagoProveedor.classList.add('oculto');
            modalPagoProveedor.setAttribute('aria-hidden', 'true');
            formPagoProveedor.reset();
            mostrarMensajeExito(datos.message || 'El pago al proveedor fue registrado correctamente.');

            const cuentaUrl = proveedorCuenta.cuentaUrl;
            const cuentaActualizada = await fetch(cuentaUrl, { headers: { 'Accept': 'application/json' } }).then(function (r) { return r.json(); });
            mostrarDetalleProveedor(cuentaActualizada, cuentaUrl);
            cargarProveedores(window.location.pathname + window.location.search);
        });

        cerrarDetalleProveedor.addEventListener('click', cerrarDetalleProveedorPanel);

        detalleProveedor.addEventListener('click', function (event) {
            if (event.target === detalleProveedor) {
                cerrarDetalleProveedorPanel();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                cerrarDetalleProveedorPanel();
            }
        });

        function cargarProveedores(url) {

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {

                const documento = new DOMParser().parseFromString(html, 'text/html');

                const filas = documento.querySelector('#filas-proveedores');
                const botones = documento.querySelector('#botones-paginacion-proveedores');

                tablaProveedores.innerHTML = filas.innerHTML;
                paginacion.innerHTML = botones.innerHTML;

                history.pushState({}, '', url);
            });
        }

        inputBuscar.addEventListener('input', function () {

            clearTimeout(tiempoEspera);

            tiempoEspera = setTimeout(function () {

                const buscar = inputBuscar.value;

                const url = buscar
                    ? `/proveedores?buscar=${encodeURIComponent(buscar)}`
                    : '/proveedores';

                cargarProveedores(url);

            }, 300);
        });

        paginacion.addEventListener('click', function (event) {

            if (event.target.tagName === 'A') {

                event.preventDefault();

                cargarProveedores(event.target.href);
            }
        });
    </script>

@endsection