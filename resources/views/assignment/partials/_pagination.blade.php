<div class="pager">

    @if ($paginator->currentPage() > 1)
        <div class="previous">
            <a href="{!! $paginator->previousPageUrl() !!}">Previous Section</a>
        </div>
    @endif

    @if ($paginator->hasMorePages())
        <div class="next">
            <a href="{!! $paginator->nextPageUrl() !!}">Next Section</a>
        </div>
    @endif

</div>