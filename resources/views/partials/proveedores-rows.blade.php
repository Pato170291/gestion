@forelse ($proveedores as $proveedor)

    <tr>
        <td>{{ $proveedor->empresa }}</td>
        <td>{{ $proveedor->contacto }}</td>
        <td>{{ $proveedor->telefono }}</td>
        <td>{{ $proveedor->email }}</td>
        <td>{{ $proveedor->direccion }}</td>
        <td>{{ $proveedor->cuit }}</td>
        <td>{{ $proveedor->condicion_iva }}</td>
        <td>${{ number_format($proveedor->saldo ?? 0, 2, ',', '.') }}</td>

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

            <form method="POST" action="{{ route('proveedores.estado', $proveedor->id) }}">
                @csrf
                <button type="submit" aria-label="{{ $proveedor->activo ? 'Desactivar' : 'Activar' }} proveedor" title="{{ $proveedor->activo ? 'Desactivar' : 'Activar' }} proveedor">
                    {{ $proveedor->activo ? 'Desactivar' : 'Activar' }}
                </button>
            </form>

            <button
                type="button"
                class="boton-ver-proveedor"
                data-id="{{ $proveedor->id }}"
                data-cuenta-url="{{ route('proveedores.cuenta-corriente', $proveedor->id) }}"
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
        <td colspan="10">
            No hay proveedores registrados.
        </td>
    </tr>

@endforelse