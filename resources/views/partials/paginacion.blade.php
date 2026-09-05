@if ($clientes->hasPages())

    <nav>
        @if ($clientes->onFirstPage())
            <span>&lt;&lt;</span>
        @else
            <a href="{{ $clientes->previousPageUrl() }}">&lt;&lt;</a>
        @endif

        @foreach ($clientes->getUrlRange(1, $clientes->lastPage()) as $pagina => $url)

            @if ($pagina == $clientes->currentPage())
                <span>{{ $pagina }}</span>
            @else
                <a href="{{ $url }}">{{ $pagina }}</a>
            @endif

        @endforeach

        @if ($clientes->hasMorePages())
            <a href="{{ $clientes->nextPageUrl() }}">&gt;&gt;</a>
        @else
            <span>&gt;&gt;</span>
        @endif
    </nav>

@endif