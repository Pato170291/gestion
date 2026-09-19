@forelse ($cierres as $cierre)
    <tr>
        <td>
            <button
                type="button"
                class="caja-accion-ojo"
                data-cierre="{{ $cierre->id }}"
                aria-label="Ver cierre"
                title="Ver detalle"
            >
                &#128065;
            </button>
        </td>

        <td>{{ $cierre->fecha->format('d/m/Y') }}</td>

        <td>
            ${{ number_format($cierre->saldo_inicial, 2, ',', '.') }}
        </td>

        <td class="caja-monto ingreso">
            +${{ number_format($cierre->total_ingresos, 2, ',', '.') }}
        </td>

        <td class="caja-monto egreso">
            -${{ number_format($cierre->total_egresos, 2, ',', '.') }}
        </td>

        <td>
            ${{ number_format($cierre->saldo_esperado, 2, ',', '.') }}
        </td>

        <td class="{{ 
            $cierre->diferencia < 0
                ? 'caja-diferencia-negativa'
                : ($cierre->diferencia > 0
                    ? 'caja-diferencia-positiva'
                    : '')
        }}">
            ${{ number_format($cierre->diferencia, 2, ',', '.') }}
        </td>

        <td>
            {{ ucfirst($cierre->estado) }}
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="caja-vacia">
            No hay historial de cierres.
        </td>
    </tr>
@endforelse