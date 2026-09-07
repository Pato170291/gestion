<div>
    Mostrando {{ $clientes->firstItem() }}–{{ $clientes->lastItem() }}
    de {{ $clientes->total() }} clientes
</div>

@if ($clientes->hasPages())

    <nav>
        @if ($clientes->onFirstPage())
            <span>&lt;&lt;</span>
        @else
            <a href="{{ $clientes->previousPageUrl() }}">&lt;&lt;</a>
        @endif

        @php
            $paginaActual = $clientes->currentPage();
            $ultimaPagina = $clientes->lastPage();
        @endphp

        @if ($paginaActual > 3)
            <a href="{{ $clientes->url(1) }}">1</a>
            <span>...</span>
        @endif

        @for ($pagina = max(1, $paginaActual - 2); $pagina <= min($ultimaPagina, $paginaActual + 2); $pagina++)

            @if ($pagina == $paginaActual)
                <span>{{ $pagina }}</span>
            @else
                <a href="{{ $clientes->url($pagina) }}">{{ $pagina }}</a>
            @endif

        @endfor

        @if ($paginaActual < $ultimaPagina - 2)
            <span>...</span>
            <a href="{{ $clientes->url($ultimaPagina) }}">{{ $ultimaPagina }}</a>
        @endif

        @if ($clientes->hasMorePages())
            <a href="{{ $clientes->nextPageUrl() }}">&gt;&gt;</a>
        @else
            <span>&gt;&gt;</span>
        @endif
    </nav>
    
@endif