@extends('layouts.app')

@section('title', 'Editar venta')

@section('content')

    <style>
        .formulario-venta { max-width: 760px; }
        .grupo-venta { margin: 0 0 24px; padding: 20px; border: 1px solid #dddddd; background: #fafafa; }
        .campo-venta { display: flex; flex-direction: column; gap: 6px; margin-bottom: 16px; }
        .campo-venta:last-child { margin-bottom: 0; }
        .campo-venta input,
        .campo-venta select { max-width: 500px; padding: 9px 10px; border: 1px solid #cccccc; border-radius: 4px; font: inherit; }
        .campo-precio-venta { position: relative; }
        .campo-precio-venta::before { position: absolute; left: 10px; bottom: 10px; z-index: 1; color: #555555; content: '$'; }
        .campo-precio-venta input { padding-left: 26px; }
        .boton-venta { padding: 9px 14px; background: #eeeeee; border: 1px solid #cccccc; border-radius: 4px; cursor: pointer; font-size: 14px; text-decoration: none; color: black; }
        .boton-venta:hover { background: #d7ebff; box-shadow: 0 4px 10px rgba(0, 91, 170, 0.2); }
        .errores-formulario { margin-bottom: 24px; padding: 14px 18px; border: 1px solid #dc3545; background: #f8d7da; color: #842029; }
        .errores-formulario p { margin: 0 0 6px; }
        .errores-formulario p:last-child { margin-bottom: 0; }
        .formas-pago-opciones { display: grid; gap: 10px; margin-top: 8px; }
        .forma-pago-opcion { display: flex; align-items: center; gap: 8px; }
        .forma-pago-opcion input[type="checkbox"] { width: 17px; height: 17px; margin: 0; }
        .montos-pago { display: grid; gap: 12px; margin-top: 16px; }
        .monto-pago.oculto { display: none; }
    </style>

    @php
        $detalle = $venta->detalles->first();
        $formasPago = ['efectivo' => 'Efectivo', 'tarjeta' => 'Tarjeta', 'transferencia' => 'Transferencia', 'cuenta_corriente' => 'Cuenta corriente'];
        $pagosPorForma = $venta->pagos->keyBy('forma_pago');
        $productoSeleccionado = old('producto_id', $detalle && $detalle->producto_id ? $detalle->producto_id : 'otro');
    @endphp

    <h1>Editar venta</h1>

    <form class="formulario-venta" method="POST" action="/ventas/{{ $venta->id }}">
        @csrf
        @method('PUT')

        @if ($errors->any())
            <div class="errores-formulario">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <fieldset class="grupo-venta">
            <legend>Información de la venta</legend>

            <div class="campo-venta">
                <label for="fecha-venta">Fecha</label>
                <input type="date" id="fecha-venta" name="fecha" value="{{ old('fecha', $venta->fecha->format('Y-m-d')) }}" required>
            </div>

            <div class="campo-venta">
                <label for="cliente-venta">Cliente</label>
                <select id="cliente-venta" name="cliente_id">
                    <option value="">No es cliente</option>
                    @foreach ($clientes as $cliente)
                        <option value="{{ $cliente->id }}" {{ (string) old('cliente_id', $venta->cliente_id) === (string) $cliente->id ? 'selected' : '' }}>
                            {{ $cliente->nombre }} {{ $cliente->apellido }}
                        </option>
                    @endforeach
                </select>
            </div>
        </fieldset>

        <fieldset class="grupo-venta">
            <legend>Producto</legend>

            <div class="campo-venta">
                <label for="producto-venta">Producto</label>
                <select id="producto-venta" name="producto_id" required>
                    <option value="">Seleccione un producto</option>
                    <option value="otro" {{ $productoSeleccionado === 'otro' ? 'selected' : '' }}>Otro</option>
                    @foreach ($productos as $producto)
                        <option value="{{ $producto->id }}" data-precio="{{ $producto->precio_venta }}" {{ (string) $productoSeleccionado === (string) $producto->id ? 'selected' : '' }}>
                            {{ $producto->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="campo-venta">
                <label for="cantidad-venta">Cantidad</label>
                <input type="number" id="cantidad-venta" name="cantidad" min="1" step="1" value="{{ old('cantidad', $detalle?->cantidad ?? 1) }}" required>
            </div>

            <div class="campo-venta campo-precio-venta">
                <label for="precio-venta">Precio</label>
                <input type="number" id="precio-venta" name="precio" min="0" step="0.01" value="{{ old('precio', $detalle?->precio ?? 0) }}" required>
            </div>
        </fieldset>

        <fieldset class="grupo-venta">
            <legend>Forma de pago</legend>

            <div class="formas-pago-opciones">
                @foreach ($formasPago as $valor => $etiqueta)
                    @php
                        $pago = $pagosPorForma->get($valor);
                        $seleccionada = old('formas_pago') ? in_array($valor, old('formas_pago'), true) : $pago !== null;
                    @endphp
                    <label class="forma-pago-opcion">
                        <input type="checkbox" name="formas_pago[]" value="{{ $valor }}" {{ $seleccionada ? 'checked' : '' }}>
                        <span>{{ $etiqueta }}</span>
                    </label>
                    <div class="campo-venta monto-pago campo-precio-venta oculto" data-forma-pago="{{ $valor }}">
                        <label for="monto-{{ $valor }}">Monto en {{ $etiqueta }}</label>
                        <input type="number" id="monto-{{ $valor }}" name="montos_pago[{{ $valor }}]" min="0" step="0.01" value="{{ old('montos_pago.' . $valor, $pago?->monto ?? '') }}">
                    </div>
                @endforeach
            </div>
        </fieldset>

        <button type="submit" class="boton-venta">Guardar cambios</button>
        <a href="/ventas" class="boton-venta">Cancelar</a>
    </form>

    <script>
        const selectorProducto = document.getElementById('producto-venta');
        const precioProducto = document.getElementById('precio-venta');
        const opcionesPago = document.querySelectorAll('input[name="formas_pago[]"]');
        const montosPago = document.querySelectorAll('.monto-pago');

        function actualizarPrecioProducto() {
            const opcion = selectorProducto.selectedOptions[0];
            if (selectorProducto.value !== 'otro' && opcion && opcion.dataset.precio !== '') {
                precioProducto.value = opcion.dataset.precio;
            }
        }

        function actualizarMontosPago() {
            const seleccionadas = Array.from(opcionesPago).filter(function (opcion) {
                return opcion.checked;
            });
            const mostrarMontos = seleccionadas.length > 1;

            montosPago.forEach(function (campo) {
                const checkbox = document.querySelector('input[name="formas_pago[]"][value="' + campo.dataset.formaPago + '"]');
                campo.classList.toggle('oculto', !mostrarMontos || !checkbox.checked);
                campo.querySelector('input').required = mostrarMontos && checkbox.checked;
            });
        }

        selectorProducto.addEventListener('change', actualizarPrecioProducto);
        opcionesPago.forEach(function (opcion) {
            opcion.addEventListener('change', actualizarMontosPago);
        });
        actualizarMontosPago();
    </script>

@endsection
