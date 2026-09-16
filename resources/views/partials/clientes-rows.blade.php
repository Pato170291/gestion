@forelse ($clientes as $cliente)
    <tr>
        <td>{{ $cliente->nombre }}</td>
        <td>{{ $cliente->apellido }}</td>
        <td>{{ $cliente->telefono }}</td>
        <td>{{ $cliente->email }}</td>
        <td>{{ $cliente->condicion_iva }}</td>
        
        <td>
            @php
                $saldo = (float) ($cliente->saldo ?? 0);
            @endphp

            @if ($saldo < 0)
                <span style="color: #198754; font-weight: 600;">
                    Saldo a favor: ${{ number_format(abs($saldo), 2, ',', '.') }}
                </span>
            @elseif ($saldo > 0)
                <span style="color: #dc3545; font-weight: 600;">
                Saldo a pagar: ${{ number_format($saldo, 2, ',', '.') }}
                </span>
            @else
                <span>
                    Saldo: $0,00
                </span>
            @endif
        </td>
        <td>{{ $cliente->activo ? 'Activo' : 'Inactivo' }}</td>

        <td>
            <a href="/clientes/{{ $cliente->id }}/editar" aria-label="Editar cliente" title="Editar cliente">
                &#9998;
            </a>

            <form method="POST" action="{{ route('clientes.estado', $cliente->id) }}">
                @csrf
                <button type="submit" aria-label="{{ $cliente->activo ? 'Desactivar' : 'Activar' }} cliente" title="{{ $cliente->activo ? 'Desactivar' : 'Activar' }} cliente">
                    {{ $cliente->activo ? 'Desactivar' : 'Activar' }}
                </button>
            </form>

            <button
                type="button"
                class="boton-ver-cliente"
                data-id="{{ $cliente->id }}"
                data-cuenta-url="{{ route('clientes.cuenta-corriente', $cliente->id) }}"
                data-nombre="{{ $cliente->nombre }} {{ $cliente->apellido }}"
                aria-label="Ver detalle de {{ $cliente->nombre }} {{ $cliente->apellido }}"
                title="Ver detalle"
            >
                &#128065;
            </button>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8">
            No se encontraron clientes.
        </td>
    </tr>    
@endforelse