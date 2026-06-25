@if ($paginator->hasPages())
    <nav class="admin-pagination-nav" role="navigation" aria-label="{{ __('Pagination Navigation') }}">
        <p class="admin-pagination-summary">
            @if ($paginator->firstItem())
                Showing <strong>{{ $paginator->firstItem() }}</strong> to <strong>{{ $paginator->lastItem() }}</strong> of <strong>{{ $paginator->total() }}</strong> results
            @else
                {{ $paginator->count() }} results
            @endif
        </p>

        <div class="admin-pagination-links">
            @if ($paginator->onFirstPage())
                <span class="admin-pagination-btn is-disabled" aria-disabled="true">Previous</span>
            @else
                <a class="admin-pagination-btn" href="{{ $paginator->previousPageUrl() }}" rel="prev">Previous</a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="admin-pagination-ellipsis" aria-disabled="true">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="admin-pagination-btn is-active" aria-current="page">{{ $page }}</span>
                        @else
                            <a class="admin-pagination-btn" href="{{ $url }}" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a class="admin-pagination-btn" href="{{ $paginator->nextPageUrl() }}" rel="next">Next</a>
            @else
                <span class="admin-pagination-btn is-disabled" aria-disabled="true">Next</span>
            @endif
        </div>
    </nav>
@endif
