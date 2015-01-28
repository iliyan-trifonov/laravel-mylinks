@if($paginator->getLastPage() > 1)
    <nav>
        <ul class="pagination pagination-lg">
            <li @if($paginator->getCurrentPage() == 1) class="disabled" @endif>
                <a
                    href="{{ $paginator->getUrl($paginator->getCurrentPage() - 1) }}"
                    aria-label="Previous"
                >
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            @for ($i = 1; $i <= $paginator->getLastPage(); $i++)
                <li @if($paginator->getCurrentPage() == $i) class="active" @endif>
                    <a href="{{ $paginator->getUrl($i) }}">{{ $i }}</a>
                </li>
            @endfor
            <li @if($paginator->getCurrentPage() == $paginator->getLastPage()) class="disabled" @endif>
                <a href="{{ $paginator->getUrl($paginator->getCurrentPage() + 1) }}" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
@endif
