@if ($page->lastPage() > 1 || !empty($always))
    <div class="admin-pagination">
        <button type="button" data-href="{{ $page->previousPageUrl() }}" @disabled(!$page->previousPageUrl())>← Previous</button>
        <span>Page {{ $page->currentPage() }} of {{ $page->lastPage() }}</span>
        <button type="button" data-href="{{ $page->nextPageUrl() }}" @disabled(!$page->nextPageUrl())>Next →</button>
    </div>
@endif
