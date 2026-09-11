@extends('layouts.app')

@section('title', 'Editar producto')

@section('content')

    <style>
        .formulario-producto { max-width: 760px; }
        .grupo-producto { margin: 0 0 24px; padding: 20px; border: 1px solid #dddddd; background: #fafafa; }
        .campo-producto, .campo-proveedor-modal { display: flex; flex-direction: column; gap: 6px; margin-bottom: 16px; }
        .campo-producto:last-child, .campo-proveedor-modal:last-child { margin-bottom: 0; }
        .campo-producto input, .campo-producto select, .campo-producto textarea,
        .campo-proveedor-modal input { max-width: 500px; padding: 9px 10px; border: 1px solid #cccccc; border-radius: 4px; font: inherit; }
        .campo-precio-producto { position: relative; }
        .campo-precio-producto::before { position: absolute; left: 10px; bottom: 10px; z-index: 1; color: #555555; content: '$'; }
        .campo-precio-producto input { padding-left: 26px; }
        .campo-producto textarea { min-height: 90px; resize: vertical; }
        .selector-proveedor-linea { display: flex; align-items: flex-end; gap: 10px; }
        .selector-proveedor-linea .campo-producto { flex: 1; }
        #abrir-nuevo-proveedor { margin-bottom: 16px; white-space: nowrap; }
        .boton-producto { padding: 9px 14px; background: #eeeeee; border: 1px solid #cccccc; border-radius: 4px; cursor: pointer; font-size: 14px; text-decoration: none; color: black; }
        .boton-producto:hover { background: #d7ebff; box-shadow: 0 4px 10px rgba(0, 91, 170, 0.2); }
        .errores-proveedor-modal { display: none; margin-bottom: 18px; padding: 12px 16px; background: #f8d7da; border: 1px solid #f1aeb5; color: #842029; }
        .errores-proveedor-modal.visible { display: block; }
        .mensaje-proveedor-exito { display: none; }
        .mensaje-proveedor-exito.visible { position: fixed; top: 20px; right: 20px; display: block; padding: 15px 20px; background: #28a745; border: 0; border-radius: 8px; color: white; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); z-index: 1000; }
        .modal-proveedor { position: fixed; inset: 0; display: flex; align-items: center; justify-content: center; padding: 20px; background: rgba(0, 0, 0, 0.35); z-index: 900; }
        .modal-proveedor.oculto { display: none; }
        .modal-proveedor-contenido { width: min(620px, 100%); max-height: 90vh; padding: 28px; overflow-y: auto; background: white; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25); }
        .modal-proveedor-encabezado { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
        .modal-proveedor-encabezado h2 { margin: 0; }
        .cerrar-modal-proveedor { margin: 0; padding: 4px 10px; font-size: 22px; background: transparent; border: none; border-radius: 4px; cursor: pointer; transition: background-color 0.2s ease; }
        .cerrar-modal-proveedor:hover { background-color: #d0d0d0; }
        .errores-formulario { margin-bottom: 24px; padding: 14px 18px; border: 1px solid #dc3545; background: #f8d7da; color: #842029; }
        .errores-formulario p { margin: 0 0 6px; }
        .errores-formulario p:last-child { margin-bottom: 0; }
    </style>

    <h1>Editar producto</h1>

    <form class="formulario-producto" method="POST" action="/productos/{{ $producto->id }}">
        @csrf
        @method('PUT')

        <div id="mensaje-proveedor-exito" class="mensaje-proveedor-exito"></div>

        @if ($errors->any())
            <div class="errores-formulario">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <fieldset class="grupo-producto">
            <legend>Información del producto</legend>

            <div class="campo-producto">
                <label for="nombre">Nombre del producto</label>
                <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $producto->nombre) }}" required>
            </div>

            <div class="campo-producto">
                <label for="marca">Marca (opcional)</label>
                <input type="text" id="marca" name="marca" value="{{ old('marca', $producto->marca) }}">
            </div>

            <div class="campo-producto">
                <label for="descripcion">Descripción (opcional)</label>
                <textarea id="descripcion" name="descripcion">{{ old('descripcion', $producto->descripcion) }}</textarea>
            </div>
        </fieldset>

        <fieldset class="grupo-producto">
            <legend>Precios y stock</legend>

            <div class="campo-producto campo-precio-producto">
                <label for="precio-compra">Precio de compra</label>
                <input type="number" id="precio-compra" name="precio_compra" value="{{ old('precio_compra', $producto->precio_compra) }}" min="0" step="0.01" required>
            </div>

            <div class="campo-producto campo-precio-producto">
                <label for="precio-venta">Precio de venta (opcional)</label>
                <input type="number" id="precio-venta" name="precio_venta" value="{{ old('precio_venta', $producto->precio_venta) }}" min="0" step="0.01">
            </div>

            <div class="campo-producto">
                <label>Stock actual</label>
                <input type="text" value="{{ $stockActual }}" disabled>
                <small>El stock se gestiona desde el módulo Stock (Entradas, Salidas y Ajustes).</small>
            </div>

            <div class="campo-producto">
                <label for="stock-minimo">Stock mínimo (opcional)</label>
                <input type="number" id="stock-minimo" name="stock_minimo" value="{{ old('stock_minimo', $producto->stock_minimo ?: 1) }}" min="0">
            </div>

            <div class="campo-producto">
                <label for="unidad">Unidad de medida (opcional)</label>
                <select id="unidad" name="unidad">
                    <option value="Unidad" {{ old('unidad', $producto->unidad) === 'Unidad' ? 'selected' : '' }}>Unidad</option>
                    <option value="kg" {{ old('unidad', $producto->unidad) === 'kg' ? 'selected' : '' }}>Kilogramo</option>
                    <option value="litro" {{ old('unidad', $producto->unidad) === 'litro' ? 'selected' : '' }}>Litro</option>
                    <option value="caja" {{ old('unidad', $producto->unidad) === 'caja' ? 'selected' : '' }}>Caja</option>
                </select>
            </div>
        </fieldset>

        <fieldset class="grupo-producto">
            <legend>Proveedor</legend>

            <div class="selector-proveedor-linea">
                <div class="campo-producto">
                    <label for="proveedor">Proveedor</label>
                    <select id="proveedor" name="proveedor">
                        <option value="">Sin proveedor</option>
                        @foreach ($proveedores as $proveedor)
                            <option value="{{ $proveedor->empresa }}" {{ old('proveedor', $producto->proveedor) === $proveedor->empresa ? 'selected' : '' }}>
                                {{ $proveedor->empresa }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="button" class="boton-producto" id="abrir-nuevo-proveedor">+ Nuevo proveedor</button>
            </div>
        </fieldset>

        <fieldset class="grupo-producto">
            <legend>Vencimiento</legend>

            <div class="campo-producto">
                <label for="tiene-vencimiento">¿Tiene vencimiento? (opcional)</label>
                <select id="tiene-vencimiento" name="tiene_vencimiento">
                    <option value="0" {{ old('tiene_vencimiento', $producto->tiene_vencimiento) == 0 ? 'selected' : '' }}>No</option>
                    <option value="1" {{ old('tiene_vencimiento', $producto->tiene_vencimiento) == 1 ? 'selected' : '' }}>Sí</option>
                </select>
            </div>

            <div class="campo-producto">
                <label for="fecha-vencimiento">Fecha de vencimiento (opcional)</label>
                <input type="date" id="fecha-vencimiento" name="fecha_vencimiento" value="{{ old('fecha_vencimiento', optional($producto->fecha_vencimiento)->format('Y-m-d')) }}">
            </div>
        </fieldset>

        <button type="submit" class="boton-producto">Guardar cambios</button>
        <a href="/productos" class="boton-producto">Cancelar</a>
    </form>

    <div id="modal-nuevo-proveedor" class="modal-proveedor oculto" aria-hidden="true">
        <section class="modal-proveedor-contenido" role="dialog" aria-modal="true" aria-labelledby="nuevo-proveedor-titulo">
            <div class="modal-proveedor-encabezado">
                <h2 id="nuevo-proveedor-titulo">Nuevo proveedor</h2>
                <button type="button" class="cerrar-modal-proveedor" aria-label="Cerrar nuevo proveedor">&times;</button>
            </div>
            <div id="errores-proveedor-modal" class="errores-proveedor-modal"></div>
            <form id="form-nuevo-proveedor">
                @csrf
                <div class="campo-proveedor-modal"><label for="proveedor-empresa">Empresa</label><input type="text" id="proveedor-empresa" name="empresa" required></div>
                <div class="campo-proveedor-modal"><label for="proveedor-contacto">Contacto (opcional)</label><input type="text" id="proveedor-contacto" name="contacto"></div>
                <div class="campo-proveedor-modal"><label for="proveedor-telefono">Teléfono</label><input type="text" id="proveedor-telefono" name="telefono" required></div>
                <div class="campo-proveedor-modal"><label for="proveedor-email">Email (opcional)</label><input type="email" id="proveedor-email" name="email"></div>
                <div class="campo-proveedor-modal"><label for="proveedor-direccion">Dirección (opcional)</label><input type="text" id="proveedor-direccion" name="direccion"></div>
                <div class="campo-proveedor-modal"><label for="proveedor-cuit">CUIT (opcional)</label><input type="text" id="proveedor-cuit" name="cuit"></div>
                <input type="hidden" name="activo" value="1">
                <button type="submit" class="boton-producto">Guardar proveedor</button>
                <button type="button" class="boton-producto" id="cerrar-nuevo-proveedor">Cancelar</button>
            </form>
        </section>
    </div>

    <script>
        const modalNuevoProveedor = document.getElementById('modal-nuevo-proveedor');
        const selectorProveedor = document.getElementById('proveedor');
        const formularioNuevoProveedor = document.getElementById('form-nuevo-proveedor');
        const erroresProveedorModal = document.getElementById('errores-proveedor-modal');

        function ocultarNuevoProveedor() {
            modalNuevoProveedor.classList.add('oculto');
            modalNuevoProveedor.setAttribute('aria-hidden', 'true');
        }

        document.getElementById('abrir-nuevo-proveedor').addEventListener('click', function () {
            modalNuevoProveedor.classList.remove('oculto');
            modalNuevoProveedor.setAttribute('aria-hidden', 'false');
        });
        document.querySelector('.cerrar-modal-proveedor').addEventListener('click', ocultarNuevoProveedor);
        document.getElementById('cerrar-nuevo-proveedor').addEventListener('click', ocultarNuevoProveedor);

        formularioNuevoProveedor.addEventListener('submit', function (event) {
            event.preventDefault();
            erroresProveedorModal.classList.remove('visible');
            fetch('/proveedores', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': formularioNuevoProveedor.querySelector('input[name="_token"]').value,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: new FormData(formularioNuevoProveedor)
            }).then(async function (response) {
                const datos = await response.json();
                if (!response.ok) { throw datos; }
                return datos;
            }).then(function (datos) {
                const opcion = document.createElement('option');
                opcion.value = datos.proveedor.empresa;
                opcion.textContent = datos.proveedor.empresa;
                opcion.selected = true;
                selectorProveedor.appendChild(opcion);
                formularioNuevoProveedor.reset();
                ocultarNuevoProveedor();

                const aviso = document.getElementById('mensaje-proveedor-exito');
                aviso.textContent = `✓ ${datos.message}`;
                aviso.classList.add('visible');
                setTimeout(function () {
                    aviso.classList.remove('visible');
                }, 4000);
            }).catch(function (datos) {
                const mensajes = datos.errors ? Object.values(datos.errors).flat() : ['No se pudo cargar el proveedor.'];
                erroresProveedorModal.innerHTML = mensajes.map(mensaje => `<p>${mensaje}</p>`).join('');
                erroresProveedorModal.classList.add('visible');
            });
        });
    </script>

@endsection
