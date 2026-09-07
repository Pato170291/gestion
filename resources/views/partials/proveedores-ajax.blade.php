<table>
    <tbody id="filas-proveedores">

        @include('partials.proveedores-rows')

    </tbody>
</table>

@include('partials.paginacion-generica', ['paginador' => $proveedores])