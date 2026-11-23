{{--
    Domain availability search. $theme: 'dark' (on a dark band) or 'light'.
    Without JavaScript the form falls back to the contact page with the domain filled in.
--}}
@php $extensions = \App\Support\DomainChecker::extensions(); @endphp
<div class="domain-search domain-search-{{ $theme ?? 'light' }}" data-domain-search data-endpoint="{{ route('domain.check') }}">
    <form method="GET" action="{{ url('/contact') }}" class="domain-search-form" novalidate>
        <input type="hidden" name="service" value="domain-registration" />
        <div class="domain-search-field">
            <svg class="domain-search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="12" cy="12" r="10" /><line x1="2" y1="12" x2="22" y2="12" />
                <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10Z" />
            </svg>
            <input type="text" name="domain" placeholder="Find your domain, e.g. mybusiness" aria-label="Domain name" autocomplete="off" autocapitalize="off" spellcheck="false" maxlength="80" data-domain-input />
            <select name="ext" aria-label="Extension" data-domain-ext>
                @foreach ($extensions as $ext => $price)
                    <option value="{{ $ext }}">{{ $ext }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="domain-search-btn" data-domain-btn>Search</button>
    </form>
    <div class="domain-search-tlds" aria-hidden="true">
        @foreach (array_slice($extensions, 0, 6, true) as $ext => $price)
            <span><strong>{{ $ext }}</strong> {{ $price['INR'] }}</span>
        @endforeach
    </div>
    <div class="domain-search-results" data-domain-results aria-live="polite" hidden></div>
</div>
