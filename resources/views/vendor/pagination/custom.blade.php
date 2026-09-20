@if ($paginator->hasPages())
<div class="pagination">
    <div class="pagination-info">Menampilkan {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} dari {{ $paginator->total() }} data</div>
    <div class="pagination-links">
        @if ($paginator->onFirstPage())
            <span class="pagination-btn disabled" aria-disabled="true">Sebelumnya</span>
        @else
            <a class="pagination-btn" href="{{ $paginator->previousPageUrl() }}" rel="prev">Sebelumnya</a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="pagination-gap">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="pagination-btn active" aria-current="page">{{ $page }}</span>
                    @else
                        <a class="pagination-btn" href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a class="pagination-btn" href="{{ $paginator->nextPageUrl() }}" rel="next">Berikutnya</a>
        @else
            <span class="pagination-btn disabled" aria-disabled="true">Berikutnya</span>
        @endif
    </div>
</div>
@endif