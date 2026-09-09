@forelse ($productos as $producto)
    <tr class="fila-producto">
        <td>{{ $producto->id }}</td>
        <td>{{ $producto->nombre }}</td>
        <td>${{ number_format($producto->precio_venta, 0, ',', '.') }}</td>
        <td>{{ $producto->stock_actual }}</td>
        <td>
            <a
                href="/productos/{{ $producto->id }}/editar"
                aria-label="Editar producto {{ $producto->nombre }}"
                title="Editar producto"
            >&#9998;</a>
            <form method="POST" action="/productos/{{ $producto->id }}">
                @csrf
                @method('DELETE')

                <button
                    type="submit"
                    aria-label="Eliminar producto"
                    title="Eliminar producto"
                    onclick="return confirm('¿Seguro que querés eliminar este producto?')"
                >&#128465;</button>
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
        <td colspan="5">No hay productos registrados.</td>
    </tr>
@endforelse
