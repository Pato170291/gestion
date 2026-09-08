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
    </style>

    <h1>Listado de proveedores</h1>

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

            <table class="movimientos-proveedor">
                <tbody>
                    <tr>
                        <td>08/09</td>
                        <td>Sin movimientos</td>
                        <td>$0</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>

            <div class="saldo-proveedor-final">
                <span>Saldo</span>
                <strong>$0</strong>
            </div>

            <button type="button" class="registrar-pago-proveedor" disabled>Registrar pago</button>
        </aside>
    </div>

    <script>
        const inputBuscar = document.getElementById('buscar');
        const tablaProveedores = document.getElementById('tabla-proveedores');
        const paginacion = document.getElementById('paginacion');
        const detalleProveedor = document.getElementById('detalle-proveedor');
        const detalleProveedorTitulo = document.getElementById('detalle-proveedor-titulo');
        const cerrarDetalleProveedor = detalleProveedor.querySelector('.cerrar-detalle-proveedor');

        let tiempoEspera;

        function cerrarDetalleProveedorPanel() {
            detalleProveedor.classList.add('oculto');
            detalleProveedor.setAttribute('aria-hidden', 'true');
        }

        tablaProveedores.addEventListener('click', function (event) {
            const boton = event.target.closest('.boton-ver-proveedor');

            if (!boton) {
                return;
            }

            detalleProveedorTitulo.textContent = `Proveedor: ${boton.dataset.nombre}`;
            detalleProveedor.classList.remove('oculto');
            detalleProveedor.setAttribute('aria-hidden', 'false');
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