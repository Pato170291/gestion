@forelse ($proveedores as $proveedor)

    <tr>
        <td>{{ $proveedor->empresa }}</td>
        <td>{{ $proveedor->contacto }}</td>
        <td>{{ $proveedor->telefono }}</td>
        <td>{{ $proveedor->email }}</td>
        <td>{{ $proveedor->direccion }}</td>
        <td>{{ $proveedor->cuit }}</td>
        <td>$0</td>

        <td>
            @if ($proveedor->activo)
                Activo
            @else
                Inactivo
            @endif
        </td>

        <td>
            <a href="/proveedores/{{ $proveedor->id }}/editar" aria-label="Editar proveedor" title="Editar proveedor">
                &#9998;
            </a>

            <form method="POST" action="/proveedores/{{ $proveedor->id }}">
                @csrf
                @method('DELETE')

                <button type="submit" aria-label="Eliminar proveedor" title="Eliminar proveedor" onclick="return confirm('¿Seguro que querés eliminar este proveedor?')">
                    &#128465;
                </button>
            </form>

            <button
                type="button"
                class="boton-ver-proveedor"
                data-nombre="{{ $proveedor->empresa }}"
                aria-label="Ver detalle de {{ $proveedor->empresa }}"
                title="Ver detalle"
            >
                &#128065;
            </button>
        </td>
    </tr>

@empty

    <tr>
        <td colspan="9">
            No hay proveedores registrados.
        </td>
    </tr>

@endforelse