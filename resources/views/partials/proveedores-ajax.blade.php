<div id="filas-proveedores">
    @include('partials.proveedores-rows')
</div>

<div id="botones-paginacion-proveedores">
    {{ $proveedores->links() }}
</div>