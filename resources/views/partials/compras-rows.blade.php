@forelse ($compras as $compra)
    <tr>
        <td>{{ $compra->id }}</td>
        <td>{{ $compra->fecha->format('d/m/Y') }}</td>
        <td>{{ $compra->proveedor?->empresa ?? 'Sin proveedor' }}</td>
        <td>${{ number_format($compra->total, 2, ',', '.') }}</td>
        <td>{{ $compra->estado }}</td>
        <td class="acciones-compra">
            <a href="/compras/{{ $compra->id }}/editar" aria-label="Editar compra {{ $compra->id }}" title="Editar compra">&#9998;</a>
            <form method="POST" action="/compras/{{ $compra->id }}">
                @csrf
                @method('DELETE')
                <button type="submit" aria-label="Eliminar compra {{ $compra->id }}" title="Eliminar compra" onclick="return confirm('¿Seguro que querés eliminar esta compra?')">&#128465;</button>
            </form>
            <button type="button" class="boton-ver-compra" data-compra="{{ $compra->id }}" aria-label="Ver detalle de compra" title="Ver detalle de compra">&#128065;</button>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6">No hay compras registradas.</td>
    </tr>
@endforelse