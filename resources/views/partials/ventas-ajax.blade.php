<table>
    <tbody id="filas-ventas-ajax">
        @include('partials.ventas-rows')
    </tbody>
</table>

@include('partials.paginacion-ventas', ['paginador' => $ventas])

<script type="application/json" id="datos-ventas-ajax">
    @json($ventas->items())
</script>