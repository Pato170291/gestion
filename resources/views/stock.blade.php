@extends('layouts.app')

@section('title', 'Stock')

@section('content')

<style>
    .stock-pagina {
        max-width: 1280px;
        margin: 0 auto;
    }

    .stock-encabezado {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 28px;
    }

    .stock-encabezado h1 {
        margin: 0 0 6px;
        color: #20252b;
        font-size: 30px;
    }

    .stock-encabezado p {
        margin: 0;
        color: #69727d;
    }

    .boton-stock {
        border: 0;
        border-radius: 6px;
        padding: 11px 16px;
        background: #2563eb;
        color: white;
        cursor: pointer;
        font-size: 14px;
        font-weight: bold;
        transition: background-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
    }

    .boton-stock:hover {
        background: #1d4ed8;
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3);
        transform: translateY(-2px);
    }

    .boton-secundario {
        background: #eef2f7;
        color: #374151;
    }

    .boton-secundario:hover {
        background: #e2e8f0;
    }

    .resumen-stock {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 28px;
    }

    .tarjeta-stock {
        min-height: 122px;
        padding: 20px;
        border: 1px solid #e1e5ea;
        border-radius: 8px;
        background: white;
        box-shadow: 0 2px 8px rgba(30, 41, 59, 0.04);
    }

    .tarjeta-stock p {
        margin: 0 0 10px;
        color: #69727d;
        font-size: 14px;
    }

    .tarjeta-stock strong {
        display: block;
        color: #20252b;
        font-size: 28px;
    }

    .tarjeta-stock small {
        color: #8a929c;
        font-size: 12px;
    }

    .seccion-stock {
        margin-bottom: 28px;
        padding: 22px;
        border: 1px solid #e1e5ea;
        border-radius: 8px;
        background: white;
        box-shadow: 0 2px 8px rgba(30, 41, 59, 0.04);
    }

    .seccion-stock h2 {
        margin: 0;
        color: #20252b;
        font-size: 20px;
    }

    .seccion-encabezado {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
    }

    .controles-stock {
        display: flex;
        gap: 10px;
        margin-bottom: 18px;
    }

    .control-stock {
        width: 100%;
        min-height: 40px;
        padding: 9px 11px;
        border: 1px solid #d5dae1;
        border-radius: 5px;
        background: white;
        color: #374151;
        font: inherit;
    }

    .control-stock:focus {
        outline: 2px solid rgba(37, 99, 235, 0.18);
        border-color: #2563eb;
    }

    .control-busqueda {
        flex: 2;
    }

    .control-filtro {
        flex: 1;
    }

    .tabla-stock {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }

    .tabla-stock th,
    .tabla-stock td {
        padding: 14px 12px;
        border-bottom: 1px solid #edf0f3;
        text-align: left;
        vertical-align: middle;
    }

    .tabla-stock th {
        background: #f8fafc;
        color: #69727d;
        font-size: 12px;
        font-weight: bold;
        text-transform: uppercase;
    }

    .tabla-stock tbody tr:hover {
        background: #fbfcfe;
    }

    .nombre-producto-stock {
        color: #20252b;
        font-weight: bold;
    }

    .codigo-producto {
        color: #69727d;
    }

    .estado-stock {
        display: inline-block;
        padding: 5px 9px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: bold;
    }

    .estado-normal {
        background: #e8f5ed;
        color: #187344;
    }

    .estado-bajo {
        background: #fff4db;
        color: #996500;
    }

    .estado-sin-stock {
        background: #fde8e8;
        color: #b42318;
    }

    .boton-ver {
        display: inline-block;
        padding: 6px 10px;
        background-color: #eeeeee;
        color: black;
        border: 1px solid #cccccc;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        line-height: normal;
        transition: background-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
    }

    .boton-ver:hover {
        background-color: #d7ebff;
        box-shadow: 0 4px 10px rgba(0, 91, 170, 0.2);
        transform: translateY(-2px);
    }

    .vacio-stock {
        display: none;
        padding: 22px 12px;
        color: #69727d;
        text-align: center;
    }

    .historial-tabla td:first-child,
    .historial-tabla th:first-child {
        white-space: nowrap;
    }

    .cantidad-entrada {
        color: #187344;
        font-weight: bold;
    }

    .cantidad-salida {
        color: #b42318;
        font-weight: bold;
    }

    .cantidad-ajuste {
        color: #996500;
        font-weight: bold;
    }

    .mensaje-exito-stock {
        position: fixed;
        top: 20px;
        right: 20px;
        background: #28a745;
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        z-index: 2000;
    }

    .mensaje-error-stock {
        margin-bottom: 20px;
        padding: 12px 16px;
        border-radius: 6px;
        border: 1px solid #f1aeb5;
        background: #fde8e8;
        color: #b42318;
    }

    .modal-stock {
        position: fixed;
        inset: 0;
        z-index: 10;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(15, 23, 42, 0.48);
    }

    .modal-stock.visible {
        display: flex;
    }

    .modal-contenido-stock {
        width: min(680px, 100%);
        max-height: calc(100vh - 40px);
        overflow-y: auto;
        padding: 24px;
        border-radius: 8px;
        background: white;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.2);
    }

    .modal-encabezado-stock {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 22px;
    }

    .modal-encabezado-stock h2 {
        margin: 0 0 5px;
        color: #20252b;
        font-size: 22px;
    }

    .modal-encabezado-stock p {
        margin: 0;
        color: #69727d;
    }

    .cerrar-modal-stock {
        margin: 0;
        padding: 4px 10px;
        font-size: 22px;
        background: transparent;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .cerrar-modal-stock:hover {
        background-color: #d0d0d0;
    }

    .datos-producto-stock {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
        margin-bottom: 24px;
    }

    .dato-producto-stock {
        padding: 14px;
        border: 1px solid #e1e5ea;
        border-radius: 6px;
    }

    .dato-producto-stock span {
        display: block;
        margin-bottom: 5px;
        color: #69727d;
        font-size: 12px;
    }

    .dato-producto-stock strong {
        color: #20252b;
        font-size: 18px;
    }

    .modal-seccion-stock h3 {
        margin: 0 0 12px;
        color: #20252b;
        font-size: 16px;
    }

    .acciones-movimiento {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 16px;
    }

    .formulario-movimiento {
        display: grid;
        gap: 15px;
    }

    .campo-movimiento {
        display: grid;
        gap: 6px;
    }

    .campo-movimiento label {
        color: #374151;
        font-size: 13px;
        font-weight: bold;
    }

    .acciones-modal-stock {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 22px;
    }

    @media (max-width: 820px) {
        .resumen-stock {
            grid-template-columns: repeat(2, 1fr);
        }

        .controles-stock {
            flex-wrap: wrap;
        }

        .control-busqueda,
        .control-filtro {
            flex: 1 1 45%;
        }
    }

    @media (max-width: 620px) {
        main {
            padding: 20px 14px;
        }

        .stock-encabezado,
        .seccion-encabezado {
            align-items: flex-start;
            flex-direction: column;
        }

        .stock-encabezado .boton-stock {
            width: 100%;
        }

        .resumen-stock {
            gap: 10px;
        }

        .tarjeta-stock {
            min-height: 108px;
            padding: 14px;
        }

        .tarjeta-stock strong {
            font-size: 24px;
        }

        .seccion-stock {
            padding: 14px;
            overflow-x: auto;
        }

        .tabla-stock {
            min-width: 650px;
        }

        .modal-contenido-stock {
            padding: 18px;
        }
    }
</style>

<div class="stock-pagina">
    @if (session('success'))
        <div id="mensaje-exito-stock" class="mensaje-exito-stock">✓ {{ session('success') }}</div>
        <script>
            setTimeout(function () {
                var mensaje = document.getElementById('mensaje-exito-stock');
                if (mensaje) mensaje.style.display = 'none';
            }, 3000);
        </script>
    @endif

    @if ($errors->any())
        <div class="mensaje-error-stock">{{ $errors->first() }}</div>
    @endif

    <div class="stock-encabezado">
        <div>
            <h1>Stock</h1>
            <p>Control y seguimiento del inventario del comercio.</p>
        </div>

        <button type="button" class="boton-stock" data-abrir-modal="movimiento">+ Movimiento</button>
    </div>

    <section class="resumen-stock" aria-label="Resumen del stock">
        <article class="tarjeta-stock">
            <p>Productos con stock</p>
            <strong>{{ $resumen['con_stock'] }}</strong>
            <small>Con unidades disponibles</small>
        </article>
        <article class="tarjeta-stock">
            <p>Stock bajo</p>
            <strong>{{ $resumen['bajo'] }}</strong>
            <small>En el límite o por debajo</small>
        </article>
        <article class="tarjeta-stock">
            <p>Sin stock</p>
            <strong>{{ $resumen['sin_stock'] }}</strong>
            <small>Sin unidades disponibles</small>
        </article>
        <article class="tarjeta-stock">
            <p>Unidades totales</p>
            <strong>{{ number_format($resumen['unidades_totales'], 0, ',', '.') }}</strong>
            <small>Sumatoria del inventario</small>
        </article>
    </section>

    <section class="seccion-stock">
        <div class="seccion-encabezado">
            <h2>Inventario</h2>
        </div>

        <div class="controles-stock">
            <input type="search" id="buscar-stock" class="control-stock control-busqueda" placeholder="Buscar producto..." aria-label="Buscar producto">
            <select id="filtro-estado-stock" class="control-stock control-filtro" aria-label="Filtrar por estado">
                <option value="todos">Todos</option>
                <option value="normal">Stock normal</option>
                <option value="bajo">Stock bajo</option>
                <option value="sin-stock">Sin stock</option>
            </select>
            <select class="control-stock control-filtro" aria-label="Filtrar por proveedor">
                <option>Todos los proveedores</option>
                <option>Distribuidora Central</option>
                <option>Almacén Mayorista</option>
            </select>
        </div>

        <table class="tabla-stock" id="tabla-inventario">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Código</th>
                    <th>Stock actual</th>
                    <th>Stock mínimo</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($productos as $producto)
                    @php
                        $estadoTexto = match ($producto->estado_stock) {
                            'normal' => 'Normal',
                            'bajo' => 'Stock bajo',
                            default => 'Sin stock',
                        };
                        $codigo = str_pad($producto->id, 3, '0', STR_PAD_LEFT);
                    @endphp
                    <tr data-producto="{{ $producto->nombre }}" data-estado="{{ $producto->estado_stock }}" data-stock="{{ $producto->stock_calculado }}" data-minimo="{{ $producto->stock_minimo }}" data-codigo="{{ $codigo }}">
                        <td class="nombre-producto-stock">{{ $producto->nombre }}</td>
                        <td class="codigo-producto">{{ $codigo }}</td>
                        <td>{{ $producto->stock_calculado }}</td>
                        <td>{{ $producto->stock_minimo }}</td>
                        <td><span class="estado-stock estado-{{ $producto->estado_stock }}">{{ $estadoTexto }}</span></td>
                        <td><button type="button" class="boton-ver" data-producto-id="{{ $producto->id }}" data-producto="{{ $producto->nombre }}" data-stock="{{ $producto->stock_calculado }}" data-minimo="{{ $producto->stock_minimo }}" aria-label="Ver detalle de {{ $producto->nombre }}" title="Ver detalle">&#128065;</button></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">No hay productos registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div id="inventario-vacio" class="vacio-stock">No hay productos que coincidan con la búsqueda.</div>
    </section>

    <section class="seccion-stock">
        <div class="seccion-encabezado">
            <h2>Historial de movimientos</h2>
        </div>

        <table class="tabla-stock historial-tabla">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Producto</th>
                    <th>Tipo</th>
                    <th>Cantidad</th>
                    <th>Motivo</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($movimientos as $movimiento)
                    <tr>
                        <td>{{ $movimiento->fecha->format('d/m/Y') }}</td>
                        <td>{{ $movimiento->producto->nombre ?? '-' }}</td>
                        <td>{{ ucfirst($movimiento->tipo) }}</td>
                        <td class="cantidad-{{ $movimiento->tipo }}">{{ $movimiento->cantidad_texto }}</td>
                        <td>{{ $movimiento->motivo }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">Todavía no hay movimientos registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>
</div>

<div class="modal-stock" id="modal-detalle-stock" aria-hidden="true">
    <div class="modal-contenido-stock" role="dialog" aria-modal="true" aria-labelledby="titulo-detalle-stock">
        <div class="modal-encabezado-stock">
            <div>
                <h2 id="titulo-detalle-stock">Producto</h2>
                <p>Detalle del inventario</p>
            </div>
            <button type="button" class="cerrar-modal-stock" data-cerrar-modal="detalle" aria-label="Cerrar detalle">&times;</button>
        </div>

        <div class="datos-producto-stock">
            <div class="dato-producto-stock"><span>Stock actual</span><strong id="detalle-stock-actual">15 unidades</strong></div>
            <div class="dato-producto-stock"><span>Stock mínimo</span><strong id="detalle-stock-minimo">5 unidades</strong></div>
        </div>

        <div class="modal-seccion-stock">
            <h3>Historial de movimientos</h3>
            <table class="tabla-stock">
                <thead><tr><th>Fecha</th><th>Tipo</th><th>Cantidad</th><th>Motivo</th></tr></thead>
                <tbody id="movimientos-detalle-stock">
                    <tr><td colspan="4">Sin movimientos registrados.</td></tr>
                </tbody>
            </table>
        </div>

        <div class="acciones-movimiento">
            <button type="button" class="boton-stock boton-secundario" data-abrir-modal="movimiento" data-tipo="entrada">+ Entrada</button>
            <button type="button" class="boton-stock boton-secundario" data-abrir-modal="movimiento" data-tipo="salida">- Salida</button>
            <button type="button" class="boton-stock boton-secundario" data-abrir-modal="movimiento" data-tipo="ajuste">Ajustar stock</button>
        </div>
    </div>
</div>

<div class="modal-stock" id="modal-movimiento-stock" aria-hidden="true">
    <div class="modal-contenido-stock" role="dialog" aria-modal="true" aria-labelledby="titulo-movimiento-stock">
        <div class="modal-encabezado-stock">
            <div>
                <h2 id="titulo-movimiento-stock">Nuevo movimiento</h2>
                <p>Registrá una entrada, salida o ajuste manual de stock.</p>
            </div>
            <button type="button" class="cerrar-modal-stock" data-cerrar-modal="movimiento" aria-label="Cerrar formulario">&times;</button>
        </div>

        <form class="formulario-movimiento" id="formulario-movimiento-stock" method="POST" action="{{ route('stock.movimientos.store') }}">
            @csrf
            <div class="campo-movimiento">
                <label for="tipo-movimiento-stock">Tipo de movimiento</label>
                <select id="tipo-movimiento-stock" name="tipo" class="control-stock" required>
                    <option value="entrada">Entrada</option>
                    <option value="salida">Salida</option>
                    <option value="ajuste">Ajuste</option>
                </select>
            </div>
            <div class="campo-movimiento">
                <label for="producto-movimiento-stock">Producto</label>
                <select id="producto-movimiento-stock" name="producto_id" class="control-stock" required>
                    <option value="" disabled selected>Seleccionar producto</option>
                    @foreach ($productos as $producto)
                        <option value="{{ $producto->id }}">{{ $producto->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="campo-movimiento">
                <label for="cantidad-movimiento-stock" id="etiqueta-cantidad-movimiento-stock">Cantidad</label>
                <input type="number" id="cantidad-movimiento-stock" name="cantidad" class="control-stock" min="0" step="1" placeholder="Ej: 10" required>
            </div>
            <div class="campo-movimiento">
                <label for="motivo-movimiento-stock">Motivo</label>
                <select id="motivo-movimiento-stock" name="motivo" class="control-stock" required>
                    <option value="Compra">Compra</option>
                    <option value="Venta">Venta</option>
                    <option value="Ajuste de inventario">Ajuste de inventario</option>
                    <option value="Devolución">Devolución</option>
                    <option value="Merma">Merma</option>
                    <option value="Otro">Otro</option>
                </select>
            </div>
            <div class="campo-movimiento">
                <label for="fecha-movimiento-stock">Fecha</label>
                <input type="date" id="fecha-movimiento-stock" name="fecha" class="control-stock" value="{{ old('fecha', now()->toDateString()) }}" required>
            </div>
            <div class="acciones-modal-stock">
                <button type="button" class="boton-stock boton-secundario" data-cerrar-modal="movimiento">Cancelar</button>
                <button type="submit" class="boton-stock boton-secundario">Guardar</button>
            </div>
        </form>
    </div>
</div>

<script id="movimientos-por-producto" type="application/json">{!! $movimientosPorProducto->toJson() !!}</script>

<script>
    (function () {
        const filas = Array.from(document.querySelectorAll('#tabla-inventario tbody tr'));
        const busqueda = document.getElementById('buscar-stock');
        const filtro = document.getElementById('filtro-estado-stock');
        const mensajeVacio = document.getElementById('inventario-vacio');
        const modalDetalle = document.getElementById('modal-detalle-stock');
        const modalMovimiento = document.getElementById('modal-movimiento-stock');
        const tipoMovimiento = document.getElementById('tipo-movimiento-stock');
        const productoMovimiento = document.getElementById('producto-movimiento-stock');
        const etiquetaCantidad = document.getElementById('etiqueta-cantidad-movimiento-stock');
        const cantidadMovimiento = document.getElementById('cantidad-movimiento-stock');
        const movimientosPorProducto = JSON.parse(document.getElementById('movimientos-por-producto').textContent || '{}');
        let modalPrevio = null;
        let productoDetalleId = null;

        function aplicarFiltros() {
            const texto = busqueda.value.toLowerCase().trim();
            const estado = filtro.value;
            let visibles = 0;

            filas.forEach(function (fila) {
                const coincideTexto = fila.dataset.producto.toLowerCase().includes(texto) || fila.dataset.codigo.includes(texto);
                const coincideEstado = estado === 'todos' || fila.dataset.estado === estado;
                fila.style.display = coincideTexto && coincideEstado ? '' : 'none';
                if (coincideTexto && coincideEstado) visibles++;
            });

            mensajeVacio.style.display = visibles ? 'none' : 'block';
        }

        function cerrarModales() {
            document.querySelectorAll('.modal-stock.visible').forEach(function (modal) {
                modal.classList.remove('visible');
                modal.setAttribute('aria-hidden', 'true');
            });
            modalPrevio = null;
        }

        function cerrarModalMovimiento() {
            modalMovimiento.classList.remove('visible');
            modalMovimiento.setAttribute('aria-hidden', 'true');

            if (modalPrevio) {
                modalPrevio.classList.add('visible');
                modalPrevio.setAttribute('aria-hidden', 'false');
                modalPrevio = null;
            }
        }

        busqueda.addEventListener('input', aplicarFiltros);
        filtro.addEventListener('change', aplicarFiltros);

        document.querySelectorAll('.boton-ver').forEach(function (boton) {
            boton.addEventListener('click', function () {
                productoDetalleId = boton.dataset.productoId;
                document.getElementById('titulo-detalle-stock').textContent = boton.dataset.producto;
                document.getElementById('detalle-stock-actual').textContent = boton.dataset.stock + ' unidades';
                document.getElementById('detalle-stock-minimo').textContent = boton.dataset.minimo + ' unidades';

                const cuerpoHistorial = document.getElementById('movimientos-detalle-stock');
                const movimientos = movimientosPorProducto[productoDetalleId] || [];
                cuerpoHistorial.innerHTML = movimientos.length
                    ? movimientos.map(function (movimiento) {
                        return '<tr><td>' + movimiento.fecha + '</td><td>' + movimiento.tipo + '</td><td class="cantidad-' + movimiento.tipo_clase + '">' + movimiento.cantidad + '</td><td>' + movimiento.motivo + '</td></tr>';
                    }).join('')
                    : '<tr><td colspan="4">Sin movimientos registrados.</td></tr>';

                modalDetalle.classList.add('visible');
                modalDetalle.setAttribute('aria-hidden', 'false');
            });
        });

        document.querySelectorAll('[data-abrir-modal="movimiento"]').forEach(function (boton) {
            boton.addEventListener('click', function () {
                const modalOrigen = boton.closest('.modal-stock');
                modalPrevio = (modalOrigen && modalOrigen !== modalMovimiento) ? modalOrigen : null;

                if (modalPrevio) {
                    modalPrevio.classList.remove('visible');
                    modalPrevio.setAttribute('aria-hidden', 'true');
                } else {
                    cerrarModales();
                }

                if (boton.dataset.tipo) tipoMovimiento.value = boton.dataset.tipo;
                if (modalOrigen === modalDetalle && productoDetalleId) productoMovimiento.value = productoDetalleId;
                actualizarEtiquetaCantidad();
                modalMovimiento.classList.add('visible');
                modalMovimiento.setAttribute('aria-hidden', 'false');
            });
        });

        document.querySelectorAll('[data-cerrar-modal]').forEach(function (boton) {
            boton.addEventListener('click', function () {
                if (boton.dataset.cerrarModal === 'movimiento') {
                    cerrarModalMovimiento();
                } else {
                    cerrarModales();
                }
            });
        });

        document.querySelectorAll('.modal-stock').forEach(function (modal) {
            modal.addEventListener('click', function (evento) {
                if (evento.target !== modal) return;
                if (modal === modalMovimiento) {
                    cerrarModalMovimiento();
                } else {
                    cerrarModales();
                }
            });
        });

        function actualizarEtiquetaCantidad() {
            if (tipoMovimiento.value === 'ajuste') {
                etiquetaCantidad.textContent = 'Stock resultante';
                cantidadMovimiento.min = '0';
            } else {
                etiquetaCantidad.textContent = 'Cantidad';
                cantidadMovimiento.min = '1';
            }
        }

        tipoMovimiento.addEventListener('change', actualizarEtiquetaCantidad);
        actualizarEtiquetaCantidad();

        document.addEventListener('keydown', function (evento) {
            if (evento.key !== 'Escape') return;
            if (modalMovimiento.classList.contains('visible')) {
                cerrarModalMovimiento();
            } else {
                cerrarModales();
            }
        });
    }());
</script>

@endsection