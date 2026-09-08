@forelse ($clientes as $cliente)
    <tr>
        <td>{{ $cliente->nombre }}</td>
        <td>{{ $cliente->apellido }}</td>
        <td>{{ $cliente->telefono }}</td>
        <td>{{ $cliente->email }}</td>
        <td>$0</td>

        <td>
            <a href="/clientes/{{ $cliente->id }}/editar" aria-label="Editar cliente" title="Editar cliente">
                &#9998;
            </a>

            <form method="POST" action="/clientes/{{ $cliente->id }}">
                @csrf
                @method('DELETE')

                <button type="submit" aria-label="Eliminar cliente" title="Eliminar cliente" onclick="return confirm('¿Seguro que querés eliminar este cliente?')">
                    &#128465;
                </button>
            </form>

            <button
                type="button"
                class="boton-ver-cliente"
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
        <td colspan="6">
            No se encontraron clientes.
        </td>
    </tr>    
@endforelse