@extends('layouts.app')

@section('title', 'Productos')

@section('content')

    <style>
        .productos-tabla {
            width: 100%;
            max-width: 900px;
            border-collapse: collapse;
            border: 1px solid #555555;
        }

        .productos-tabla th,
        .productos-tabla td {
            padding: 10px;
            text-align: left;
            border: 1px solid #555555;
        }

        .productos-tabla th {
            background-color: #eeeeee;
        }

        .productos-tabla tr:nth-child(even) {
            background-color: #f8f8f8;
        }

        .buscar-producto {
            width: 400px;
            padding: 10px 12px;
            font-size: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .productos-tabla th:last-child,
        .productos-tabla td:last-child {
            width: 145px;
            white-space: nowrap;
        }

        .productos-tabla a,
        .productos-tabla button,
        #form-busqueda-productos button,
        .detalle-producto button,
        .enlace-crear-producto {
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

        .productos-tabla form {
            display: inline;
        }

        .enlace-crear-producto {
            margin-bottom: 20px;
            transition: background-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }

        .enlace-crear-producto:hover {
            background-color: #d7ebff;
            box-shadow: 0 4px 10px rgba(0, 91, 170, 0.2);
            transform: translateY(-2px);
        }

        #form-busqueda-productos button,
        .productos-tabla tbody td:last-child button {
            transition: background-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }

        #form-busqueda-productos button:hover,
        .productos-tabla tbody td:last-child button:hover {
            background-color: #d7ebff;
            box-shadow: 0 4px 10px rgba(0, 91, 170, 0.2);
            transform: translateY(-2px);
        }

        .detalle-producto {
            position: fixed;
            inset: 0;
            display: flex;
            justify-content: flex-end;
            background: rgba(0, 0, 0, 0.35);
            z-index: 900;
        }

        .detalle-producto.oculto {
            display: none;
        }

        .detalle-producto-contenido {
            width: min(500px, 100%);
            height: 100%;
            padding: 30px;
            overflow-y: auto;
            background: white;
            box-shadow: -4px 0 14px rgba(0, 0, 0, 0.2);
        }

        .detalle-producto-encabezado {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .detalle-producto-encabezado h2 {
            margin: 0;
        }

        .cerrar-detalle-producto {
            margin: 0;
            padding: 4px 10px;
            font-size: 22px;
        }

        .detalle-producto-datos {
            width: 100%;
            border-collapse: collapse;
        }

        .detalle-producto-datos th,
        .detalle-producto-datos td {
            padding: 9px 0;
            text-align: left;
            border-bottom: 1px solid #eeeeee;
        }

        .detalle-producto-datos th {
            width: 48%;
            font-weight: normal;
            color: #555;
        }

        .detalle-producto-seccion {
            margin-top: 24px;
            margin-bottom: 8px;
        }
    </style>

    @if (session('success'))
        <div id="mensaje-exito-producto">
            ✓ {{ session('success') }}
        </div>

        <style>
            #mensaje-exito-producto {
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
                document.getElementById('mensaje-exito-producto').style.display = 'none';
            }, 3000);
        </script>
    @endif

    <h1>Productos</h1>

    <form id="form-busqueda-productos">
        <input
            type="text"
            id="buscar-producto"
            class="buscar-producto"
            placeholder="Buscar por nombre, precio o stock"
        >
        <button type="submit">Buscar</button>
    </form>

    <br>

    <a href="/productos/crear" class="enlace-crear-producto">+ Nuevo producto</a>

    <table class="productos-tabla" id="tabla-productos">
        <thead>
            <tr class="fila-producto">
                <th>ID</th>
                <th>Producto</th>
                <th>Precio de venta</th>
                <th>Stock</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($productos as $producto)
                <tr class="fila-producto">
                    <td>{{ $producto->id }}</td>
                    <td>{{ $producto->nombre }}</td>
                    <td>${{ number_format($producto->precio_venta, 0, ',', '.') }}</td>
                    <td>{{ $producto->stock_actual }}</td>
                    <td>
                        <button
                            type="button"
                            class="boton-ver-producto"
                            data-producto="{{ $producto->id }}"
                            aria-label="Ver producto {{ $producto->nombre }}"
                            title="Ver producto"
                        >&#128065;</button>
                        <a
                            href="/productos/{{ $producto->id }}/editar"
                            aria-label="Editar producto {{ $producto->nombre }}"
                            title="Editar producto"
                        >&#9998;</a>
                        <form method="POST" action="/productos/{{ $producto->id }}">
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                aria-label="Eliminar producto"
                                title="Eliminar producto"
                                onclick="return confirm('¿Seguro que querés eliminar este producto?')"
                            >&#128465;</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No hay productos registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div id="detalle-producto" class="detalle-producto oculto" aria-hidden="true">
        <aside class="detalle-producto-contenido" role="dialog" aria-modal="true" aria-labelledby="detalle-producto-titulo">
            <div class="detalle-producto-encabezado">
                <h2 id="detalle-producto-titulo">Producto</h2>
                <button type="button" class="cerrar-detalle-producto" aria-label="Cerrar detalle">&times;</button>
            </div>

            <table class="detalle-producto-datos">
                <tbody>
                    <tr><th>ID</th><td id="detalle-id">1</td></tr>
                    <tr><th>Marca</th><td id="detalle-marca">Coca Cola</td></tr>
                    <tr><th>Descripción</th><td id="detalle-descripcion">-</td></tr>
                </tbody>
            </table>

            <h3 class="detalle-producto-seccion">Precios</h3>
            <table class="detalle-producto-datos">
                <tbody>
                    <tr><th>Precio de compra</th><td id="detalle-compra">$1.800</td></tr>
                    <tr><th>Precio de venta</th><td id="detalle-venta">$2.500</td></tr>
                </tbody>
            </table>

            <h3 class="detalle-producto-seccion">Stock</h3>
            <table class="detalle-producto-datos">
                <tbody>
                    <tr><th>Stock actual</th><td id="detalle-stock">12</td></tr>
                    <tr><th>Stock mínimo</th><td id="detalle-stock-minimo">5</td></tr>
                    <tr><th>Unidad</th><td id="detalle-unidad">Unidad</td></tr>
                </tbody>
            </table>

            <h3 class="detalle-producto-seccion">Información adicional</h3>
            <table class="detalle-producto-datos">
                <tbody>
                    <tr><th>Proveedor</th><td id="detalle-proveedor">Distribuidora X</td></tr>
                    <tr><th>Tiene vencimiento</th><td id="detalle-tiene-vencimiento">Sí</td></tr>
                    <tr><th>Vencimiento</th><td id="detalle-vencimiento">15/12/2026</td></tr>
                </tbody>
            </table>
        </aside>
    </div>

    <script type="application/json" id="datos-productos">
        @json($productos->keyBy('id'))
    </script>

    <script>
        const formularioBusquedaProductos = document.getElementById('form-busqueda-productos');
        const inputBuscarProducto = document.getElementById('buscar-producto');
        const filasProductos = document.querySelectorAll('.fila-producto');
        const cuerpoTablaProductos = document.querySelector('#tabla-productos tbody');
        const detalleProducto = document.getElementById('detalle-producto');
        const cerrarDetalleProducto = detalleProducto.querySelector('.cerrar-detalle-producto');
        const detalleTitulo = document.getElementById('detalle-producto-titulo');

        const datosProductos = JSON.parse(document.getElementById('datos-productos').textContent);

        function formatearPrecio(valor) {
            return '$' + Number(valor).toLocaleString('es-AR', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        }

        function formatearFecha(fecha) {
            if (!fecha) {
                return '-';
            }

            const partes = fecha.substring(0, 10).split('-');

            return `${partes[2]}/${partes[1]}/${partes[0]}`;
        }

        function filtrarProductos() {
            const textoBusqueda = inputBuscarProducto.value.trim().toLowerCase();
            let productosVisibles = 0;

            filasProductos.forEach(function (fila) {
                const coincide = fila.textContent.toLowerCase().includes(textoBusqueda);

                fila.style.display = coincide ? '' : 'none';

                if (coincide) {
                    productosVisibles++;
                }
            });

            const mensajeSinProductos = document.getElementById('mensaje-sin-productos');

            if (productosVisibles === 0) {
                if (!mensajeSinProductos) {
                    const filaVacia = document.createElement('tr');
                    filaVacia.id = 'mensaje-sin-productos';
                    filaVacia.innerHTML = '<td colspan="5">No se encontraron productos.</td>';
                    cuerpoTablaProductos.appendChild(filaVacia);
                }
            } else if (mensajeSinProductos) {
                mensajeSinProductos.remove();
            }
        }

        inputBuscarProducto.addEventListener('input', filtrarProductos);

        formularioBusquedaProductos.addEventListener('submit', function (event) {
            event.preventDefault();
            filtrarProductos();
        });

        function mostrarDetalleProducto(producto) {
            detalleTitulo.textContent = `Producto: ${producto.nombre}`;
            document.getElementById('detalle-id').textContent = producto.id;
            document.getElementById('detalle-marca').textContent = producto.marca;
            document.getElementById('detalle-descripcion').textContent = producto.descripcion || '-';
            document.getElementById('detalle-compra').textContent = formatearPrecio(producto.precio_compra);
            document.getElementById('detalle-venta').textContent = formatearPrecio(producto.precio_venta);
            document.getElementById('detalle-stock').textContent = producto.stock_actual;
            document.getElementById('detalle-stock-minimo').textContent = producto.stock_minimo;
            document.getElementById('detalle-unidad').textContent = producto.unidad;
            document.getElementById('detalle-proveedor').textContent = producto.proveedor || '-';
            document.getElementById('detalle-tiene-vencimiento').textContent = producto.tiene_vencimiento ? 'Sí' : 'No';
            document.getElementById('detalle-vencimiento').textContent = formatearFecha(producto.fecha_vencimiento);
            detalleProducto.classList.remove('oculto');
            detalleProducto.setAttribute('aria-hidden', 'false');
        }

        function ocultarDetalleProducto() {
            detalleProducto.classList.add('oculto');
            detalleProducto.setAttribute('aria-hidden', 'true');
        }

        document.querySelectorAll('.boton-ver-producto').forEach(function (boton) {
            boton.addEventListener('click', function () {
                mostrarDetalleProducto(datosProductos[boton.dataset.producto]);
            });
        });

        cerrarDetalleProducto.addEventListener('click', ocultarDetalleProducto);

        detalleProducto.addEventListener('click', function (event) {
            if (event.target === detalleProducto) {
                ocultarDetalleProducto();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                ocultarDetalleProducto();
            }
        });
    </script>

@endsection