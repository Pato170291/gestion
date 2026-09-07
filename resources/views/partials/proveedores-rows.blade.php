@forelse ($proveedores as $proveedor)

    <tr>
        <td>{{ $proveedor->empresa }}</td>
        <td>{{ $proveedor->contacto }}</td>
        <td>{{ $proveedor->telefono }}</td>
        <td>{{ $proveedor->email }}</td>
        <td>{{ $proveedor->direccion }}</td>
        <td>{{ $proveedor->cuit }}</td>

        <td>
            @if ($proveedor->activo)
                Activo
            @else
                Inactivo
            @endif
        </td>

        <td>
            <a href="/proveedores/{{ $proveedor->id }}/editar">
                Editar
            </a>

            <form method="POST" action="/proveedores/{{ $proveedor->id }}">
                @csrf
                @method('DELETE')

                <button type="submit" onclick="return confirm('¿Seguro que querés eliminar este proveedor?')">
                    Eliminar
                </button>
            </form>
        </td>
    </tr>

@empty

    <tr>
        <td colspan="8">
            No hay proveedores registrados.
        </td>
    </tr>

@endforelse