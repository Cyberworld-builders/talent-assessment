{{-- Pagination --}}
<ul class="pagination">

    {{-- Previous --}}
    @if ($paginator->currentPage() > 1)
        <li><a href="?page={{ $paginator->currentPage() - 1 }}"><i class="fa-angle-left"></i></a></li>
    @else
        <li class="disabled"><a href="#"><i class="fa-angle-left"></i></a></li>
    @endif

    {{-- Page Numbers --}}
    @for ($i = 1; $i <= $paginator->lastPage(); $i++)
        <li {{ ($paginator->currentPage() == $i ? 'class=active' : '') }}>
            <a {{ ($paginator->currentPage() == $i ? '' : 'href=?page=' . $i) }}>{{ $i }}</a>
        </li>
    @endfor

    {{-- Next --}}
    @if ($paginator->currentPage() < $paginator->lastPage())
        <li><a href="?page={{ $paginator->currentPage() + 1 }}"><i class="fa-angle-right"></i></a></li>
    @else
        <li class="disabled"><a href="#"><i class="fa-angle-right"></i></a></li>
    @endif
</ul>