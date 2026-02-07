@if ($paginator->hasPages())
    <nav>
        <ul class="pagination">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="disabled">
                    <span>&lsaquo;</span>
                </li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev">&lsaquo;</a>
                </li>
            @endif

            @php
                $currentPage = $paginator->currentPage();
                $lastPage = $paginator->lastPage();
                $start = 1;
                $end = $lastPage;
                
                // Show limited pages with ellipsis
                if ($lastPage > 7) {
                    if ($currentPage <= 4) {
                        // Near the start
                        $end = 5;
                        $showEndEllipsis = true;
                        $showStartEllipsis = false;
                    } elseif ($currentPage >= $lastPage - 3) {
                        // Near the end
                        $start = $lastPage - 4;
                        $showEndEllipsis = false;
                        $showStartEllipsis = true;
                    } else {
                        // In the middle
                        $start = $currentPage - 2;
                        $end = $currentPage + 2;
                        $showStartEllipsis = true;
                        $showEndEllipsis = true;
                    }
                } else {
                    $showStartEllipsis = false;
                    $showEndEllipsis = false;
                }
            @endphp

            {{-- First Page --}}
            @if ($showStartEllipsis)
                <li class="{{ 1 == $currentPage ? 'active' : '' }}">
                    @if (1 == $currentPage)
                        <span>1</span>
                    @else
                        <a href="{{ $paginator->url(1) }}">1</a>
                    @endif
                </li>
                <li><span class="pagination-ellipsis">...</span></li>
            @endif

            {{-- Page Numbers --}}
            @for ($page = $start; $page <= $end; $page++)
                @if (!$showStartEllipsis || $page > 1)
                    @if (!$showEndEllipsis || $page < $lastPage)
                        <li class="{{ $page == $currentPage ? 'active' : '' }}">
                            @if ($page == $currentPage)
                                <span>{{ $page }}</span>
                            @else
                                <a href="{{ $paginator->url($page) }}">{{ $page }}</a>
                            @endif
                        </li>
                    @endif
                @endif
            @endfor

            {{-- Last Page --}}
            @if ($showEndEllipsis)
                <li><span class="pagination-ellipsis">...</span></li>
                <li class="{{ $lastPage == $currentPage ? 'active' : '' }}">
                    @if ($lastPage == $currentPage)
                        <span>{{ $lastPage }}</span>
                    @else
                        <a href="{{ $paginator->url($lastPage) }}">{{ $lastPage }}</a>
                    @endif
                </li>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next">&rsaquo;</a>
                </li>
            @else
                <li class="disabled">
                    <span>&rsaquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif

