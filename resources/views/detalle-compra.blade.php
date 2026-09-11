@extends('layouts.app')

@section('title', 'Detalle de compra')

@section('content')
    <style>
        .detalle-compra { max-width: 900px; }
        .detalle-compra-encabezado { display: flex; justify-content: space-between; align-items: center; }
        .detalle-compra-datos, .detalle-compra-productos { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .detalle-compra-datos th, .detalle-compra-datos td, .detalle-compra-productos th, .detalle-compra-productos td { padding: 10px; text-align: left; border: 1px solid #555; }
        .detalle-compra-datos th, .detalle-compra-productos th { background: #eee; }
        .boton-volver-compra { display: inline-block; padding: 9px 14px; background: #eee; border: 1px solid #ccc; border-radius: 4px; color: #000; text-decoration: none; }
    </style>

    <div class="detalle-compra">
        <div class="detalle-compra-encabezado"><h1>Detalle de compra #{{ $compra->id }}</h1><a href="/compras" class="boton-volver-compra">Volver</a></div>
        <table class="detalle-compra-datos"><tbody>
            <tr><th>Proveedor</th><td>{{ $compra->proveedor?->empresa ?? 'Sin proveedor' }}</td></tr>
            <tr><th>Fecha</th><td>{{ $compra->fecha->format('d/m/Y') }}</td></tr>
            <tr><th>Total</th><td>${{ number_format($compra->total, 2, ',', '.') }}</td></tr>
            <tr><th>Total pagado</th><td>${{ number_format($compra->total_pagado, 2, ',', '.') }}</td></tr>
            <tr><th>Estado</th><td>{{ $compra->estado }}</td></tr>
            @if ($compra->estado === 'Anulada')
                <tr><th>Fecha de anulación</th><td>{{ $compra->fecha_anulacion?->format('d/m/Y H:i') ?? '-' }}</td></tr>
                <tr><th>Motivo de anulación</th><td>{{ $compra->motivo_anulacion ?: 'Sin motivo indicado' }}</td></tr>
            @endif
        </tbody></table>
        <h2>Productos</h2>
        <table class="detalle-compra-productos"><thead><tr><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Subtotal</th></tr></thead><tbody>
            @foreach ($compra->detalles as $detalle)<tr><td>{{ $detalle->producto_nombre }}</td><td>{{ $detalle->cantidad }}</td><td>${{ number_format($detalle->precio, 2, ',', '.') }}</td><td>${{ number_format($detalle->subtotal, 2, ',', '.') }}</td></tr>@endforeach
        </tbody></table>
        <h2>Pagos</h2>
        <table class="detalle-compra-datos"><tbody>
            @forelse ($compra->pagos as $pago)<tr><th>{{ ucfirst(str_replace('_', ' ', $pago->forma_pago)) }}</th><td>${{ number_format($pago->monto, 2, ',', '.') }}</td></tr>@empty<tr><td colspan="2">No hay pagos registrados.</td></tr>@endforelse
        </tbody></table>
    </div>
@endsection