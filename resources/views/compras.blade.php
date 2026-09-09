@extends('layouts.app')

@section('title', 'Compras')

@section('content')
    <style>
        .compras-tabla { width: 100%; max-width: 1000px; border-collapse: collapse; border: 1px solid #555; }
        .compras-tabla th, .compras-tabla td { padding: 10px; text-align: left; border: 1px solid #555; }
        .compras-tabla th { background: #eee; }
        .compras-tabla tr:nth-child(even) { background: #f8f8f8; }
        .buscar-compra { width: 400px; padding: 10px 12px; font-size: 15px; border: 1px solid #ccc; border-radius: 6px; }
        #form-busqueda-compras button { display: inline-block; padding: 6px 8px; background-color: #eee; color: black; border: 1px solid #ccc; border-radius: 4px; cursor: pointer; font-size: 14px; margin-right: 2px; transition: background-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease; }
        .enlace-crear-compra, .acciones-compra a, .acciones-compra button { display: inline-block; padding: 6px 10px; background-color: #eee; color: black; text-decoration: none; border: 1px solid #ccc; border-radius: 4px; cursor: pointer; font-size: 14px; margin-right: 2px; }
        .enlace-crear-compra { margin: 10px 0 20px; transition: background-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease; }
        .enlace-crear-compra:hover, #form-busqueda-compras button:hover, .acciones-compra a:hover, .acciones-compra button:hover { background-color: #d7ebff; box-shadow: 0 4px 10px rgba(0, 91, 170, 0.2); transform: translateY(-2px); }
        .acciones-compra a, .acciones-compra button { transition: background-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease; }
        .acciones-compra { white-space: nowrap; }
        .acciones-compra form { display: inline; }
        #paginacion-compras { margin-top: 20px; }
        #paginacion-compras nav { display: flex; justify-content: center; margin-top: 10px; }
        #paginacion-compras a, #paginacion-compras span { display: inline-block; padding: 6px 10px; margin-right: 5px; border: 1px solid #ccc; border-radius: 4px; text-decoration: none; font-size: 14px; }
        #paginacion-compras a { background-color: #eee; color: black; }
        #paginacion-compras span { background-color: #ccc; color: black; }
        .mensaje-compra { position: fixed; top: 20px; right: 20px; background: #28a745; color: white; padding: 15px 20px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); z-index: 1000; }
        .modal-compra { position: fixed; inset: 0; display: flex; justify-content: flex-end; background: rgba(0, 0, 0, .35); z-index: 900; }
        .modal-compra.oculto { display: none; }
        .modal-compra-contenido { width: min(700px, 100%); height: 100%; padding: 30px; overflow-y: auto; background: white; box-shadow: -4px 0 14px rgba(0, 0, 0, .2); }
        .modal-compra-encabezado { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
        .modal-compra-encabezado h2 { margin: 0; }
        .cerrar-modal-compra { padding: 4px 10px; font-size: 22px; }
        .detalle-compra-datos, .detalle-compra-productos { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        .detalle-compra-datos th, .detalle-compra-datos td, .detalle-compra-productos th, .detalle-compra-productos td { padding: 10px; text-align: left; border-bottom: 1px solid #eee; }
        .detalle-compra-datos th { width: 48%; font-weight: normal; color: #555; }
        .detalle-compra-productos th { width: auto; font-weight: bold; color: black; }
        .detalle-compra-seccion { margin: 24px 0 8px; }
    </style>

    <h1>Compras</h1>

    @if (session('success'))
        <div id="mensaje-compra" class="mensaje-compra">✓ {{ session('success') }}</div>

        <script>
            setTimeout(function () {
                document.getElementById('mensaje-compra').style.display = 'none';
            }, 3000);
        </script>
    @endif

    <form id="form-busqueda-compras">
        <input type="text" id="buscar-compra" class="buscar-compra" value="{{ request('buscar') }}" placeholder="Buscar por proveedor, fecha o estado">
        <button type="submit">Buscar</button>
    </form>

    <br>
    <a href="/compras/crear" class="enlace-crear-compra">+ Nueva compra</a>

    <table class="compras-tabla">
        <thead><tr><th>ID</th><th>Fecha</th><th>Proveedor</th><th>Total</th><th>Estado</th><th>Acciones</th></tr></thead>
        <tbody id="filas-compras">@include('partials.compras-rows')</tbody>
    </table>

    @include('partials.paginacion-compras', ['paginador' => $compras])

    <div id="modal-compra" class="modal-compra oculto" aria-hidden="true">
        <section class="modal-compra-contenido" role="dialog" aria-modal="true" aria-labelledby="modal-compra-titulo">
            <div class="modal-compra-encabezado"><h2 id="modal-compra-titulo">Detalle de compra</h2><button type="button" class="cerrar-modal-compra" id="cerrar-modal-compra" aria-label="Cerrar detalle">&times;</button></div>
            <table class="detalle-compra-datos"><tbody>
                <tr><th>Compra #</th><td id="detalle-compra-id">-</td></tr>
                <tr><th>Proveedor</th><td id="detalle-compra-proveedor">-</td></tr>
                <tr><th>Fecha</th><td id="detalle-compra-fecha">-</td></tr>
                <tr><th>Total</th><td id="detalle-compra-total">-</td></tr>
                <tr><th>Total pagado</th><td id="detalle-compra-total-pagado">-</td></tr>
                <tr><th>Estado</th><td id="detalle-compra-estado">-</td></tr>
            </tbody></table>
            <h3 class="detalle-compra-seccion">Productos</h3>
            <table class="detalle-compra-productos"><thead><tr><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Subtotal</th></tr></thead><tbody id="detalle-compra-productos-filas"></tbody></table>
            <h3 class="detalle-compra-seccion">Pagos</h3>
            <table class="detalle-compra-datos"><tbody id="detalle-compra-pagos-filas"></tbody></table>
        </section>
    </div>

    <script type="application/json" id="datos-compras">@json($compras->items())</script>
    <script>
        const formularioBusquedaCompras = document.getElementById('form-busqueda-compras');
        const inputBuscarCompra = document.getElementById('buscar-compra');
        const filasCompras = document.getElementById('filas-compras');
        const paginacionCompras = document.getElementById('paginacion-compras');
        const modalCompra = document.getElementById('modal-compra');
        let datosCompras = JSON.parse(document.getElementById('datos-compras').textContent);

        function precioCompra(valor) { return '$' + Number(valor || 0).toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
        function fechaCompra(valor) { return valor ? valor.substring(0, 10).split('-').reverse().join('/') : '-'; }
        function mostrarCompra(compra) {
            document.getElementById('modal-compra-titulo').textContent = 'Detalle de compra #' + compra.id;
            document.getElementById('detalle-compra-id').textContent = compra.id;
            document.getElementById('detalle-compra-proveedor').textContent = compra.proveedor ? compra.proveedor.empresa : 'Sin proveedor';
            document.getElementById('detalle-compra-fecha').textContent = fechaCompra(compra.fecha);
            document.getElementById('detalle-compra-total').textContent = precioCompra(compra.total);
            document.getElementById('detalle-compra-total-pagado').textContent = precioCompra(compra.total_pagado);
            document.getElementById('detalle-compra-estado').textContent = compra.estado;
            document.getElementById('detalle-compra-productos-filas').innerHTML = (compra.detalles || []).map(function (detalle) { return '<tr><td>' + detalle.producto_nombre + '</td><td>' + detalle.cantidad + '</td><td>' + precioCompra(detalle.precio) + '</td><td>' + precioCompra(detalle.subtotal) + '</td></tr>'; }).join('') || '<tr><td colspan="4">No hay productos cargados.</td></tr>';
            document.getElementById('detalle-compra-pagos-filas').innerHTML = (compra.pagos || []).map(function (pago) { return '<tr><th>' + pago.forma_pago.replace('_', ' ') + '</th><td>' + precioCompra(pago.monto) + '</td></tr>'; }).join('') || '<tr><td colspan="2">No hay pagos registrados.</td></tr>';
            modalCompra.classList.remove('oculto');
            modalCompra.setAttribute('aria-hidden', 'false');
        }
        function cerrarCompra() { modalCompra.classList.add('oculto'); modalCompra.setAttribute('aria-hidden', 'true'); }
        filasCompras.addEventListener('click', function (event) { const boton = event.target.closest('.boton-ver-compra'); if (boton) { const compra = datosCompras.find(function (item) { return String(item.id) === String(boton.dataset.compra); }); if (compra) mostrarCompra(compra); } });
        document.getElementById('cerrar-modal-compra').addEventListener('click', cerrarCompra);
        modalCompra.addEventListener('click', function (event) { if (event.target === modalCompra) cerrarCompra(); });
        function cargarCompras(url) { fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(function (response) { return response.text(); }).then(function (html) { const documento = new DOMParser().parseFromString(html, 'text/html'); filasCompras.innerHTML = documento.querySelector('#filas-compras-ajax').innerHTML; paginacionCompras.innerHTML = documento.querySelector('#paginacion-compras').innerHTML; datosCompras = JSON.parse(documento.querySelector('#datos-compras-ajax').textContent); history.pushState({}, '', url); }); }
        let tiempoEsperaBusquedaCompras;
        function buscarCompras() { const buscar = inputBuscarCompra.value.trim(); cargarCompras(buscar ? '/compras?buscar=' + encodeURIComponent(buscar) : '/compras'); }
        inputBuscarCompra.addEventListener('input', function () { clearTimeout(tiempoEsperaBusquedaCompras); tiempoEsperaBusquedaCompras = setTimeout(buscarCompras, 300); });
        formularioBusquedaCompras.addEventListener('submit', function (event) { event.preventDefault(); buscarCompras(); });
        paginacionCompras.addEventListener('click', function (event) { const enlace = event.target.closest('a'); if (enlace) { event.preventDefault(); cargarCompras(enlace.href); } });
    </script>
@endsection