@php
    $mostrarCliente = $mostrarCliente ?? false;
    $mostrarProveedor = $mostrarProveedor ?? false;
    $mostrarProducto = $mostrarProducto ?? false;
    $campoPrecioProducto = $campoPrecioProducto ?? 'precio_venta';
    $selectorProducto = $selectorProducto ?? 'producto-venta';
    $selectorProveedor = $selectorProveedor ?? 'proveedor-compra';
    $selectorCliente = $selectorCliente ?? 'cliente-venta';
@endphp

<style>
    .modal-alta-rapida { position: fixed; inset: 0; display: flex; align-items: center; justify-content: center; padding: 20px; background: rgba(0, 0, 0, .35); z-index: 900; }
    .modal-alta-rapida.oculto { display: none; }
    .modal-alta-rapida-contenido { width: min(760px, 100%); max-height: 90vh; overflow-y: auto; padding: 28px; background: #fff; box-shadow: 0 8px 24px rgba(0, 0, 0, .25); }
    .modal-alta-rapida-encabezado { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
    .modal-alta-rapida-encabezado h2 { margin: 0; }
    .cerrar-modal-alta-rapida { padding: 4px 10px; font-size: 22px; }
    .campo-alta-rapida { display: flex; flex-direction: column; gap: 6px; margin-bottom: 16px; }
    .campo-alta-rapida input, .campo-alta-rapida select, .campo-alta-rapida textarea { width: 100%; max-width: 500px; padding: 9px 10px; border: 1px solid #ccc; border-radius: 4px; font: inherit; }
    .grupo-alta-rapida { margin: 0 0 18px; padding: 16px; border: 1px solid #ddd; background: #fafafa; }
    .error-alta-rapida { display: none; margin-bottom: 18px; padding: 12px 16px; background: #f8d7da; border: 1px solid #f1aeb5; color: #842029; }
    .error-alta-rapida.visible { display: block; }
    .boton-alta-rapida { display: inline-block; padding: 6px 8px; background: #eee; border: 1px solid #ccc; border-radius: 4px; cursor: pointer; font-size: 14px; text-decoration: none; color: #000; margin-right: 2px; transition: background-color .2s ease, box-shadow .2s ease, transform .2s ease; }
    .boton-alta-rapida:hover { background: #d7ebff; box-shadow: 0 4px 10px rgba(0, 91, 170, .2); transform: translateY(-2px); }
    .mensaje-alta-rapida { position: fixed; top: 20px; right: 20px; padding: 15px 20px; background: #28a745; border-radius: 8px; color: white; box-shadow: 0 4px 10px rgba(0, 0, 0, .2); z-index: 1000; }
</style>

@if ($mostrarCliente)
    <div id="modal-alta-cliente" class="modal-alta-rapida oculto" aria-hidden="true">
        <section class="modal-alta-rapida-contenido" role="dialog" aria-modal="true">
            <div class="modal-alta-rapida-encabezado"><h2>Nuevo cliente</h2><button type="button" class="cerrar-modal-alta-rapida" data-cerrar-modal="modal-alta-cliente">&times;</button></div>
            <div id="error-alta-cliente" class="error-alta-rapida"></div>
            <form id="form-alta-cliente">@csrf
                <div class="campo-alta-rapida"><label>Nombre</label><input name="nombre" required></div>
                <div class="campo-alta-rapida"><label>Apellido</label><input name="apellido" required></div>
                <div class="campo-alta-rapida"><label>Teléfono</label><input name="telefono" required></div>
                <div class="campo-alta-rapida"><label>Email (opcional)</label><input type="email" name="email"></div>
                <button type="submit" class="boton-alta-rapida">Guardar cliente</button>
                <button type="button" class="boton-alta-rapida" data-cerrar-modal="modal-alta-cliente">Cancelar</button>
            </form>
        </section>
    </div>
@endif

@if ($mostrarProveedor)
    <div id="modal-alta-proveedor" class="modal-alta-rapida oculto" aria-hidden="true">
        <section class="modal-alta-rapida-contenido" role="dialog" aria-modal="true">
            <div class="modal-alta-rapida-encabezado"><h2>Nuevo proveedor</h2><button type="button" class="cerrar-modal-alta-rapida" data-cerrar-modal="modal-alta-proveedor">&times;</button></div>
            <div id="error-alta-proveedor" class="error-alta-rapida"></div>
            <form id="form-alta-proveedor">@csrf
                <div class="campo-alta-rapida"><label>Empresa</label><input name="empresa" required></div>
                <div class="campo-alta-rapida"><label>Contacto (opcional)</label><input name="contacto"></div>
                <div class="campo-alta-rapida"><label>Teléfono</label><input name="telefono" required></div>
                <div class="campo-alta-rapida"><label>Email (opcional)</label><input type="email" name="email"></div>
                <div class="campo-alta-rapida"><label>Dirección (opcional)</label><input name="direccion"></div>
                <div class="campo-alta-rapida"><label>CUIT (opcional)</label><input name="cuit"></div>
                <input type="hidden" name="activo" value="1">
                <button type="submit" class="boton-alta-rapida">Guardar proveedor</button>
                <button type="button" class="boton-alta-rapida" data-cerrar-modal="modal-alta-proveedor">Cancelar</button>
            </form>
        </section>
    </div>
@endif

@if ($mostrarProducto)
    <div id="modal-alta-producto" class="modal-alta-rapida oculto" aria-hidden="true">
        <section class="modal-alta-rapida-contenido" role="dialog" aria-modal="true">
            <div class="modal-alta-rapida-encabezado"><h2>Nuevo producto</h2><button type="button" class="cerrar-modal-alta-rapida" data-cerrar-modal="modal-alta-producto">&times;</button></div>
            <div id="error-alta-producto" class="error-alta-rapida"></div>
            <form id="form-alta-producto">@csrf
                <fieldset class="grupo-alta-rapida"><legend>Información</legend>
                    <div class="campo-alta-rapida"><label>Nombre del producto</label><input name="nombre" required></div>
                    <div class="campo-alta-rapida"><label>Marca (opcional)</label><input name="marca"></div>
                    <div class="campo-alta-rapida"><label>Descripción (opcional)</label><textarea name="descripcion"></textarea></div>
                </fieldset>
                <fieldset class="grupo-alta-rapida"><legend>Precios y stock</legend>
                    <div class="campo-alta-rapida"><label>Precio de compra</label><input type="number" name="precio_compra" min="0" step="0.01" required></div>
                    <div class="campo-alta-rapida"><label>Precio de venta (opcional)</label><input type="number" name="precio_venta" min="0" step="0.01"></div>
                    <div class="campo-alta-rapida"><label>Stock inicial</label><input type="number" name="stock_actual" min="0" required></div>
                    <div class="campo-alta-rapida"><label>Stock mínimo (opcional)</label><input type="number" name="stock_minimo" min="0" value="1"></div>
                    <div class="campo-alta-rapida"><label>Unidad de medida</label><select name="unidad"><option value="Unidad">Unidad</option><option value="kg">Kilogramo</option><option value="litro">Litro</option><option value="caja">Caja</option></select></div>
                </fieldset>
                <fieldset class="grupo-alta-rapida"><legend>Proveedor y vencimiento</legend>
                    <div class="campo-alta-rapida"><label>Proveedor (opcional)</label><input name="proveedor"></div>
                    <div class="campo-alta-rapida"><label>¿Tiene vencimiento?</label><select name="tiene_vencimiento" id="alta-producto-vencimiento"><option value="0">No</option><option value="1">Sí</option></select></div>
                    <div class="campo-alta-rapida"><label>Fecha de vencimiento (opcional)</label><input type="date" name="fecha_vencimiento"></div>
                </fieldset>
                <button type="submit" class="boton-alta-rapida">Guardar producto</button>
                <button type="button" class="boton-alta-rapida" data-cerrar-modal="modal-alta-producto">Cancelar</button>
            </form>
        </section>
    </div>
@endif

<script>
    (function () {
        const configuraciones = [
            @if ($mostrarCliente) { modal: 'modal-alta-cliente', form: 'form-alta-cliente', error: 'error-alta-cliente', endpoint: '/clientes', selector: @json($selectorCliente), tipo: 'cliente' }, @endif
            @if ($mostrarProveedor) { modal: 'modal-alta-proveedor', form: 'form-alta-proveedor', error: 'error-alta-proveedor', endpoint: '/proveedores', selector: @json($selectorProveedor), tipo: 'proveedor' }, @endif
            @if ($mostrarProducto) { modal: 'modal-alta-producto', form: 'form-alta-producto', error: 'error-alta-producto', endpoint: '/productos', selector: @json($selectorProducto), tipo: 'producto' } @endif
        ];

        function cerrar(id) { const modal = document.getElementById(id); if (modal) { modal.classList.add('oculto'); modal.setAttribute('aria-hidden', 'true'); } }
        function abrir(id) { const modal = document.getElementById(id); if (modal) { modal.classList.remove('oculto'); modal.setAttribute('aria-hidden', 'false'); } }
        document.querySelectorAll('[data-cerrar-modal]').forEach(function (boton) { boton.addEventListener('click', function () { cerrar(boton.dataset.cerrarModal); }); });
        document.querySelectorAll('.modal-alta-rapida').forEach(function (modal) { modal.addEventListener('click', function (event) { if (event.target === modal) cerrar(modal.id); }); });
        document.addEventListener('keydown', function (event) { if (event.key === 'Escape') document.querySelectorAll('.modal-alta-rapida:not(.oculto)').forEach(function (modal) { cerrar(modal.id); }); });

        configuraciones.forEach(function (configuracion) {
            const formulario = document.getElementById(configuracion.form);
            if (!formulario) return;
            formulario.addEventListener('submit', function (event) {
                event.preventDefault();
                const error = document.getElementById(configuracion.error);
                error.classList.remove('visible');
                fetch(configuracion.endpoint, { method: 'POST', headers: { 'X-CSRF-TOKEN': formulario.querySelector('input[name="_token"]').value, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }, body: new FormData(formulario) })
                    .then(async function (response) { const datos = await response.json(); if (!response.ok) throw datos; return datos; })
                    .then(function (datos) {
                        const selector = document.getElementById(configuracion.selector);
                        const entidad = datos[configuracion.tipo];
                        const opcion = document.createElement('option');
                        opcion.value = configuracion.tipo === 'producto' ? entidad.id : (configuracion.tipo === 'proveedor' ? entidad.id : entidad.id);
                        opcion.textContent = configuracion.tipo === 'cliente' ? entidad.nombre + ' ' + entidad.apellido : (configuracion.tipo === 'proveedor' ? entidad.empresa : entidad.nombre);
                        if (configuracion.tipo === 'producto') opcion.dataset.precio = entidad['{{ $campoPrecioProducto }}'];
                        opcion.selected = true;
                        selector.appendChild(opcion);
                        formulario.reset();
                        cerrar(configuracion.modal);
                        const aviso = document.createElement('div'); aviso.className = 'mensaje-alta-rapida'; aviso.textContent = '✓ ' + datos.message; document.body.appendChild(aviso); setTimeout(function () { aviso.remove(); }, 4000);
                    })
                    .catch(function (datos) { const mensajes = datos.errors ? Object.values(datos.errors).flat() : ['No se pudo guardar.']; error.innerHTML = mensajes.map(function (mensaje) { return '<p>' + mensaje + '</p>'; }).join(''); error.classList.add('visible'); });
            });
        });

        @if ($mostrarCliente) document.getElementById('abrir-nuevo-cliente').addEventListener('click', function () { abrir('modal-alta-cliente'); }); @endif
        @if ($mostrarProveedor) document.getElementById('abrir-nuevo-proveedor-compra')?.addEventListener('click', function () { abrir('modal-alta-proveedor'); }); @endif
        @if ($mostrarProducto)
            (document.getElementById('abrir-nuevo-producto-compra') || document.getElementById('abrir-nuevo-producto-venta'))?.addEventListener('click', function () { abrir('modal-alta-producto'); });
        @endif
        @if ($mostrarProducto && $campoPrecioProducto === 'precio_venta') document.getElementById('producto-venta').addEventListener('change', function () { const opcion = this.selectedOptions[0]; if (opcion && opcion.dataset.precio) document.getElementById('precio-venta').value = opcion.dataset.precio; }); @endif
    })();
</script>