<table>
    <tbody id="filas-compras-ajax">
        @include('partials.compras-rows')
    </tbody>
</table>

@include('partials.paginacion-compras', ['paginador' => $compras])

<script type="application/json" id="datos-compras-ajax">@json($compras->items())</script>