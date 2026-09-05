<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Clientes</title>
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

    <a href="/clientes/crear">+ Crear nuevo cliente</a>
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

    <script>
        const inputBuscar = document.getElementById('buscar');
        const formulario = document.getElementById('form-busqueda');
        const tablaClientes = document.getElementById('tabla-clientes');
        const paginacion = document.getElementById('paginacion');

        let tiempoEspera;

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

</body>
</html>