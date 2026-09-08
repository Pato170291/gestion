<table>
    <tbody id="filas-productos-ajax">
        @include('partials.productos-rows')
    </tbody>
</table>

@include('partials.paginacion-productos', ['paginador' => $productos])

<script type="application/json" id="datos-productos-ajax">
    @json($productos->keyBy('id'))
</script>
