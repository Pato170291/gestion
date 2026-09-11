@forelse ($compras as $compra)
    <tr>
        <td>{{ $compra->id }}</td>
        <td>{{ $compra->fecha->format('d/m/Y') }}</td>
        <td>{{ $compra->proveedor?->empresa ?? 'Sin proveedor' }}</td>
        <td>${{ number_format($compra->total, 2, ',', '.') }}</td>
        <td class="{{ $compra->estado === 'Anulada' ? 'estado-compra-anulada' : '' }}">{{ $compra->estado }}</td>
        <td class="acciones-compra">
            @if ($compra->estado !== 'Anulada')
                <a href="/compras/{{ $compra->id }}/editar" aria-label="Editar compra {{ $compra->id }}" title="Editar compra">&#9998;</a>
                <form method="POST" action="{{ route('compras.anular', $compra->id) }}" onsubmit="return confirmarAnulacionCompra(this)">
                    @csrf
                    <input type="hidden" name="motivo_anulacion">
                    <button type="submit" aria-label="Anular compra {{ $compra->id }}" title="Anular compra">Anular</button>
                </form>
            @endif
            <button type="button" class="boton-ver-compra" data-compra="{{ $compra->id }}" aria-label="Ver detalle de compra" title="Ver detalle de compra">&#128065;</button>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6">No hay compras registradas.</td>
    </tr>
@endforelse