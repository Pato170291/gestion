@extends('layouts.app')

@section('title', 'Detalle de venta')

@section('content')

    <style>
        .detalle-venta {
            max-width: 900px;
        }

        .detalle-venta-encabezado {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .detalle-venta-datos,
        .detalle-venta-productos {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #555555;
            margin-bottom: 24px;
        }

        .detalle-venta-datos th,
        .detalle-venta-datos td,
        .detalle-venta-productos th,
        .detalle-venta-productos td {
            padding: 10px;
            text-align: left;
            border: 1px solid #555555;
        }

        .detalle-venta-datos th,
        .detalle-venta-productos th {
            background-color: #eeeeee;
        }

        .detalle-venta-productos tbody tr:nth-child(even) {
            background-color: #f8f8f8;
        }

        .detalle-venta-total {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin: 20px 0 28px;
            font-size: 20px;
            font-weight: bold;
        }

        .detalle-venta-seccion {
            margin: 24px 0 10px;
        }

        .boton-volver-ventas {
            display: inline-block;
            padding: 9px 14px;
            background: #eeeeee;
            border: 1px solid #cccccc;
            border-radius: 4px;
            color: black;
            text-decoration: none;
        }
    </style>

    <div class="detalle-venta">
        <div class="detalle-venta-encabezado">
            <h1>Detalle de venta</h1>
            <a href="/ventas" class="boton-volver-ventas">Volver</a>
        </div>

        <table class="detalle-venta-datos">
            <tbody>
                <tr><th>Venta #</th><td>{{ $id }}</td></tr>
                <tr><th>Cliente</th><td>-</td></tr>
                <tr><th>Fecha</th><td>-</td></tr>
            </tbody>
        </table>

        <h2 class="detalle-venta-seccion">Productos</h2>

        <table class="detalle-venta-productos">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="4">No hay productos cargados.</td>
                </tr>
            </tbody>
        </table>

        <div class="detalle-venta-total">
            <span>Total:</span>
            <span>-</span>
        </div>

        <table class="detalle-venta-datos">
            <tbody>
                <tr><th>Forma de pago</th><td>-</td></tr>
                <tr><th>Estado</th><td>-</td></tr>
            </tbody>
        </table>
    </div>

@endsection