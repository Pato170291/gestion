@extends('layouts.app')

@section('title', 'Crear proveedor')

@section('content')

    <style>
        .formulario-proveedor {
            max-width: 760px;
        }

        .grupo-proveedor {
            margin: 0 0 24px;
            padding: 20px;
            border: 1px solid #dddddd;
            background: #fafafa;
        }

        .campo-proveedor {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 16px;
        }

        .campo-proveedor:last-child {
            margin-bottom: 0;
        }

        .campo-proveedor input,
        .campo-proveedor select {
            max-width: 500px;
            padding: 9px 10px;
            border: 1px solid #cccccc;
            border-radius: 4px;
            font: inherit;
        }

        .boton-proveedor {
            padding: 9px 14px;
            background: #eeeeee;
            border: 1px solid #cccccc;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            color: black;
        }

        .boton-proveedor:hover {
            background: #d7ebff;
            box-shadow: 0 4px 10px rgba(0, 91, 170, 0.2);
        }

        .errores-formulario {
            margin-bottom: 24px;
            padding: 14px 18px;
            border: 1px solid #dc3545;
            background: #f8d7da;
            color: #842029;
        }

        .errores-formulario p {
            margin: 0 0 6px;
        }

        .errores-formulario p:last-child {
            margin-bottom: 0;
        }
    </style>

    <h1>Nuevo proveedor</h1>

    <form class="formulario-proveedor" method="POST" action="/proveedores">

        @csrf

        @if ($errors->any())
            <div class="errores-formulario">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <fieldset class="grupo-proveedor">
            <legend>Información del proveedor</legend>

            <div class="campo-proveedor">
                <label for="empresa">Empresa</label>
                <input type="text" id="empresa" name="empresa" value="{{ old('empresa') }}" required>
            </div>

            <div class="campo-proveedor">
                <label for="contacto">Contacto (opcional)</label>
                <input type="text" id="contacto" name="contacto" value="{{ old('contacto') }}">
            </div>

            <div class="campo-proveedor">
                <label for="telefono">Teléfono</label>
                <input type="text" id="telefono" name="telefono" value="{{ old('telefono') }}" required>
            </div>

            <div class="campo-proveedor">
                <label for="email">Email (opcional)</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}">
            </div>

            <div class="campo-proveedor">
                <label for="direccion">Dirección (opcional)</label>
                <input type="text" id="direccion" name="direccion" value="{{ old('direccion') }}">
            </div>

            <div class="campo-proveedor">
                <label for="cuit">CUIT (opcional)</label>
                <input type="text" id="cuit" name="cuit" value="{{ old('cuit') }}">
            </div>

            <div class="campo-proveedor">
                <label for="activo">Estado</label>
                <select id="activo" name="activo">
                    <option value="1" {{ old('activo', '1') == '1' ? 'selected' : '' }}>Activo</option>
                    <option value="0" {{ old('activo') == '0' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>
        </fieldset>

        <button type="submit" class="boton-proveedor">Guardar proveedor</button>
        <a href="/proveedores" class="boton-proveedor">Cancelar</a>

    </form>

@endsection