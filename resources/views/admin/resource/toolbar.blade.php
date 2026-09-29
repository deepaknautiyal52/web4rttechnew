{{--
    Search box + filter dropdowns. Submits as a GET form, so the list is
    bookmarkable; typing re-queries after a short pause and dropdowns apply
    immediately (see admin.js).
    $filters: [['name' => 'status', 'label' => 'Status', 'options' => [value => label], 'all' => 'Status: All']]
    $keep: extra query params to carry over, e.g. ['days' => 30]
--}}
<form method="GET" action="{{ url()->current() }}" class="resource-toolbar" data-filter-form>
    @foreach (($keep ?? []) as $k => $v)
        <input type="hidden" name="{{ $k }}" value="{{ $v }}" />
    @endforeach
    <div class="admin-search-bar resource-search">
        <svg class="admin-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="7" />
            <path d="m21 21-4.3-4.3" />
        </svg>
        <input type="text" name="search" placeholder="{{ $searchPlaceholder ?? 'Search...' }}" value="{{ request('search') }}" data-search-input @if(request()->filled('search')) autofocus @endif />
        @if (request()->filled('search'))
            <button type="button" class="admin-search-clear" data-search-clear aria-label="Clear search">×</button>
        @endif
    </div>
    @foreach (($filters ?? []) as $f)
        <select name="{{ $f['name'] }}" class="resource-filter" aria-label="{{ $f['label'] }}" data-autosubmit>
            @if (!array_key_exists('all', $f) || $f['all'] !== false)
                <option value="">{{ $f['all'] ?? $f['label'].': All' }}</option>
            @endif
            @foreach ($f['options'] as $value => $label)
                <option value="{{ $value }}" @selected((string) request($f['name']) === (string) $value)>{{ $label }}</option>
            @endforeach
        </select>
    @endforeach
</form>
