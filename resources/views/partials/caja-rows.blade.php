@forelse ($movimientos as $movimiento)
    <tr>
        <td>{{ $movimiento->created_at->format('d/m/Y H:i') }}</td>

        <td>{{ $movimiento->concepto }}</td>

        <td>
            <span class="caja-tipo {{ $movimiento->tipo }}">
                {{ ucfirst($movimiento->tipo) }}
            </span>
        </td>

        <td>{{ ucfirst($movimiento->medio) }}</td>

        <td class="caja-monto {{ $movimiento->tipo }}">
            @if ($movimiento->tipo === 'ingreso')
                +${{ number_format($movimiento->monto, 2, ',', '.') }}
            @else
                -${{ number_format($movimiento->monto, 2, ',', '.') }}
            @endif
        </td>

        <td>
            <button
                type="button"
                class="caja-accion-ojo"
                data-movimiento="{{ $movimiento->id }}"
                aria-label="Ver detalle del movimiento"
                title="Ver detalle"
            >
                &#128065;
            </button>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="caja-vacia">
            No hay movimientos que coincidan con los filtros.
        </td>
    </tr>
@endforelse