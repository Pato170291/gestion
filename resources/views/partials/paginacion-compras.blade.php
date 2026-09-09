<div id="paginacion-compras">
    <div>Mostrando {{ $paginador->firstItem() ?? 0 }}–{{ $paginador->lastItem() ?? 0 }} de {{ $paginador->total() }} registros</div>

    @if ($paginador->hasPages())
        <nav>
            @if ($paginador->onFirstPage())
                <span>&lt;&lt;</span>
            @else
                <a href="{{ $paginador->previousPageUrl() }}">&lt;&lt;</a>
            @endif

            @for ($pagina = 1; $pagina <= $paginador->lastPage(); $pagina++)
                @if ($pagina === $paginador->currentPage())
                    <span>{{ $pagina }}</span>
                @else
                    <a href="{{ $paginador->url($pagina) }}">{{ $pagina }}</a>
                @endif
            @endfor

            @if ($paginador->hasMorePages())
                <a href="{{ $paginador->nextPageUrl() }}">&gt;&gt;</a>
            @else
                <span>&gt;&gt;</span>
            @endif
        </nav>
    @endif
</div>