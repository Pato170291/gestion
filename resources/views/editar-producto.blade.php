@extends('layouts.app')

@section('title', 'Editar producto')

@section('content')

    <h1>Editar producto</h1>

    <form method="POST" action="/productos/{{ $producto->id }}">
        @csrf
        @method('PUT')

        <label>Nombre del producto:</label>
        <input type="text" name="nombre" value="{{ $producto->nombre }}" required>

        <br><br>

        <label>Marca (opcional):</label>
        <input type="text" name="marca" value="{{ $producto->marca }}">

        <br><br>

        <label>Descripción (opcional):</label>
        <textarea name="descripcion">{{ $producto->descripcion }}</textarea>

        <br><br>

        <label>Precio de compra:</label>
        <input type="number" name="precio_compra" value="{{ $producto->precio_compra }}" min="0" step="0.01" required>

        <br><br>

        <label>Precio de venta (opcional):</label>
        <input type="number" name="precio_venta" value="{{ $producto->precio_venta }}" min="0" step="0.01">

        <br><br>

        <label>Stock actual (opcional):</label>
        <input type="number" name="stock_actual" value="{{ $producto->stock_actual }}" min="0">

        <br><br>

        <label>Stock mínimo (opcional):</label>
        <input type="number" name="stock_minimo" value="{{ $producto->stock_minimo }}" min="0">

        <br><br>

        <label>Unidad de medida (opcional):</label>
        <select name="unidad">
            <option value="Unidad" {{ $producto->unidad === 'Unidad' ? 'selected' : '' }}>Unidad</option>
            <option value="kg" {{ $producto->unidad === 'kg' ? 'selected' : '' }}>Kilogramo</option>
            <option value="litro" {{ $producto->unidad === 'litro' ? 'selected' : '' }}>Litro</option>
            <option value="caja" {{ $producto->unidad === 'caja' ? 'selected' : '' }}>Caja</option>
        </select>

        <br><br>

        <label>Proveedor (opcional):</label>
        <input type="text" name="proveedor" value="{{ $producto->proveedor }}">

        <br><br>

        <label>¿Tiene vencimiento? (opcional):</label>
        <select name="tiene_vencimiento">
            <option value="0" {{ !$producto->tiene_vencimiento ? 'selected' : '' }}>No</option>
            <option value="1" {{ $producto->tiene_vencimiento ? 'selected' : '' }}>Sí</option>
        </select>

        <br><br>

        <label>Fecha de vencimiento (opcional):</label>
        <input type="date" name="fecha_vencimiento" value="{{ optional($producto->fecha_vencimiento)->format('Y-m-d') }}">

        <br><br>

        <button type="submit">Guardar cambios</button>
    </form>

@endsection
