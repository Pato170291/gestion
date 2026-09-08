@extends('layouts.app')

@section('title', 'Nuevo producto')

@section('content')

    <style>
        .formulario-producto {
            max-width: 760px;
        }

        .grupo-producto {
            margin: 0 0 24px;
            padding: 20px;
            border: 1px solid #dddddd;
            background: #fafafa;
        }

        .grupo-producto h2 {
            margin-top: 0;
            font-size: 20px;
        }

        .campo-producto {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 16px;
        }

        .campo-producto:last-child {
            margin-bottom: 0;
        }

        .campo-producto input,
        .campo-producto select,
        .campo-producto textarea {
            max-width: 500px;
            padding: 9px 10px;
            border: 1px solid #cccccc;
            border-radius: 4px;
            font: inherit;
        }

        .campo-producto textarea {
            min-height: 90px;
            resize: vertical;
        }

        .boton-producto {
            padding: 9px 14px;
            background: #eeeeee;
            border: 1px solid #cccccc;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        #fecha-vencimiento-contenedor.oculto {
            display: none;
        }
    </style>

    <h1>Nuevo producto</h1>

    <form class="formulario-producto">
        <fieldset class="grupo-producto">
            <legend>Información</legend>

            <div class="campo-producto">
                <label for="nombre">Nombre del producto</label>
                <input type="text" id="nombre" name="nombre" placeholder="Ej.: Coca Cola 1.5L" required>
            </div>

            <div class="campo-producto">
                <label for="marca">Marca (opcional)</label>
                <input type="text" id="marca" name="marca">
            </div>

            <div class="campo-producto">
                <label for="descripcion">Descripción (opcional)</label>
                <textarea id="descripcion" name="descripcion"></textarea>
            </div>
        </fieldset>

        <fieldset class="grupo-producto">
            <legend>Precios</legend>

            <div class="campo-producto">
                <label for="precio_compra">Precio de compra</label>
                <input type="number" id="precio_compra" name="precio_compra" min="0" step="0.01" required>
            </div>

            <div class="campo-producto">
                <label for="precio_venta">Precio de venta (opcional)</label>
                <input type="number" id="precio_venta" name="precio_venta" min="0" step="0.01">
            </div>
        </fieldset>

        <fieldset class="grupo-producto">
            <legend>Stock</legend>

            <div class="campo-producto">
                <label for="stock_actual">Stock actual (opcional)</label>
                <input type="number" id="stock_actual" name="stock_actual" min="0">
            </div>

            <div class="campo-producto">
                <label for="stock_minimo">Stock mínimo (opcional)</label>
                <input type="number" id="stock_minimo" name="stock_minimo" min="0">
            </div>

            <div class="campo-producto">
                <label for="unidad">Unidad de medida (opcional)</label>
                <select id="unidad" name="unidad">
                    <option value="unidad">Unidad</option>
                    <option value="kg">Kilogramo</option>
                    <option value="litro">Litro</option>
                    <option value="caja">Caja</option>
                </select>
            </div>
        </fieldset>

        <fieldset class="grupo-producto">
            <legend>Vencimiento</legend>

            <div class="campo-producto">
                <label for="tiene_vencimiento">¿Tiene vencimiento? (opcional)</label>
                <select id="tiene_vencimiento" name="tiene_vencimiento">
                    <option value="no">No</option>
                    <option value="si">Sí</option>
                </select>
            </div>

            <div id="fecha-vencimiento-contenedor" class="campo-producto oculto">
                <label for="fecha_vencimiento">Fecha de vencimiento (opcional)</label>
                <input type="date" id="fecha_vencimiento" name="fecha_vencimiento" disabled>
            </div>
        </fieldset>

        <button type="button" class="boton-producto">Guardar producto</button>
        <a href="/productos" class="boton-producto">Cancelar</a>
    </form>

    <script>
        const selectorVencimiento = document.getElementById('tiene_vencimiento');
        const fechaVencimientoContenedor = document.getElementById('fecha-vencimiento-contenedor');
        const fechaVencimiento = document.getElementById('fecha_vencimiento');

        selectorVencimiento.addEventListener('change', function () {
            const tieneVencimiento = selectorVencimiento.value === 'si';

            fechaVencimientoContenedor.classList.toggle('oculto', !tieneVencimiento);
            fechaVencimiento.disabled = !tieneVencimiento;
        });
    </script>

@endsection
