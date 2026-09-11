@forelse ($productos as $producto)
    <tr class="fila-producto">
        <td>{{ $producto->id }}</td>
        <td>{{ $producto->nombre }}</td>
        <td>${{ number_format($producto->precio_venta, 0, ',', '.') }}</td>
        <td>{{ $producto->stock_calculado }}</td>
        <td>{{ $producto->activo ? 'Activo' : 'Inactivo' }}</td>
        <td>
            <a
                href="/productos/{{ $producto->id }}/editar"
                aria-label="Editar producto {{ $producto->nombre }}"
                title="Editar producto"
            >&#9998;</a>
            <form method="POST" action="{{ route('productos.estado', $producto->id) }}">
                @csrf
                <button type="submit" aria-label="{{ $producto->activo ? 'Desactivar' : 'Activar' }} producto" title="{{ $producto->activo ? 'Desactivar' : 'Activar' }} producto">
                    {{ $producto->activo ? 'Desactivar' : 'Activar' }}
            </form>
            <button
                type="button"
                class="boton-ver-producto"
                data-producto="{{ $producto->id }}"
                aria-label="Ver producto {{ $producto->nombre }}"
                title="Ver producto"
            >&#128065;</button>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6">No hay productos registrados.</td>
    </tr>
@endforelse
