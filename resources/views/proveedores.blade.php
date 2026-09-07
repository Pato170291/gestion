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

    <a href="/proveedores/crear">+ Crear nuevo proveedor</a>

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
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>

            @include('partials.proveedores-rows')

        </tbody>

    </table>

    <div id="paginacion">
        <div id="botones-paginacion-proveedores">
            {{ $proveedores->links() }}
        </div>
    </div>


@endsection