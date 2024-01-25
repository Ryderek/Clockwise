<nav aria-label="page navigation" style="width: max-content; margin-left: auto; margin-right: auto;">
    <ul class="pagination">
        <li class="page-item @if($currentPage == 1) d-none @endif"><a class="page-link" href="{{ str_replace('.', '/', ("/".$currentRoute."/")) }}{{ ($currentPage-1) }}">Previous</a></li>
        @for($page = 1; $page <= $totalPages; $page++)
            <li class="page-item @if($page == $currentPage) active @endif"><a class="page-link" href="{{ str_replace('.', '/', ("/".$currentRoute."/")) }}{{ $page }}">{{ $page }}</a></li>
        @endfor
        <li class="page-item @if($currentPage == $totalPages) d-none @endif"><a class="page-link" href="{{ str_replace('.', '/', ("/".$currentRoute."/")) }}{{ ($currentPage+1) }}">Next</a></li>
    </ul>
</nav>