@extends('layouts.app')

@section('title', 'Clientes')

@section('content')

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
             margin-right: 5px;
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

        .enlace-crear-cliente {
            display: inline-block;
            margin-top: 10px;
            transition: background-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }

        .enlace-crear-cliente:hover {
            background-color: #d7ebff;
            box-shadow: 0 4px 10px rgba(0, 91, 170, 0.2);
            transform: translateY(-2px);
        }

        #form-busqueda button,
        #tabla-completa tbody td:last-child a,
        #tabla-completa tbody td:last-child button,
        .detalle-contenido button:not(.cerrar-detalle),
        .modal-pago-cuenta-contenido button {
            transition: background-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }

        #form-busqueda button:hover,
        #tabla-completa tbody td:last-child a:hover,
        #tabla-completa tbody td:last-child button:hover,
        .detalle-contenido button:not(.cerrar-detalle):hover,
        .modal-pago-cuenta-contenido button:hover {
            background-color: #d7ebff;
            box-shadow: 0 4px 10px rgba(0, 91, 170, 0.2);
            transform: translateY(-2px);
        }

        .detalle-cliente {
            position: fixed;
            inset: 0;
            display: flex;
            justify-content: flex-end;
            background: rgba(0, 0, 0, 0.35);
            z-index: 900;
        }

        .detalle-cliente.oculto {
            display: none;
        }

        .detalle-contenido {
            width: min(460px, 100%);
            height: 100%;
            padding: 30px;
            overflow-y: auto;
            background: white;
            box-shadow: -4px 0 14px rgba(0, 0, 0, 0.2);
        }

        .detalle-encabezado {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
        }

        .detalle-encabezado h2 {
            margin: 0;
        }

        .cerrar-detalle {
            margin: 0;
            padding: 4px 10px;
            font-size: 22px;
            background: transparent;
            border: none;
            border-radius: 4px;
            transition: background-color 0.2s ease;
        }

        .cerrar-detalle:hover {
            background-color: #d0d0d0;
        }

        .saldo-destacado {
            margin: 20px 0;
            padding: 18px;
            background: #f8f8f8;
            border: 1px solid #dddddd;
        }

        .detalle-datos-cliente p {
            margin: 4px 0;
        }

        .saldo-destacado strong,
        .saldo-final strong {
            display: block;
            margin-top: 8px;
            font-size: 24px;
        }

        .movimientos {
            width: 100%;
            margin: 20px 0;
        }

        .movimientos td {
            padding: 8px 0;
            border-bottom: 1px solid #eeeeee;
        }

        .movimientos-vacio {
            color: #777;
            font-style: italic;
        }

        .saldo-final {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 18px;
            border-top: 1px solid #cccccc;
        }

        .registrar-pago {
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
</head>
<body>

    <h1>Listado de clientes</h1>

    <form method="GET" action="/clientes" id="form-busqueda">
        <input
            type="text"
            name="buscar"
            id="buscar"
            value="{{ request('buscar') }}"
            placeholder="Buscar por nombre, apellido, teléfono o email"
        >

        <button type="submit">Buscar</button>
    </form>

<br>

    <a href="/clientes/crear" class="enlace-crear-cliente">+ Crear nuevo cliente</a>
    <br><br>
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

    @if (session('error') || $errors->any())
        <div class="mensaje-error-cuenta">{{ session('error') ?: $errors->first() }}</div>
    @endif

    <table border="1" id="tabla-completa">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th>Condición</th>
                <th>Saldo</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody id="tabla-clientes">
            @include('partials.clientes-rows')
        </tbody>
    </table>
    
    <div id="paginacion">
        @include('partials.paginacion')
    </div>

    <div id="detalle-cliente" class="detalle-cliente oculto" aria-hidden="true">
        <aside class="detalle-contenido" role="dialog" aria-modal="true" aria-labelledby="detalle-titulo">
            <div class="detalle-encabezado">
                <h2 id="detalle-titulo">Cliente</h2>
                <button type="button" class="cerrar-detalle" aria-label="Cerrar detalle">&times;</button>
            </div>

            <div class="detalle-datos-cliente">
                <p><strong>Nombre:</strong> <span id="detalle-cliente-nombre"></span></p>
                <p><strong>Apellido:</strong> <span id="detalle-cliente-apellido"></span></p>
                <p><strong>CUIT:</strong> <span id="detalle-cliente-cuit"></span></p>
                <p><strong>Teléfono:</strong> <span id="detalle-cliente-telefono"></span></p>
                <p><strong>Email:</strong> <span id="detalle-cliente-email"></span></p>
                <p><strong>Condición frente al IVA:</strong> <span id="detalle-cliente-condicion"></span></p>
            </div>

            <div class="saldo-destacado">
                <span>SALDO A PAGAR</span>
                <strong>$0</strong>
            </div>

            <h3>CUENTA CORRIENTE</h3>
            <h4>ÚLTIMOS MOVIMIENTOS</h4>

            <table class="movimientos">
                <thead><tr><th>Fecha</th><th>Concepto</th><th>Debe</th><th>Haber</th><th>Saldo</th></tr></thead>
                <tbody id="movimientos-cliente"></tbody>
            </table>

            <div class="saldo-final">
                <span>Saldo</span>
                <strong>$0</strong>
            </div>

            <button type="button" class="registrar-pago" id="abrir-pago-cliente">Registrar pago</button>
        </aside>
    </div>

    <div id="modal-pago-cliente" class="modal-pago-cuenta oculto" aria-hidden="true">
        <section class="modal-pago-cuenta-contenido" role="dialog" aria-modal="true">
            <h2>Registrar pago</h2>
            <p>Cliente: <strong id="pago-cliente-nombre"></strong></p>
            <p>Saldo pendiente: <strong id="pago-cliente-saldo"></strong></p>
            <form id="form-pago-cliente" class="formulario-pago-cuenta" method="POST">
                @csrf
                <label>Monto a pagar<input name="monto" id="monto-pago-cliente" type="number" min="0.01" step="0.01" required></label>
                <p id="error-pago-cliente" class="error-pago-cuenta oculto"></p>
                <label>Medio de pago<select name="medio" required><option value="efectivo">Efectivo</option><option value="tarjeta">Tarjeta</option><option value="transferencia">Transferencia</option></select></label>
                <label>Observación<textarea name="observacion"></textarea></label>
                <div><button type="button" id="cerrar-pago-cliente">Cancelar</button><button type="submit">Registrar pago</button></div>
            </form>
        </section>
    </div>

    <script>
        const inputBuscar = document.getElementById('buscar');
        const formulario = document.getElementById('form-busqueda');
        const tablaClientes = document.getElementById('tabla-clientes');
        const paginacion = document.getElementById('paginacion');
        const detalleCliente = document.getElementById('detalle-cliente');
        const detalleTitulo = document.getElementById('detalle-titulo');
        const cerrarDetalle = detalleCliente.querySelector('.cerrar-detalle');
        const modalPagoCliente = document.getElementById('modal-pago-cliente');
        const formPagoCliente = document.getElementById('form-pago-cliente');
        const errorPagoCliente = document.getElementById('error-pago-cliente');
        const abrirPagoCliente = document.getElementById('abrir-pago-cliente');
        let clienteCuenta = null;

        let tiempoEspera;

        function cerrarDetalleCliente() {
            detalleCliente.classList.add('oculto');
            detalleCliente.setAttribute('aria-hidden', 'true');
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

        function mostrarDetalleCliente(datos, cuentaUrl) {
            clienteCuenta = datos;
            clienteCuenta.cuentaUrl = cuentaUrl;
            detalleTitulo.textContent = datos.cliente;
            document.getElementById('detalle-cliente-nombre').textContent = datos.nombre || '-';
            document.getElementById('detalle-cliente-apellido').textContent = datos.apellido || '-';
            document.getElementById('detalle-cliente-cuit').textContent = datos.cuit || '-';
            document.getElementById('detalle-cliente-telefono').textContent = datos.telefono || '-';
            document.getElementById('detalle-cliente-email').textContent = datos.email || '-';
            document.getElementById('detalle-cliente-condicion').textContent = datos.condicion_iva || '-';
            document.querySelector('#detalle-cliente .saldo-destacado strong').textContent = formatoMoneda(datos.saldo);
            document.querySelector('#detalle-cliente .saldo-final strong').textContent = formatoMoneda(datos.saldo);
            document.getElementById('movimientos-cliente').innerHTML = datos.movimientos.length ? datos.movimientos.map(function (movimiento) { return '<tr><td>' + movimiento.fecha + '</td><td>' + movimiento.concepto + '</td><td>$' + Number(movimiento.debe).toLocaleString('es-AR', { minimumFractionDigits: 2 }) + '</td><td>$' + Number(movimiento.haber).toLocaleString('es-AR', { minimumFractionDigits: 2 }) + '</td><td>$' + Number(movimiento.saldo).toLocaleString('es-AR', { minimumFractionDigits: 2 }) + '</td></tr>'; }).join('') : '<tr><td colspan="5" class="movimientos-vacio">No hay movimientos registrados.</td></tr>';
            detalleCliente.classList.remove('oculto');
            detalleCliente.setAttribute('aria-hidden', 'false');
        }

        tablaClientes.addEventListener('click', function (event) {
            const boton = event.target.closest('.boton-ver-cliente');

            if (!boton) {
                return;
            }

            fetch(boton.dataset.cuentaUrl, { headers: { 'Accept': 'application/json' } })
                .then(response => response.json())
                .then(function (datos) {
                    mostrarDetalleCliente(datos, boton.dataset.cuentaUrl);
                });
        });

        abrirPagoCliente.addEventListener('click', function () {
            if (!clienteCuenta || Number(clienteCuenta.saldo) <= 0) return;
            document.getElementById('pago-cliente-nombre').textContent = clienteCuenta.cliente;
            document.getElementById('pago-cliente-saldo').textContent = formatoMoneda(clienteCuenta.saldo);
            formPagoCliente.action = '/clientes/' + clienteCuenta.id + '/cuenta-corriente/pagos';
            errorPagoCliente.textContent = '';
            errorPagoCliente.classList.add('oculto');
            formPagoCliente.reset();
            modalPagoCliente.classList.remove('oculto');
            modalPagoCliente.setAttribute('aria-hidden', 'false');
        });
        document.getElementById('cerrar-pago-cliente').addEventListener('click', function () { modalPagoCliente.classList.add('oculto'); });

        formPagoCliente.addEventListener('submit', async function (event) {
            event.preventDefault();

            errorPagoCliente.textContent = '';
            errorPagoCliente.classList.add('oculto');

            const respuesta = await fetch(formPagoCliente.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': formPagoCliente.querySelector('input[name="_token"]').value,
                },
                body: new FormData(formPagoCliente),
            });

            const datos = await respuesta.json().catch(function () { return {}; });

            if (!respuesta.ok) {
                errorPagoCliente.textContent = (datos.errors && datos.errors.monto && datos.errors.monto[0])
                    || datos.message
                    || 'No se pudo registrar el pago.';
                errorPagoCliente.classList.remove('oculto');
                return;
            }

            modalPagoCliente.classList.add('oculto');
            modalPagoCliente.setAttribute('aria-hidden', 'true');
            formPagoCliente.reset();
            mostrarMensajeExito(datos.message || 'El pago del cliente fue registrado correctamente.');

            const cuentaUrl = clienteCuenta.cuentaUrl;
            const cuentaActualizada = await fetch(cuentaUrl, { headers: { 'Accept': 'application/json' } }).then(function (r) { return r.json(); });
            mostrarDetalleCliente(cuentaActualizada, cuentaUrl);
            cargarClientes(window.location.pathname + window.location.search);
        });

        cerrarDetalle.addEventListener('click', cerrarDetalleCliente);

        detalleCliente.addEventListener('click', function (event) {
            if (event.target === detalleCliente) {
                cerrarDetalleCliente();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                cerrarDetalleCliente();
            }
        });

        function cargarClientes(url) {

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {

                const documento = new DOMParser().parseFromString(html, 'text/html');

                const filas = documento.querySelector('#filas-clientes');
                const botones = documento.querySelector('#botones-paginacion');

                tablaClientes.innerHTML = filas.innerHTML;
                paginacion.innerHTML = botones.innerHTML;

                history.pushState({}, '', url);

            });
        }

        inputBuscar.addEventListener('input', function () {

            clearTimeout(tiempoEspera);

            tiempoEspera = setTimeout(function () {

                const buscar = inputBuscar.value;

                const url = buscar
                    ? `/clientes?buscar=${encodeURIComponent(buscar)}`
                    : '/clientes';

                cargarClientes(url);

            }, 300);
        });

        formulario.addEventListener('submit', function (event) {
            event.preventDefault();
        });

        paginacion.addEventListener('click', function (event) {

            if (event.target.tagName === 'A') {

                event.preventDefault();

                cargarClientes(event.target.href);
            }
        });

    </script>

@endsection