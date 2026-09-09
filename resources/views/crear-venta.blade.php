@extends('layouts.app')

@section('title', 'Nueva venta')

@section('content')

    <style>
        .formulario-venta {
            max-width: 760px;
        }

        .grupo-venta {
            margin: 0 0 24px;
            padding: 20px;
            border: 1px solid #dddddd;
            background: #fafafa;
        }

        .campo-venta {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 16px;
        }

        .campo-venta:last-child {
            margin-bottom: 0;
        }

        .campo-venta input,
        .campo-venta select,
        .campo-venta textarea {
            max-width: 500px;
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

        #abrir-nuevo-producto {
            margin-top: 14px;
        }

        #abrir-nuevo-cliente {
            margin-bottom: 16px;
            white-space: nowrap;
        }

        #abrir-nuevo-cliente {
            margin-bottom: 16px;
            white-space: nowrap;
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

        .boton-venta:hover,
        .boton-cliente-modal:hover {
            background: #d7ebff;
            box-shadow: 0 4px 10px rgba(0, 91, 170, 0.2);
        }

        .mensaje-cliente-exito,
        .mensaje-producto-exito {
            position: fixed;
            top: 20px;
            right: 20px;
            display: block;
            padding: 15px 20px;
            background: #28a745;
            border: 0;
            border-radius: 8px;
            color: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }

        .mensaje-cliente-exito {
            display: none;
        }

        .mensaje-cliente-exito.visible,
        .errores-cliente-modal.visible {
            display: block;
        }

        .modal-cliente {
            position: fixed;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: rgba(0, 0, 0, 0.35);
            z-index: 900;
        }

        .modal-cliente.oculto {
            display: none;
        }

        .modal-cliente-contenido {
            width: min(620px, 100%);
            padding: 28px;
            background: white;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
        }

        .modal-cliente-encabezado {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .modal-cliente-encabezado h2 {
            margin: 0;
        }

        .cerrar-modal-cliente {
            padding: 4px 10px;
            font-size: 22px;
        }

        .campo-cliente-modal {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 16px;
        }

        .campo-cliente-modal input {
            max-width: 500px;
            padding: 9px 10px;
            border: 1px solid #cccccc;
            border-radius: 4px;
            font: inherit;
        }

        .errores-cliente-modal {
            display: none;
            margin-bottom: 18px;
            padding: 12px 16px;
            background: #f8d7da;
            border: 1px solid #f1aeb5;
            color: #842029;
        }

        .mensaje-producto-exito,
        .errores-producto-modal {
            display: none;
            margin-bottom: 18px;
            padding: 12px 16px;
        }

        .mensaje-producto-exito.visible {
            display: block;
            margin-bottom: 0;
        }

        .errores-producto-modal.visible {
            display: block;
            background: #f8d7da;
            border: 1px solid #f1aeb5;
            color: #842029;
        }

        .fila-producto-venta {
            display: grid;
            grid-template-columns: minmax(180px, 1fr) 120px 150px 150px;
            gap: 12px;
            align-items: start;
        }

        .fila-producto-venta .campo-venta {
            margin-bottom: 0;
        }

        .total-venta {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 20px;
            font-size: 20px;
            font-weight: bold;
        }

        .montos-pago {
            display: grid;
            gap: 12px;
            margin-top: 16px;
        }

        .campo-precio-venta,
        .campo-subtotal-venta,
        .campo-monto-pago {
            position: relative;
        }

        .campo-precio-venta input,
        .campo-subtotal-venta input,
        .campo-monto-pago input {
            padding-left: 26px;
        }

        .campo-precio-venta::before,
        .campo-subtotal-venta::before,
        .campo-monto-pago::before {
            position: absolute;
            left: 9px;
            bottom: 10px;
            z-index: 1;
            color: #555555;
            content: '$';
        }

        .formas-pago-opciones {
            display: grid;
            gap: 10px;
            margin-top: 8px;
        }

        .forma-pago-opcion {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .forma-pago-opcion input {
            width: 17px;
            height: 17px;
            margin: 0;
        }

        .error-forma-pago {
            display: none;
            margin-top: 8px;
            color: #842029;
        }

        .error-forma-pago.visible {
            display: block;
        }

        @media (max-width: 700px) {
            .fila-producto-venta {
                grid-template-columns: 1fr 1fr;
            }

            .selector-cliente-linea {
                align-items: stretch;
                flex-direction: column;
            }

            #abrir-nuevo-cliente {
                align-self: flex-start;
                margin-bottom: 0;
            }
        }

        .modal-producto-contenido {
            width: min(760px, 100%);
            max-height: 90vh;
            overflow-y: auto;
        }

        .grupo-producto-modal {
            margin: 0 0 18px;
            padding: 16px;
            border: 1px solid #dddddd;
            background: #fafafa;
        }

        .campo-producto-modal {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 14px;
        }

        .campo-producto-modal:last-child {
            margin-bottom: 0;
        }

        .campo-producto-modal input,
        .campo-producto-modal select,
        .campo-producto-modal textarea {
            width: 100%;
            max-width: 500px;
            padding: 9px 10px;
            border: 1px solid #cccccc;
            border-radius: 4px;
            font: inherit;
        }

        .campo-producto-modal textarea {
            min-height: 70px;
            resize: vertical;
        }

        .vencimiento-producto-modal.oculto {
            display: none;
        }
    </style>

    <h1>Nueva venta</h1>

    <form class="formulario-venta" id="form-nueva-venta" method="POST" action="/ventas">
        @csrf
        <fieldset class="grupo-venta">
            <legend>Información de la venta</legend>

            <div class="campo-venta">
                <label for="fecha-venta">Fecha</label>
                <input type="date" id="fecha-venta" name="fecha" value="{{ now()->format('Y-m-d') }}">
            </div>

            <div class="selector-cliente-linea">
                <div class="campo-venta">
                    <label for="cliente-venta">Cliente (opcional)</label>
                    <select id="cliente-venta" name="cliente_id">
                        <option value="">No es cliente</option>
                        @foreach ($clientes as $cliente)
                            <option value="{{ $cliente->id }}">{{ $cliente->nombre }} {{ $cliente->apellido }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="button" class="boton-venta" id="abrir-nuevo-cliente">+ Nuevo cliente</button>
            </div>
        </fieldset>

        <fieldset class="grupo-venta">
            <legend>Productos</legend>

            <div class="fila-producto-venta">
                <div class="campo-venta">
                    <label for="producto-venta">Producto</label>
                    <select id="producto-venta" name="producto_id" required>
                        <option value="">Seleccione un producto</option>
                        <option value="otro">Otro</option>
                        @foreach ($productos as $producto)
                            <option value="{{ $producto->id }}" data-precio="{{ $producto->precio_venta }}">
                                {{ $producto->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="campo-venta">
                    <label for="cantidad-venta">Cantidad</label>
                    <input type="number" id="cantidad-venta" name="cantidad" min="1" step="1" value="1" required>
                </div>

                <div class="campo-venta campo-precio-venta">
                    <label for="precio-venta">Precio</label>
                    <input type="number" id="precio-venta" name="precio" min="0" step="0.01" required>
                </div>

                <div class="campo-venta campo-subtotal-venta">
                    <label for="subtotal-venta">Subtotal</label>
                    <input type="text" id="subtotal-venta" name="subtotal" value="$0,00" readonly>
                </div>
            </div>

            <button type="button" class="boton-venta" id="abrir-nuevo-producto">+ Nuevo producto</button>

            <div class="total-venta">
                <span>Total:</span>
                <span id="total-venta">$0,00</span>
            </div>
        </fieldset>

        <fieldset class="grupo-venta">
            <legend>Forma de pago</legend>

            <div class="campo-venta">
                <span>Formas de pago</span>
                <div class="formas-pago-opciones" id="formas-pago-venta">
                    <label class="forma-pago-opcion">
                        <input type="checkbox" name="formas_pago[]" value="efectivo">
                        <span>Efectivo</span>
                    </label>
                    <label class="forma-pago-opcion">
                        <input type="checkbox" name="formas_pago[]" value="tarjeta">
                        <span>Tarjeta</span>
                    </label>
                    <label class="forma-pago-opcion">
                        <input type="checkbox" name="formas_pago[]" value="transferencia">
                        <span>Transferencia</span>
                    </label>
                    <label class="forma-pago-opcion">
                        <input type="checkbox" name="formas_pago[]" value="cuenta_corriente">
                        <span>Cuenta corriente</span>
                    </label>
                </div>
                <div id="error-forma-pago" class="error-forma-pago">Seleccione al menos una forma de pago.</div>
            </div>

            <div id="montos-pago" class="montos-pago"></div>
        </fieldset>

        <button type="submit" class="boton-venta" id="guardar-venta">Guardar</button>
        <a href="/ventas" class="boton-venta">Cancelar</a>
    </form>

    <div id="modal-nuevo-cliente" class="modal-cliente oculto" aria-hidden="true">
        <section class="modal-cliente-contenido" role="dialog" aria-modal="true" aria-labelledby="nuevo-cliente-titulo">
            <div class="modal-cliente-encabezado">
                <h2 id="nuevo-cliente-titulo">Nuevo cliente</h2>
                <button type="button" class="cerrar-modal-cliente" aria-label="Cerrar nuevo cliente">&times;</button>
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
                <button type="button" class="boton-cliente-modal" id="cerrar-nuevo-cliente">Cancelar</button>
            </form>
        </section>
    </div>

    <div id="modal-nuevo-producto" class="modal-cliente oculto" aria-hidden="true">
        <section class="modal-cliente-contenido modal-producto-contenido" role="dialog" aria-modal="true" aria-labelledby="nuevo-producto-titulo">
            <div class="modal-cliente-encabezado">
                <h2 id="nuevo-producto-titulo">Nuevo producto</h2>
                <button type="button" class="cerrar-modal-producto" aria-label="Cerrar nuevo producto">&times;</button>
            </div>

            <div id="errores-producto-modal" class="errores-producto-modal"></div>

            <form id="form-nuevo-producto">
                @csrf
                <fieldset class="grupo-producto-modal">
                    <legend>Información</legend>
                    <div class="campo-producto-modal">
                        <label for="producto-nombre">Nombre del producto</label>
                        <input type="text" id="producto-nombre" name="nombre" required>
                    </div>
                    <div class="campo-producto-modal">
                        <label for="producto-marca">Marca (opcional)</label>
                        <input type="text" id="producto-marca" name="marca">
                    </div>
                    <div class="campo-producto-modal">
                        <label for="producto-descripcion">Descripción (opcional)</label>
                        <textarea id="producto-descripcion" name="descripcion"></textarea>
                    </div>
                </fieldset>

                <fieldset class="grupo-producto-modal">
                    <legend>Precios y stock</legend>
                    <div class="campo-producto-modal">
                        <label for="producto-precio-compra">Precio de compra</label>
                        <input type="number" id="producto-precio-compra" name="precio_compra" min="0" step="0.01" required>
                    </div>
                    <div class="campo-producto-modal">
                        <label for="producto-precio-venta">Precio de venta (opcional)</label>
                        <input type="number" id="producto-precio-venta" name="precio_venta" min="0" step="0.01">
                    </div>
                    <div class="campo-producto-modal">
                        <label for="producto-stock-inicial">Stock inicial</label>
                        <input type="number" id="producto-stock-inicial" name="stock_actual" min="0" required>
                    </div>
                    <div class="campo-producto-modal">
                        <label for="producto-stock-minimo">Stock mínimo (opcional)</label>
                        <input type="number" id="producto-stock-minimo" name="stock_minimo" min="0" value="1">
                    </div>
                    <div class="campo-producto-modal">
                        <label for="producto-unidad">Unidad de medida (opcional)</label>
                        <select id="producto-unidad" name="unidad">
                            <option value="Unidad">Unidad</option>
                            <option value="kg">Kilogramo</option>
                            <option value="litro">Litro</option>
                            <option value="caja">Caja</option>
                        </select>
                    </div>
                </fieldset>

                <fieldset class="grupo-producto-modal">
                    <legend>Proveedor y vencimiento</legend>
                    <div class="campo-producto-modal">
                        <label for="producto-proveedor">Proveedor (opcional)</label>
                        <input type="text" id="producto-proveedor" name="proveedor">
                    </div>
                    <div class="campo-producto-modal">
                        <label for="producto-tiene-vencimiento">¿Tiene vencimiento? (opcional)</label>
                        <select id="producto-tiene-vencimiento" name="tiene_vencimiento">
                            <option value="0">No</option>
                            <option value="1">Sí</option>
                        </select>
                    </div>
                    <div id="vencimiento-producto-modal" class="campo-producto-modal vencimiento-producto-modal oculto">
                        <label for="producto-fecha-vencimiento">Fecha de vencimiento (opcional)</label>
                        <input type="date" id="producto-fecha-vencimiento" name="fecha_vencimiento" disabled>
                    </div>
                </fieldset>

                <button type="submit" class="boton-cliente-modal">Guardar producto</button>
                <button type="button" class="boton-cliente-modal" id="cerrar-nuevo-producto">Cancelar</button>
            </form>
        </section>
    </div>

    <script>
        const modalNuevoCliente = document.getElementById('modal-nuevo-cliente');
        const selectorClienteVenta = document.getElementById('cliente-venta');
        const formularioNuevoCliente = document.getElementById('form-nuevo-cliente');
        const erroresClienteModal = document.getElementById('errores-cliente-modal');
        const selectorProductoVenta = document.getElementById('producto-venta');
        const cantidadVenta = document.getElementById('cantidad-venta');
        const precioVenta = document.getElementById('precio-venta');
        const subtotalVenta = document.getElementById('subtotal-venta');
        const totalVenta = document.getElementById('total-venta');
        const formasPagoVenta = document.querySelectorAll('#formas-pago-venta input[type="checkbox"]');
        const montosPago = document.getElementById('montos-pago');
        const errorFormaPago = document.getElementById('error-forma-pago');
        const modalNuevoProducto = document.getElementById('modal-nuevo-producto');
        const formularioNuevoProducto = document.getElementById('form-nuevo-producto');
        const erroresProductoModal = document.getElementById('errores-producto-modal');

        function mostrarNuevoCliente() {
            modalNuevoCliente.classList.remove('oculto');
            modalNuevoCliente.setAttribute('aria-hidden', 'false');
        }

        function ocultarNuevoCliente() {
            modalNuevoCliente.classList.add('oculto');
            modalNuevoCliente.setAttribute('aria-hidden', 'true');
        }

        document.getElementById('abrir-nuevo-cliente').addEventListener('click', mostrarNuevoCliente);
        document.querySelector('.cerrar-modal-cliente').addEventListener('click', ocultarNuevoCliente);
        document.getElementById('cerrar-nuevo-cliente').addEventListener('click', ocultarNuevoCliente);

        modalNuevoCliente.addEventListener('click', function (event) {
            if (event.target === modalNuevoCliente) {
                ocultarNuevoCliente();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                ocultarNuevoCliente();
            }
        });

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
                ocultarNuevoCliente();

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

        function actualizarPrecioProducto() {
            const opcion = selectorProductoVenta.selectedOptions[0];
            const esOtro = selectorProductoVenta.value === 'otro';
            const precioCargado = opcion ? opcion.dataset.precio : '';

            if (!esOtro && precioCargado !== undefined && precioCargado !== '') {
                precioVenta.value = precioCargado;
                precioVenta.readOnly = true;
            } else {
                if (!esOtro) {
                    precioVenta.value = '';
                }
                precioVenta.readOnly = false;
            }

            actualizarSubtotal();
        }

        function actualizarSubtotal() {
            const cantidad = Number(cantidadVenta.value) || 0;
            const precio = Number(precioVenta.value) || 0;
            const subtotal = cantidad * precio;

            subtotalVenta.value = '$' + subtotal.toLocaleString('es-AR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            totalVenta.textContent = '$' + subtotal.toLocaleString('es-AR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        selectorProductoVenta.addEventListener('change', actualizarPrecioProducto);
        cantidadVenta.addEventListener('input', actualizarSubtotal);
        precioVenta.addEventListener('input', actualizarSubtotal);

        function actualizarMontosPago() {
            const formasSeleccionadas = Array.from(formasPagoVenta).filter(function (opcion) {
                return opcion.checked;
            });

            montosPago.innerHTML = '';
            errorFormaPago.classList.toggle('visible', formasSeleccionadas.length === 0);

            if (formasSeleccionadas.length < 2) {
                return;
            }

            formasSeleccionadas.forEach(function (opcion) {
                const campo = document.createElement('div');
                campo.className = 'campo-venta campo-monto-pago';
                campo.innerHTML = `
                    <label for="monto-${opcion.value}">Monto en ${opcion.parentElement.textContent.trim()}</label>
                    <input type="number" id="monto-${opcion.value}" name="montos_pago[${opcion.value}]" min="0" step="0.01" required>
                `;
                montosPago.appendChild(campo);
            });
        }

        formasPagoVenta.forEach(function (opcion) {
            opcion.addEventListener('change', actualizarMontosPago);
        });

        actualizarMontosPago();

        document.getElementById('form-nueva-venta').addEventListener('submit', function (event) {
            const hayFormaDePago = Array.from(formasPagoVenta).some(function (opcion) {
                return opcion.checked;
            });

            errorFormaPago.classList.toggle('visible', !hayFormaDePago);

            if (!hayFormaDePago) {
                event.preventDefault();
                formasPagoVenta[0].focus();
            }
        });

        function mostrarNuevoProducto() {
            modalNuevoProducto.classList.remove('oculto');
            modalNuevoProducto.setAttribute('aria-hidden', 'false');
        }

        function ocultarNuevoProducto() {
            modalNuevoProducto.classList.add('oculto');
            modalNuevoProducto.setAttribute('aria-hidden', 'true');
        }

        document.getElementById('abrir-nuevo-producto').addEventListener('click', mostrarNuevoProducto);
        document.querySelector('.cerrar-modal-producto').addEventListener('click', ocultarNuevoProducto);
        document.getElementById('cerrar-nuevo-producto').addEventListener('click', ocultarNuevoProducto);

        modalNuevoProducto.addEventListener('click', function (event) {
            if (event.target === modalNuevoProducto) {
                ocultarNuevoProducto();
            }
        });

        document.getElementById('producto-tiene-vencimiento').addEventListener('change', function (event) {
            const campoFecha = document.getElementById('vencimiento-producto-modal');
            const fecha = document.getElementById('producto-fecha-vencimiento');
            const tieneVencimiento = event.target.value === '1';

            campoFecha.classList.toggle('oculto', !tieneVencimiento);
            fecha.disabled = !tieneVencimiento;
        });

        formularioNuevoProducto.addEventListener('submit', function (event) {
            event.preventDefault();
            erroresProductoModal.classList.remove('visible');

            fetch('/productos', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': formularioNuevoProducto.querySelector('input[name="_token"]').value,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: new FormData(formularioNuevoProducto)
            })
            .then(async function (response) {
                const datos = await response.json();

                if (!response.ok) {
                    throw datos;
                }

                return datos;
            })
            .then(function (datos) {
                const producto = datos.producto;
                const opcion = document.createElement('option');
                opcion.value = producto.id;
                opcion.dataset.precio = producto.precio_venta || '';
                opcion.textContent = producto.nombre;
                opcion.selected = true;
                selectorProductoVenta.appendChild(opcion);
                formularioNuevoProducto.reset();
                ocultarNuevoProducto();
                actualizarPrecioProducto();

                const aviso = document.createElement('div');
                aviso.className = 'mensaje-producto-exito visible';
                aviso.textContent = '✓ ' + datos.message;
                document.body.appendChild(aviso);
                setTimeout(function () {
                    aviso.remove();
                }, 4000);
            })
            .catch(function (datos) {
                const mensajes = datos.errors
                    ? Object.values(datos.errors).flat()
                    : ['No se pudo cargar el producto.'];
                erroresProductoModal.innerHTML = mensajes.map(mensaje => `<p>${mensaje}</p>`).join('');
                erroresProductoModal.classList.add('visible');
            });
        });
    </script>

@endsection
