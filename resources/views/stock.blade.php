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
    }

    .boton-stock:hover {
        background: #1d4ed8;
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
        border: 0;
        background: transparent;
        color: #2563eb;
        cursor: pointer;
        font-size: 18px;
        line-height: 1;
    }

    .boton-ver:hover {
        color: #1d4ed8;
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
        border: 0;
        background: transparent;
        color: #69727d;
        cursor: pointer;
        font-size: 25px;
        line-height: 1;
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
            <strong>125</strong>
            <small>Con unidades disponibles</small>
        </article>
        <article class="tarjeta-stock">
            <p>Stock bajo</p>
            <strong>7</strong>
            <small>En el límite o por debajo</small>
        </article>
        <article class="tarjeta-stock">
            <p>Sin stock</p>
            <strong>3</strong>
            <small>Sin unidades disponibles</small>
        </article>
        <article class="tarjeta-stock">
            <p>Unidades totales</p>
            <strong>1.284</strong>
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
                <tr data-producto="Coca Cola 2L" data-estado="normal" data-stock="15" data-minimo="5" data-codigo="001">
                    <td class="nombre-producto-stock">Coca Cola 2L</td>
                    <td class="codigo-producto">001</td>
                    <td>15</td>
                    <td>5</td>
                    <td><span class="estado-stock estado-normal">Normal</span></td>
                    <td><button type="button" class="boton-ver" data-producto="Coca Cola 2L" data-stock="15" data-minimo="5" aria-label="Ver detalle de Coca Cola 2L" title="Ver detalle">&#128065;</button></td>
                </tr>
                <tr data-producto="Yerba 1kg" data-estado="bajo" data-stock="3" data-minimo="5" data-codigo="002">
                    <td class="nombre-producto-stock">Yerba 1kg</td>
                    <td class="codigo-producto">002</td>
                    <td>3</td>
                    <td>5</td>
                    <td><span class="estado-stock estado-bajo">Stock bajo</span></td>
                    <td><button type="button" class="boton-ver" data-producto="Yerba 1kg" data-stock="3" data-minimo="5" aria-label="Ver detalle de Yerba 1kg" title="Ver detalle">&#128065;</button></td>
                </tr>
                <tr data-producto="Azúcar 1kg" data-estado="sin-stock" data-stock="0" data-minimo="3" data-codigo="003">
                    <td class="nombre-producto-stock">Azúcar 1kg</td>
                    <td class="codigo-producto">003</td>
                    <td>0</td>
                    <td>3</td>
                    <td><span class="estado-stock estado-sin-stock">Sin stock</span></td>
                    <td><button type="button" class="boton-ver" data-producto="Azúcar 1kg" data-stock="0" data-minimo="3" aria-label="Ver detalle de Azúcar 1kg" title="Ver detalle">&#128065;</button></td>
                </tr>
            </tbody>
        </table>
        <div id="inventario-vacio" class="vacio-stock">No hay productos que coincidan con la búsqueda.</div>
    </section>

    <section class="seccion-stock">
        <div class="seccion-encabezado">
            <h2>Historial de movimientos</h2>
            <button type="button" class="boton-stock boton-secundario" data-abrir-modal="movimiento">+ Movimiento</button>
        </div>

        <table class="tabla-stock historial-tabla">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Producto</th>
                    <th>Tipo</th>
                    <th>Cantidad</th>
                    <th>Motivo</th>
                    <th>Referencia</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>09/09/2026</td><td>Coca Cola 2L</td><td>Entrada</td><td class="cantidad-entrada">+10</td><td>Compra</td><td>Compra #25</td></tr>
                <tr><td>09/09/2026</td><td>Coca Cola 2L</td><td>Salida</td><td class="cantidad-salida">-2</td><td>Venta</td><td>Venta #40</td></tr>
                <tr><td>09/09/2026</td><td>Yerba 1kg</td><td>Salida</td><td class="cantidad-salida">-1</td><td>Venta</td><td>Venta #41</td></tr>
                <tr><td>08/09/2026</td><td>Azúcar 1kg</td><td>Ajuste</td><td class="cantidad-ajuste">+1</td><td>Corrección de inventario</td><td>Ajuste manual</td></tr>
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
                <thead><tr><th>Fecha</th><th>Tipo</th><th>Cantidad</th><th>Motivo</th><th>Referencia</th></tr></thead>
                <tbody>
                    <tr><td>09/09/2026</td><td>Entrada</td><td class="cantidad-entrada">+10</td><td>Compra</td><td>Compra #25</td></tr>
                    <tr><td>09/09/2026</td><td>Salida</td><td class="cantidad-salida">-2</td><td>Venta</td><td>Venta #40</td></tr>
                    <tr><td>08/09/2026</td><td>Ajuste</td><td class="cantidad-ajuste">+1</td><td>Corrección de inventario</td><td>Ajuste manual</td></tr>
                </tbody>
            </table>
        </div>

        <div class="acciones-movimiento">
            <button type="button" class="boton-stock" data-abrir-modal="movimiento" data-tipo="Entrada">+ Entrada</button>
            <button type="button" class="boton-stock" data-abrir-modal="movimiento" data-tipo="Salida">- Salida</button>
            <button type="button" class="boton-stock boton-secundario" data-abrir-modal="movimiento" data-tipo="Ajuste">Ajustar stock</button>
        </div>
    </div>
</div>

<div class="modal-stock" id="modal-movimiento-stock" aria-hidden="true">
    <div class="modal-contenido-stock" role="dialog" aria-modal="true" aria-labelledby="titulo-movimiento-stock">
        <div class="modal-encabezado-stock">
            <div>
                <h2 id="titulo-movimiento-stock">Nuevo movimiento</h2>
                <p>La registración de movimientos se habilitará próximamente.</p>
            </div>
            <button type="button" class="cerrar-modal-stock" data-cerrar-modal="movimiento" aria-label="Cerrar formulario">&times;</button>
        </div>

        <form class="formulario-movimiento" id="formulario-movimiento-stock">
            <div class="campo-movimiento">
                <label for="tipo-movimiento-stock">Tipo de movimiento</label>
                <select id="tipo-movimiento-stock" class="control-stock">
                    <option>Entrada</option>
                    <option>Salida</option>
                    <option>Ajuste</option>
                </select>
            </div>
            <div class="campo-movimiento">
                <label for="producto-movimiento-stock">Producto</label>
                <select id="producto-movimiento-stock" class="control-stock">
                    <option>Coca Cola 2L</option>
                    <option>Yerba 1kg</option>
                    <option>Azúcar 1kg</option>
                </select>
            </div>
            <div class="campo-movimiento">
                <label for="cantidad-movimiento-stock">Cantidad</label>
                <input type="number" id="cantidad-movimiento-stock" class="control-stock" min="1" placeholder="Ej: 10">
            </div>
            <div class="campo-movimiento">
                <label for="motivo-movimiento-stock">Motivo</label>
                <select id="motivo-movimiento-stock" class="control-stock">
                    <option>Compra</option>
                    <option>Venta</option>
                    <option>Ajuste de inventario</option>
                    <option>Devolución</option>
                    <option>Merma</option>
                    <option>Otro</option>
                </select>
            </div>
            <div class="campo-movimiento">
                <label for="observacion-movimiento-stock">Observación (opcional)</label>
                <textarea id="observacion-movimiento-stock" class="control-stock" rows="3" placeholder="Agregar una observación"></textarea>
            </div>
            <div class="acciones-modal-stock">
                <button type="button" class="boton-stock boton-secundario" data-cerrar-modal="movimiento">Cancelar</button>
                <button type="submit" class="boton-stock">Guardar</button>
            </div>
        </form>
    </div>
</div>

<script>
    (function () {
        const filas = Array.from(document.querySelectorAll('#tabla-inventario tbody tr'));
        const busqueda = document.getElementById('buscar-stock');
        const filtro = document.getElementById('filtro-estado-stock');
        const mensajeVacio = document.getElementById('inventario-vacio');
        const modalDetalle = document.getElementById('modal-detalle-stock');
        const modalMovimiento = document.getElementById('modal-movimiento-stock');
        const tipoMovimiento = document.getElementById('tipo-movimiento-stock');

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
        }

        busqueda.addEventListener('input', aplicarFiltros);
        filtro.addEventListener('change', aplicarFiltros);

        document.querySelectorAll('.boton-ver').forEach(function (boton) {
            boton.addEventListener('click', function () {
                document.getElementById('titulo-detalle-stock').textContent = boton.dataset.producto;
                document.getElementById('detalle-stock-actual').textContent = boton.dataset.stock + ' unidades';
                document.getElementById('detalle-stock-minimo').textContent = boton.dataset.minimo + ' unidades';
                modalDetalle.classList.add('visible');
                modalDetalle.setAttribute('aria-hidden', 'false');
            });
        });

        document.querySelectorAll('[data-abrir-modal="movimiento"]').forEach(function (boton) {
            boton.addEventListener('click', function () {
                cerrarModales();
                if (boton.dataset.tipo) tipoMovimiento.value = boton.dataset.tipo;
                modalMovimiento.classList.add('visible');
                modalMovimiento.setAttribute('aria-hidden', 'false');
            });
        });

        document.querySelectorAll('[data-cerrar-modal]').forEach(function (boton) {
            boton.addEventListener('click', cerrarModales);
        });

        document.querySelectorAll('.modal-stock').forEach(function (modal) {
            modal.addEventListener('click', function (evento) {
                if (evento.target === modal) cerrarModales();
            });
        });

        document.getElementById('formulario-movimiento-stock').addEventListener('submit', function (evento) {
            evento.preventDefault();
            cerrarModales();
        });

        document.addEventListener('keydown', function (evento) {
            if (evento.key === 'Escape') cerrarModales();
        });
    }());
</script>

@endsection