@forelse ($clientes as $cliente)
    <tr>
        <td>{{ $cliente->nombre }}</td>
        <td>{{ $cliente->apellido }}</td>
        <td>{{ $cliente->telefono }}</td>
        <td>{{ $cliente->email }}</td>

        <td>
            <a href="/clientes/{{ $cliente->id }}/editar">
                Editar
            </a>

            <form method="POST" action="/clientes/{{ $cliente->id }}">
                @csrf
                @method('DELETE')

                <button type="submit" onclick="return confirm('¿Seguro que querés eliminar este cliente?')">
                    Eliminar
                </button>
            </form>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5">
            No se encontraron clientes.
        </td>
    </tr>    
@endforelse