@extends('layouts.app')

@section('title', 'Crear proveedor')

@section('content')

    <h1>Nuevo proveedor</h1>

    @if ($errors->any())

        <div id="mensaje-error">

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>

        <style>
            #mensaje-error {
                position: fixed;
                top: 20px;
                right: 20px;
                background: #dc3545;
                color: white;
                padding: 15px 20px;
                border-radius: 8px;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
                z-index: 1000;
            }
        </style>

        <script>
            setTimeout(function () {
                document.getElementById('mensaje-error').style.display = 'none';
            }, 5000);
        </script>

    @endif

    <form method="POST" action="/proveedores">

        @csrf

        <label>Empresa:</label>
        <input type="text" name="empresa" value="{{ old('empresa') }}">

        <br><br>

        <label>Contacto:</label>
        <input type="text" name="contacto" value="{{ old('contacto') }}">

        <br><br>

        <label>Teléfono:</label>
        <input type="text" name="telefono" value="{{ old('telefono') }}">

        <br><br>

        <label>Email:</label>
        <input type="email" name="email" value="{{ old('email') }}">

        <br><br>

        <label>Dirección:</label>
        <input type="text" name="direccion" value="{{ old('direccion') }}">

        <br><br>

        <label>CUIT:</label>
        <input type="text" name="cuit" value="{{ old('cuit') }}">

        <br><br>

        <label>Estado:</label>
        <select name="activo">
            <option value="1" {{ old('activo', '1') == '1' ? 'selected' : '' }}>Activo</option>
            <option value="0" {{ old('activo') == '0' ? 'selected' : '' }}>Inactivo</option>
        </select>

        <br><br>

        <button type="submit" id="boton-guardar">Guardar proveedor</button>

    </form>
    <script>
        document.querySelector('form').addEventListener('submit', function () {
            document.getElementById('boton-guardar').disabled = true;
            document.getElementById('boton-guardar').innerText = 'Guardando...';
        });
    </script>
    
@endsection