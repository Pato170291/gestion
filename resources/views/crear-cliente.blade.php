@extends('layouts.app')

@section('title', 'Crear cliente')

@section('content')

    <style>
        .formulario-cliente {
            max-width: 760px;
        }

        .grupo-cliente {
            margin: 0 0 24px;
            padding: 20px;
            border: 1px solid #dddddd;
            background: #fafafa;
        }

        .campo-cliente {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 16px;
        }

        .campo-cliente:last-child {
            margin-bottom: 0;
        }

        .campo-cliente input {
            max-width: 500px;
            padding: 9px 10px;
            border: 1px solid #cccccc;
            border-radius: 4px;
            font: inherit;
        }

        .campo-cliente select {
            max-width: 500px;
            padding: 9px 10px;
            border: 1px solid #cccccc;
            border-radius: 4px;
            font: inherit;
        }

        .boton-cliente {
            padding: 9px 14px;
            background: #eeeeee;
            border: 1px solid #cccccc;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            color: black;
        }

        .boton-cliente:hover {
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

    <h1>Nuevo cliente</h1>

    <form class="formulario-cliente" method="POST" action="/clientes">

        @csrf

        @if ($errors->any())
            <div class="errores-formulario">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <fieldset class="grupo-cliente">
            <legend>Información del cliente</legend>

            <div class="campo-cliente">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" required>
            </div>

            <div class="campo-cliente">
                <label for="apellido">Apellido</label>
                <input type="text" id="apellido" name="apellido" value="{{ old('apellido') }}" required>
            </div>

            <div class="campo-cliente">
                <label for="telefono">Teléfono</label>
                <input type="text" id="telefono" name="telefono" value="{{ old('telefono') }}" required>
            </div>

            <div class="campo-cliente">
                <label for="email">Email (opcional)</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}">
            </div>

            <div class="campo-cliente">
                <label for="cuit">CUIT (opcional)</label>
                <input type="text" id="cuit" name="cuit" value="{{ old('cuit') }}">
            </div>

            <div class="campo-cliente">
                <label for="condicion_iva">Condición frente al IVA</label>
                <select id="condicion_iva" name="condicion_iva">
                    @foreach (\App\Models\Cliente::CONDICIONES_IVA as $condicion)
                        <option value="{{ $condicion }}" {{ old('condicion_iva', 'Consumidor Final') === $condicion ? 'selected' : '' }}>{{ $condicion }}</option>
                    @endforeach
                </select>
            </div>
        </fieldset>

        <button type="submit" class="boton-cliente">Guardar cliente</button>
        <a href="/clientes" class="boton-cliente">Cancelar</a>

    </form>

@endsection