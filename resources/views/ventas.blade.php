@extends('layouts.app')

@section('title', 'Ventas')

@section('content')

    <style>
        .ventas-tabla {
            width: 100%;
            max-width: 1000px;
            border-collapse: collapse;
            border: 1px solid #555555;
        }

        .ventas-tabla th,
        .ventas-tabla td {
            padding: 10px;
            text-align: left;
            border: 1px solid #555555;
        }

        .ventas-tabla th {
            background-color: #eeeeee;
        }

        .ventas-tabla tr:nth-child(even) {
            background-color: #f8f8f8;
        }

        .mensaje-venta-exito {
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

        .mensaje-venta-error {
            margin-bottom: 20px;
            padding: 12px 16px;
            border: 1px solid #f1aeb5;
            background: #f8d7da;
            color: #842029;
        }

        .estado-venta-anulada {
            color: #842029;
            font-weight: bold;
        }

        .buscar-venta {
            width: 400px;
            padding: 10px 12px;
            font-size: 15px;
            border: 1px solid #cccccc;
            border-radius: 6px;
        }

        #form-busqueda-ventas button {
            display: inline-block;
            padding: 6px 8px;
            background-color: #eeeeee;
            color: black;
            border: 1px solid #cccccc;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin-right: 2px;
            transition: background-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }

        .enlace-crear-venta,
        .acciones-venta a,
        .acciones-venta button {
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
        }

        .enlace-crear-venta {
            margin: 10px 0 20px;
            transition: background-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }

        .enlace-crear-venta:hover,
        #form-busqueda-ventas button:hover,
        .acciones-venta a:hover,
        .acciones-venta button:hover {
            background-color: #d7ebff;
            box-shadow: 0 4px 10px rgba(0, 91, 170, 0.2);
            transform: translateY(-2px);
        }

        #form-busqueda-ventas button,
        .acciones-venta a,
        .acciones-venta button {
            transition: background-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }

        .acciones-venta {
            white-space: nowrap;
        }

        .acciones-venta form {
            display: inline;
        }

        #paginacion-ventas {
            margin-top: 20px;
        }

        #paginacion-ventas nav {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }

        #paginacion-ventas a,
        #paginacion-ventas span {
            display: inline-block;
            padding: 6px 10px;
            margin-right: 5px;
            border: 1px solid #cccccc;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
        }

        #paginacion-ventas a {
            background-color: #eeeeee;
            color: black;
        }

        #paginacion-ventas span {
            background-color: #cccccc;
            color: black;
        }

        .modal-venta {
            position: fixed;
            inset: 0;
            display: flex;
            justify-content: flex-end;
            background: rgba(0, 0, 0, 0.35);
            z-index: 900;
        }

        .modal-venta.oculto {
            display: none;
        }

        .modal-venta-contenido {
            width: min(700px, 100%);
            height: 100%;
            padding: 30px;
            overflow-y: auto;
            background: white;
            box-shadow: -4px 0 14px rgba(0, 0, 0, 0.2);
        }

        .modal-venta-encabezado {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .modal-venta-encabezado h2 {
            margin: 0;
        }

        .cerrar-modal-venta {
            margin: 0;
            padding: 4px 10px;
            font-size: 22px;
            background: transparent;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .cerrar-modal-venta:hover {
            background-color: #d0d0d0;
        }

        .detalle-venta-datos,
        .detalle-venta-productos {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }

        .detalle-venta-datos th,
        .detalle-venta-datos td,
        .detalle-venta-productos th,
        .detalle-venta-productos td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #eeeeee;
        }

        .detalle-venta-datos th,
        .detalle-venta-productos th {
            width: 48%;
            font-weight: normal;
            color: #555555;
        }

        .detalle-venta-productos th {
            width: auto;
            font-weight: bold;
            color: black;
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
            margin: 24px 0 8px;
        }

        .modal-venta-formulario {
            position: fixed;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: rgba(0, 0, 0, 0.35);
            z-index: 901;
        }

        .modal-venta-formulario.oculto {
            display: none;
        }

        .modal-venta-formulario-contenido {
            width: min(620px, 100%);
            max-height: 90vh;
            padding: 28px;
            overflow-y: auto;
            background: white;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
        }

        .modal-venta-formulario-encabezado,
        .modal-cliente-encabezado {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .modal-venta-formulario-encabezado h2,
        .modal-cliente-encabezado h2 {
            margin: 0;
        }

        .cerrar-formulario-venta,
        .cerrar-formulario-cliente {
            margin: 0;
            padding: 4px 10px;
            font-size: 22px;
            background: transparent;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .cerrar-formulario-venta:hover,
        .cerrar-formulario-cliente:hover {
            background-color: #d0d0d0;
        }

        .campo-venta,
        .campo-cliente-modal {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 18px;
        }

        .campo-venta select,
        .campo-venta input,
        .campo-cliente-modal input {
            width: 100%;
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

        .boton-venta,
        .boton-cliente-modal {
            padding: 9px 14px;
            background: #eeeeee;
            border: 1px solid #cccccc;
            border-radius: 4px;
            color: black;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
        }

        .mensaje-cliente-exito {
            display: none;
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            background: #28a745;
            border: 0;
            border-radius: 8px;
            color: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }

        .mensaje-cliente-exito.visible {
            display: block;
        }

        .errores-cliente-modal {
            display: none;
            margin-bottom: 18px;
            padding: 12px 16px;
            background: #f8d7da;
            border: 1px solid #f1aeb5;
            color: #842029;
        }

        .errores-cliente-modal.visible {
            display: block;
        }
    </style>

    <h1>Ventas</h1>

    @if (session('success'))
        <div id="mensaje-venta-exito" class="mensaje-venta-exito">✓ {{ session('success') }}</div>

        <script>
            setTimeout(function () {
                document.getElementById('mensaje-venta-exito').style.display = 'none';
            }, 3000);
        </script>
    @endif

    @if (session('error'))
        <div class="mensaje-venta-error">{{ session('error') }}</div>
    @endif

    <form id="form-busqueda-ventas">
        <input
            type="text"
            id="buscar-venta"
            class="buscar-venta"
            value="{{ request('buscar') }}"
            placeholder="Buscar por cliente, fecha o estado"
        >
        <button type="submit">Buscar</button>
    </form>

    <br>

    <a href="/ventas/crear" class="enlace-crear-venta">+ Nueva venta</a>

    <table class="ventas-tabla" id="tabla-ventas">
        <thead>
            <tr>
                <th>ID</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="filas-ventas">
            @include('partials.ventas-rows')
        </tbody>
    </table>

    @include('partials.paginacion-ventas', ['paginador' => $ventas])

    <div id="modal-venta" class="modal-venta oculto" aria-hidden="true">
        <aside class="modal-venta-contenido" role="dialog" aria-modal="true" aria-labelledby="modal-venta-titulo">
            <div class="modal-venta-encabezado">
                <h2 id="modal-venta-titulo">Detalle de venta</h2>
                <button type="button" class="cerrar-modal-venta" aria-label="Cerrar detalle">&times;</button>
            </div>

            <table class="detalle-venta-datos">
                <tbody>
                    <tr><th>Venta #</th><td id="detalle-venta-id">-</td></tr>
                    <tr><th>Cliente</th><td id="detalle-venta-cliente">-</td></tr>
                    <tr><th>Fecha</th><td id="detalle-venta-fecha">-</td></tr>
                </tbody>
            </table>

            <h3 class="detalle-venta-seccion">Productos</h3>
            <table class="detalle-venta-productos">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody id="detalle-venta-productos-filas"></tbody>
            </table>

            <div class="detalle-venta-total">
                <span>Total:</span>
                <span id="detalle-venta-total">-</span>
            </div>

            <h3 class="detalle-venta-seccion">Pagos</h3>
            <table class="detalle-venta-datos">
                <tbody id="detalle-venta-pagos-filas"></tbody>
            </table>

            <table class="detalle-venta-datos">
                <tbody>
                    <tr><th>Total pagado</th><td id="detalle-venta-total-pagado">-</td></tr>
                    <tr><th>Estado</th><td id="detalle-venta-estado">-</td></tr>
                </tbody>
            </table>
        </aside>
    </div>

    <script type="application/json" id="datos-ventas">
        @json($ventas->items())
    </script>

    <div id="modal-nueva-venta" class="modal-venta-formulario oculto" aria-hidden="true">
        <section class="modal-venta-formulario-contenido" role="dialog" aria-modal="true" aria-labelledby="nueva-venta-titulo">
            <div class="modal-venta-formulario-encabezado">
                <h2 id="nueva-venta-titulo">Nueva venta</h2>
                <button type="button" class="cerrar-formulario-venta" aria-label="Cerrar nueva venta">&times;</button>
            </div>

            <form id="form-nueva-venta">
                <div class="campo-venta">
                    <label for="fecha-venta">Fecha</label>
                    <input type="date" id="fecha-venta" name="fecha" value="{{ now()->format('Y-m-d') }}">
                </div>

                <div class="selector-cliente-linea">
                    <div class="campo-venta">
                        <label for="cliente-venta">Cliente</label>
                        <select id="cliente-venta" name="cliente_id">
                            <option value="">No es cliente</option>
                            @foreach ($clientes as $cliente)
                                <option value="{{ $cliente->id }}">{{ $cliente->nombre }} {{ $cliente->apellido }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="button" class="boton-venta" id="abrir-nuevo-cliente">Nuevo cliente</button>
                </div>

                <button type="button" class="boton-venta">Continuar</button>
            </form>
        </section>
    </div>

    <div id="modal-nuevo-cliente" class="modal-venta-formulario oculto" aria-hidden="true">
        <section class="modal-venta-formulario-contenido" role="dialog" aria-modal="true" aria-labelledby="nuevo-cliente-titulo">
            <div class="modal-cliente-encabezado">
                <h2 id="nuevo-cliente-titulo">Nuevo cliente</h2>
                <button type="button" class="cerrar-formulario-cliente" aria-label="Cerrar nuevo cliente">&times;</button>
            </div>

            <div id="errores-cliente-modal" class="errores-cliente-modal"></div>

            <form id="form-nuevo-cliente">
                @csrf
                <div class="campo-cliente-modal">
                    <label for="cliente-nombre">Nombre</label>
                    <input type="text" id="cliente-nombre" name="nombre" required>
                </div>

                <div class="campo-cliente-modal">
                    <label for="cliente-apellido">Apellido</label>
                    <input type="text" id="cliente-apellido" name="apellido" required>
                </div>

                <div class="campo-cliente-modal">
                    <label for="cliente-telefono">Teléfono</label>
                    <input type="text" id="cliente-telefono" name="telefono" required>
                </div>

                <div class="campo-cliente-modal">
                    <label for="cliente-email">Email (opcional)</label>
                    <input type="email" id="cliente-email" name="email">
                </div>

                <button type="submit" class="boton-cliente-modal">Guardar cliente</button>
                <button type="button" class="boton-cliente-modal" id="volver-a-nueva-venta">Cancelar</button>
            </form>
        </section>
    </div>

    <script>
        const formularioBusquedaVentas = document.getElementById('form-busqueda-ventas');
        const inputBuscarVenta = document.getElementById('buscar-venta');
        const filasVentas = document.getElementById('filas-ventas');
        const paginacionVentas = document.getElementById('paginacion-ventas');
        const modalVenta = document.getElementById('modal-venta');
        const cerrarModalVenta = modalVenta.querySelector('.cerrar-modal-venta');
        const modalNuevaVenta = document.getElementById('modal-nueva-venta');
        const modalNuevoCliente = document.getElementById('modal-nuevo-cliente');
        const selectorClienteVenta = document.getElementById('cliente-venta');
        const formularioNuevoCliente = document.getElementById('form-nuevo-cliente');
        const erroresClienteModal = document.getElementById('errores-cliente-modal');

        function mostrarModalVenta() {
            modalVenta.classList.remove('oculto');
            modalVenta.setAttribute('aria-hidden', 'false');
        }

        function ocultarModalVenta() {
            modalVenta.classList.add('oculto');
            modalVenta.setAttribute('aria-hidden', 'true');
        }

        let datosVentas = JSON.parse(document.getElementById('datos-ventas').textContent);

        function confirmarAnulacion(formulario) {
            if (!confirm('¿Está seguro de que desea anular esta venta? La venta permanecerá registrada en el historial.')) {
                return false;
            }

            const motivo = prompt('Motivo de anulación (opcional):', '');
            formulario.querySelector('input[name="motivo_anulacion"]').value = motivo || '';

            return true;
        }

        function formatearPrecioVenta(valor) {
            return '$' + Number(valor || 0).toLocaleString('es-AR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function formatearFechaVenta(fecha) {
            if (!fecha) {
                return '-';
            }

            const partes = fecha.substring(0, 10).split('-');
            return `${partes[2]}/${partes[1]}/${partes[0]}`;
        }

        function formatearFormaPago(formaPago) {
            return formaPago.replace('_', ' ').replace(/^\w/, function (letra) {
                return letra.toUpperCase();
            });
        }

        function mostrarDetalleVenta(venta) {
            document.getElementById('modal-venta-titulo').textContent = `Detalle de venta #${venta.id}`;
            document.getElementById('detalle-venta-id').textContent = venta.id;
            document.getElementById('detalle-venta-cliente').textContent = venta.cliente
                ? `${venta.cliente.nombre} ${venta.cliente.apellido}`
                : 'No es cliente';
            document.getElementById('detalle-venta-fecha').textContent = formatearFechaVenta(venta.fecha);
            document.getElementById('detalle-venta-total').textContent = formatearPrecioVenta(venta.total);
            document.getElementById('detalle-venta-total-pagado').textContent = formatearPrecioVenta(venta.total_pagado);
            document.getElementById('detalle-venta-estado').textContent = venta.estado;

            const filasProductos = document.getElementById('detalle-venta-productos-filas');
            filasProductos.innerHTML = venta.detalles && venta.detalles.length
                ? venta.detalles.map(function (detalle) {
                    return `<tr>
                        <td>${detalle.producto_nombre}</td>
                        <td>${detalle.cantidad}</td>
                        <td>${formatearPrecioVenta(detalle.precio)}</td>
                        <td>${formatearPrecioVenta(detalle.subtotal)}</td>
                    </tr>`;
                }).join('')
                : '<tr><td colspan="4">No hay productos cargados.</td></tr>';

            const filasPagos = document.getElementById('detalle-venta-pagos-filas');
            filasPagos.innerHTML = venta.pagos && venta.pagos.length
                ? venta.pagos.map(function (pago) {
                    return `<tr><th>${formatearFormaPago(pago.forma_pago)}</th><td>${formatearPrecioVenta(pago.monto)}</td></tr>`;
                }).join('')
                : '<tr><th>Pagos</th><td>No hay pagos registrados.</td></tr>';

            mostrarModalVenta();
        }

        filasVentas.addEventListener('click', function (event) {
            const boton = event.target.closest('.boton-ver-venta');

            if (boton) {
                const venta = datosVentas.find(function (item) {
                    return String(item.id) === String(boton.dataset.venta);
                });

                if (venta) {
                    mostrarDetalleVenta(venta);
                }
            }
        });

        function cargarVentas(url) {
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                const documento = new DOMParser().parseFromString(html, 'text/html');
                const filas = documento.querySelector('#filas-ventas-ajax');
                const paginacion = documento.querySelector('#paginacion-ventas');
                const nuevosDatos = documento.querySelector('#datos-ventas-ajax');

                filasVentas.innerHTML = filas.innerHTML;
                paginacionVentas.innerHTML = paginacion.innerHTML;
                datosVentas = JSON.parse(nuevosDatos.textContent);
                history.pushState({}, '', url);
            });
        }

        let tiempoEsperaBusqueda;

        function buscarVentas() {
            const buscar = inputBuscarVenta.value.trim();
            const url = buscar
                ? `/ventas?buscar=${encodeURIComponent(buscar)}`
                : '/ventas';

            cargarVentas(url);
        }

        inputBuscarVenta.addEventListener('input', function () {
            clearTimeout(tiempoEsperaBusqueda);
            tiempoEsperaBusqueda = setTimeout(buscarVentas, 300);
        });

        formularioBusquedaVentas.addEventListener('submit', function (event) {
            event.preventDefault();
            buscarVentas();
        });

        paginacionVentas.addEventListener('click', function (event) {
            const enlace = event.target.closest('a');

            if (enlace) {
                event.preventDefault();
                cargarVentas(enlace.href);
            }
        });
        cerrarModalVenta.addEventListener('click', ocultarModalVenta);

        modalVenta.addEventListener('click', function (event) {
            if (event.target === modalVenta) {
                ocultarModalVenta();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                ocultarModalVenta();
            }
        });

        function mostrarNuevaVenta() {
            modalNuevaVenta.classList.remove('oculto');
            modalNuevaVenta.setAttribute('aria-hidden', 'false');
        }

        function ocultarNuevaVenta() {
            modalNuevaVenta.classList.add('oculto');
            modalNuevaVenta.setAttribute('aria-hidden', 'true');
        }

        function mostrarNuevoCliente() {
            modalNuevaVenta.classList.add('oculto');
            modalNuevoCliente.classList.remove('oculto');
            modalNuevoCliente.setAttribute('aria-hidden', 'false');
        }

        function volverANuevaVenta() {
            modalNuevoCliente.classList.add('oculto');
            modalNuevoCliente.setAttribute('aria-hidden', 'true');
            modalNuevaVenta.classList.remove('oculto');
            modalNuevaVenta.setAttribute('aria-hidden', 'false');
        }

        document.getElementById('abrir-nueva-venta').addEventListener('click', mostrarNuevaVenta);
        document.querySelector('.cerrar-formulario-venta').addEventListener('click', ocultarNuevaVenta);
        document.getElementById('abrir-nuevo-cliente').addEventListener('click', mostrarNuevoCliente);
        document.querySelector('.cerrar-formulario-cliente').addEventListener('click', volverANuevaVenta);
        document.getElementById('volver-a-nueva-venta').addEventListener('click', volverANuevaVenta);

        formularioNuevoCliente.addEventListener('submit', function (event) {
            event.preventDefault();
            erroresClienteModal.classList.remove('visible');

            fetch('/clientes', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': formularioNuevoCliente.querySelector('input[name="_token"]').value,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: new FormData(formularioNuevoCliente)
            })
            .then(async function (response) {
                const datos = await response.json();

                if (!response.ok) {
                    throw datos;
                }

                return datos;
            })
            .then(function (datos) {
                const cliente = datos.cliente;
                const opcion = document.createElement('option');
                opcion.value = cliente.id;
                opcion.textContent = `${cliente.nombre} ${cliente.apellido}`;
                opcion.selected = true;
                selectorClienteVenta.appendChild(opcion);
                formularioNuevoCliente.reset();
                volverANuevaVenta();
                const aviso = document.createElement('div');
                aviso.className = 'mensaje-cliente-exito visible';
                aviso.textContent = '✓ ' + datos.message;
                document.body.appendChild(aviso);
                setTimeout(function () {
                    aviso.remove();
                }, 4000);
            })
            .catch(function (datos) {
                const mensajes = datos.errors
                    ? Object.values(datos.errors).flat()
                    : ['No se pudo cargar el cliente.'];
                erroresClienteModal.innerHTML = mensajes.map(mensaje => `<p>${mensaje}</p>`).join('');
                erroresClienteModal.classList.add('visible');
            });
        });
    </script>

@endsection