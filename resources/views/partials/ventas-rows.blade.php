@forelse ($ventas as $venta)
    <tr>
        <td>{{ $venta->id }}</td>
        <td>{{ $venta->fecha->format('d/m/Y') }}</td>
        <td>{{ $venta->cliente ? $venta->cliente->nombre . ' ' . $venta->cliente->apellido : 'No es cliente' }}</td>
        <td>${{ number_format($venta->total, 2, ',', '.') }}</td>
        <td>{{ $venta->estado }}</td>
        <td class="acciones-venta">
            <a
                href="/ventas/{{ $venta->id }}/editar"
                aria-label="Editar venta {{ $venta->id }}"
                title="Editar venta"
            >&#9998;</a>
            <form method="POST" action="/ventas/{{ $venta->id }}">
                @csrf
                @method('DELETE')

                <button
                    type="submit"
                    aria-label="Eliminar venta {{ $venta->id }}"
                    title="Eliminar venta"
                    onclick="return confirm('¿Seguro que querés eliminar esta venta?')"
                >&#128465;</button>
            </form>
            <button type="button" class="boton-ver-venta" data-venta="{{ $venta->id }}" aria-label="Ver detalle de venta" title="Ver detalle de venta">&#128065;</button>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6">No hay ventas registradas.</td>
    </tr>
@endforelse