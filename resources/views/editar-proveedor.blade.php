@extends('layouts.app')

@section('title', 'Editar proveedor')

@section('content')

    <h1>Editar proveedor</h1>

    <form method="POST" action="/proveedores/{{ $proveedor->id }}">

        @csrf
        @method('PUT')

        <label>Empresa:</label>
        <input type="text" name="empresa" value="{{ $proveedor->empresa }}">

        <br><br>

        <label>Contacto:</label>
        <input type="text" name="contacto" value="{{ $proveedor->contacto }}">

        <br><br>

        <label>Teléfono:</label>
        <input type="text" name="telefono" value="{{ $proveedor->telefono }}">

        <br><br>

        <label>Email:</label>
        <input type="email" name="email" value="{{ $proveedor->email }}">

        <br><br>

        <label>Dirección:</label>
        <input type="text" name="direccion" value="{{ $proveedor->direccion }}">

        <br><br>

        <label>CUIT:</label>
        <input type="text" name="cuit" value="{{ $proveedor->cuit }}">

        <br><br>

        <label>Estado:</label>
        <select name="activo">

            <option value="1" {{ $proveedor->activo == 1 ? 'selected' : '' }}>
                Activo
            </option>

            <option value="0" {{ $proveedor->activo == 0 ? 'selected' : '' }}>
                Inactivo
            </option>

        </select>

        <br><br>

        <button type="submit">Guardar cambios</button>

    </form>

@endsection