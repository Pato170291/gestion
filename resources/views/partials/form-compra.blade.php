@php
    $detalle = $compra?->detalles->first();
    $formasPago = ['efectivo' => 'Efectivo', 'tarjeta' => 'Tarjeta', 'transferencia' => 'Transferencia', 'cuenta_corriente' => 'Cuenta corriente'];
    $pagosPorForma = $compra ? $compra->pagos->keyBy('forma_pago') : collect();
    $productoSeleccionado = old('producto_id', $detalle?->producto_id ?? 'otro');
@endphp

<style>
    .formulario-compra { max-width: 760px; }
    .grupo-compra { margin: 0 0 24px; padding: 20px; border: 1px solid #ddd; background: #fafafa; }
    .campo-compra { display: flex; flex-direction: column; gap: 6px; margin-bottom: 16px; }
    .campo-compra:last-child { margin-bottom: 0; }
    .campo-compra input, .campo-compra select { max-width: 500px; padding: 9px 10px; border: 1px solid #ccc; border-radius: 4px; font: inherit; }
    .campo-precio-compra, .campo-monto-pago { position: relative; max-width: 500px; }
    .campo-precio-compra::before, .campo-monto-pago::before { position: absolute; left: 10px; bottom: 10px; z-index: 1; color: #555; content: '$'; }
    .campo-precio-compra input, .campo-monto-pago input { width: 100%; padding-left: 26px; }
    .boton-compra { display: inline-block; padding: 6px 8px; background-color: #eee; border: 1px solid #ccc; border-radius: 4px; cursor: pointer; font-size: 14px; text-decoration: none; color: #000; margin-right: 2px; transition: background-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease; }
    .boton-compra:hover { background-color: #d7ebff; box-shadow: 0 4px 10px rgba(0, 91, 170, 0.2); transform: translateY(-2px); }
    .errores-formulario { margin-bottom: 24px; padding: 14px 18px; border: 1px solid #dc3545; background: #f8d7da; color: #842029; }
    .errores-formulario p { margin: 0 0 6px; }
    .formas-pago-opciones { display: grid; gap: 10px; }
    .forma-pago-opcion { display: flex; align-items: center; gap: 8px; }
    .forma-pago-opcion input { width: 17px; height: 17px; margin: 0; }
    .error-forma-pago { display: none; margin-top: 8px; color: #842029; }
    .error-forma-pago.visible { display: block; }
    .montos-pago.oculto, .monto-pago.oculto { display: none; }
    .selector-compra-linea { display: flex; align-items: flex-end; gap: 10px; }
    .selector-compra-linea .campo-compra { flex: 1; }
    .selector-compra-linea .boton-compra { margin-bottom: 16px; white-space: nowrap; }
</style>

@if ($errors->any())
    <div class="errores-formulario">
        @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
    </div>
@endif

<form class="formulario-compra" method="POST" action="{{ $action }}">
    @csrf
    @if ($compra)<input type="hidden" name="_method" value="PUT">@endif

    <fieldset class="grupo-compra">
        <legend>Información de la compra</legend>
        <div class="campo-compra">
            <label for="fecha-compra">Fecha</label>
            <input type="date" id="fecha-compra" name="fecha" value="{{ old('fecha', $compra?->fecha?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required>
        </div>
        <div class="selector-compra-linea">
            <div class="campo-compra">
                <label for="proveedor-compra">Proveedor (opcional)</label>
                <select id="proveedor-compra" name="proveedor_id">
                    <option value="">Sin proveedor</option>
                    @foreach ($proveedores as $proveedor)
                        <option value="{{ $proveedor->id }}" {{ (string) old('proveedor_id', $compra?->proveedor_id) === (string) $proveedor->id ? 'selected' : '' }}>{{ $proveedor->empresa }}</option>
                    @endforeach
                </select>
            </div>
            <button type="button" class="boton-compra" id="abrir-nuevo-proveedor-compra">+ Nuevo proveedor</button>
        </div>
    </fieldset>

    <fieldset class="grupo-compra">
        <legend>Producto</legend>
        <div class="selector-compra-linea">
            <div class="campo-compra">
                <label for="producto-compra">Producto</label>
                <select id="producto-compra" name="producto_id" required>
                    <option value="otro" {{ $productoSeleccionado === 'otro' ? 'selected' : '' }}>Otro</option>
                    @foreach ($productos as $producto)
                        <option value="{{ $producto->id }}" data-precio="{{ $producto->precio_compra }}" {{ (string) $productoSeleccionado === (string) $producto->id ? 'selected' : '' }}>{{ $producto->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <button type="button" class="boton-compra" id="abrir-nuevo-producto-compra">+ Nuevo producto</button>
        </div>
        <div class="campo-compra">
            <label for="cantidad-compra">Cantidad</label>
            <input type="number" id="cantidad-compra" name="cantidad" min="1" step="1" value="{{ old('cantidad', $detalle?->cantidad ?? 1) }}" required>
        </div>
        <div class="campo-compra campo-precio-compra">
            <label for="precio-compra">Precio de compra</label>
            <input type="number" id="precio-compra" name="precio" min="0" step="0.01" value="{{ old('precio', $detalle?->precio ?? 0) }}" required>
        </div>
    </fieldset>

    <fieldset class="grupo-compra">
        <legend>Forma de pago</legend>
        <div class="formas-pago-opciones">
            <div class="campo-compra">
                <span>Formas de pago</span>
                <div class="formas-pago-opciones" id="formas-pago-compra">
            @foreach ($formasPago as $valor => $etiqueta)
                @php $pago = $pagosPorForma->get($valor); $seleccionada = old('formas_pago') ? in_array($valor, old('formas_pago'), true) : $pago !== null; @endphp
                <label class="forma-pago-opcion"><input type="checkbox" name="formas_pago[]" value="{{ $valor }}" {{ $seleccionada ? 'checked' : '' }}><span>{{ $etiqueta }}</span></label>
                <div class="campo-compra campo-monto-pago monto-pago oculto" data-forma-pago="{{ $valor }}">
                    <label for="monto-{{ $valor }}">Monto en {{ $etiqueta }}</label>
                    <input type="number" id="monto-{{ $valor }}" name="montos_pago[{{ $valor }}]" min="0" step="0.01" value="{{ old('montos_pago.' . $valor, $pago?->monto ?? '') }}">
                </div>
            @endforeach
                </div>
                <div id="error-forma-pago-compra" class="error-forma-pago">Seleccione al menos una forma de pago.</div>
            </div>
        </div>
    </fieldset>

    <button type="submit" class="boton-compra">{{ $compra ? 'Guardar cambios' : 'Guardar compra' }}</button>
    <a href="/compras" class="boton-compra">Cancelar</a>
</form>

@include('partials.modal-altas-rapidas', [
    'mostrarProveedor' => true,
    'mostrarProducto' => true,
    'campoPrecioProducto' => 'precio_compra',
    'selectorProducto' => 'producto-compra',
    'selectorProveedor' => 'proveedor-compra',
])

<script>
    const selectorProductoCompra = document.getElementById('producto-compra');
    const precioProductoCompra = document.getElementById('precio-compra');
    const opcionesPagoCompra = document.querySelectorAll('#formas-pago-compra input[name="formas_pago[]"]');
    const montosPagoCompra = document.querySelectorAll('.monto-pago');
    function actualizarPrecioCompra() { const opcion = selectorProductoCompra.selectedOptions[0]; if (selectorProductoCompra.value !== 'otro' && opcion) precioProductoCompra.value = opcion.dataset.precio; }
    const errorFormaPagoCompra = document.getElementById('error-forma-pago-compra');
    function actualizarMontosCompra() { const seleccionadas = Array.from(opcionesPagoCompra).filter(function (opcion) { return opcion.checked; }); const multiples = seleccionadas.length > 1; errorFormaPagoCompra.classList.toggle('visible', seleccionadas.length === 0); montosPagoCompra.forEach(function (campo) { const checkbox = document.querySelector('input[name="formas_pago[]"][value="' + campo.dataset.formaPago + '"]'); campo.classList.toggle('oculto', !multiples || !checkbox.checked); campo.querySelector('input').required = multiples && checkbox.checked; }); }
    selectorProductoCompra.addEventListener('change', actualizarPrecioCompra);
    opcionesPagoCompra.forEach(function (opcion) { opcion.addEventListener('change', actualizarMontosCompra); });
    actualizarMontosCompra();
    document.querySelector('.formulario-compra').addEventListener('submit', function (event) { const hayFormaDePago = Array.from(opcionesPagoCompra).some(function (opcion) { return opcion.checked; }); errorFormaPagoCompra.classList.toggle('visible', !hayFormaDePago); if (!hayFormaDePago) event.preventDefault(); });
</script>