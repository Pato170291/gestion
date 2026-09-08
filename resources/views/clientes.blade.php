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
        #tabla-completa tbody td:last-child button {
            transition: background-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }

        #form-busqueda button:hover,
        #tabla-completa tbody td:last-child a:hover,
        #tabla-completa tbody td:last-child button:hover {
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
        }

        .saldo-destacado {
            margin: 20px 0;
            padding: 18px;
            background: #f8f8f8;
            border: 1px solid #dddddd;
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

    <table border="1" id="tabla-completa">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th>Saldo</th>
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

            <div class="saldo-destacado">
                <span>SALDO A PAGAR</span>
                <strong>$0</strong>
            </div>

            <h3>CUENTA CORRIENTE</h3>

            <table class="movimientos">
                <tbody>
                    <tr>
                        <td colspan="2" class="movimientos-vacio">Sin movimientos registrados</td>
                    </tr>
                </tbody>
            </table>

            <div class="saldo-final">
                <span>Saldo</span>
                <strong>$0</strong>
            </div>

            <button type="button" class="registrar-pago" disabled>Registrar pago</button>
        </aside>
    </div>

    <script>
        const inputBuscar = document.getElementById('buscar');
        const formulario = document.getElementById('form-busqueda');
        const tablaClientes = document.getElementById('tabla-clientes');
        const paginacion = document.getElementById('paginacion');
        const detalleCliente = document.getElementById('detalle-cliente');
        const detalleTitulo = document.getElementById('detalle-titulo');
        const cerrarDetalle = detalleCliente.querySelector('.cerrar-detalle');

        let tiempoEspera;

        function cerrarDetalleCliente() {
            detalleCliente.classList.add('oculto');
            detalleCliente.setAttribute('aria-hidden', 'true');
        }

        tablaClientes.addEventListener('click', function (event) {
            const boton = event.target.closest('.boton-ver-cliente');

            if (!boton) {
                return;
            }

            detalleTitulo.textContent = boton.dataset.nombre;
            detalleCliente.classList.remove('oculto');
            detalleCliente.setAttribute('aria-hidden', 'false');
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