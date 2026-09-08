<div id="botones-paginacion-productos">
    <div>
        Mostrando {{ $paginador->firstItem() }}–{{ $paginador->lastItem() }}
        de {{ $paginador->total() }} registros
    </div>

    @if ($paginador->hasPages())
        <nav>
            @if ($paginador->onFirstPage())
                <span>&lt;&lt;</span>
            @else
                <a href="{{ $paginador->previousPageUrl() }}">&lt;&lt;</a>
            @endif

            @php
                $paginaActual = $paginador->currentPage();
                $ultimaPagina = $paginador->lastPage();
            @endphp

            @if ($paginaActual > 3)
                <a href="{{ $paginador->url(1) }}">1</a>
                <span>...</span>
            @endif

            @for ($pagina = max(1, $paginaActual - 2); $pagina <= min($ultimaPagina, $paginaActual + 2); $pagina++)
                @if ($pagina == $paginaActual)
                    <span>{{ $pagina }}</span>
                @else
                    <a href="{{ $paginador->url($pagina) }}">{{ $pagina }}</a>
                @endif
            @endfor

            @if ($paginaActual < $ultimaPagina - 2)
                <span>...</span>
                <a href="{{ $paginador->url($ultimaPagina) }}">{{ $ultimaPagina }}</a>
            @endif

            @if ($paginador->hasMorePages())
                <a href="{{ $paginador->nextPageUrl() }}">&gt;&gt;</a>
            @else
                <span>&gt;&gt;</span>
            @endif
        </nav>
    @endif
</div>
