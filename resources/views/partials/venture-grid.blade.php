{{-- Cards for the sister businesses. $detailed adds the highlight list (Ventures page). --}}
<div class="venture-grid">
    @foreach (\App\Support\Site::ventures() as $v)
        <article id="{{ $v['id'] }}" class="venture-card" style="--venture-color: {{ $v['color'] }}">
            <div class="venture-icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    @switch($v['id'])
                        @case('homestay')
                            <path d="M2 20 9 8l4 6 3-4 6 10H2Z" />
                            <path d="M13 20v-4l3-2.5 3 2.5v4" />
                            @break
                        @case('real-estate')
                            <path d="M3 21V9l6-4v16" />
                            <path d="M9 21V3h8a2 2 0 0 1 2 2v16" />
                            <path d="M2 21h20" />
                            <path d="M13 7h2M13 11h2M13 15h2M5 12h1M5 16h1" />
                            @break
                        @case('yoga')
                            <path d="M12 20c-4 0-8-2.5-9-7 3 0 6 1.5 9 7Z" />
                            <path d="M12 20c4 0 8-2.5 9-7-3 0-6 1.5-9 7Z" />
                            <path d="M12 20c-2.5-3-2.5-9 0-14 2.5 5 2.5 11 0 14Z" />
                            @break
                        @case('security')
                            <path d="M3 6.5 17 4l1.5 6L4.5 12.5Z" />
                            <path d="m18.5 10 2.5-.5" />
                            <path d="m10 11.3.8 3.7H6v5" />
                            <path d="M3 20h6" />
                            @break
                    @endswitch
                </svg>
            </div>
            <span class="venture-category">{{ $v['category'] }}</span>
            <h3>{{ $v['name'] }}</h3>
            <p class="venture-tagline">{{ $v['tagline'] }}</p>
            <p>{{ $v['description'] }}</p>

            @if (!empty($detailed))
                <ul class="venture-highlights">
                    @foreach ($v['highlights'] as $h)
                        <li>{{ $h }}</li>
                    @endforeach
                </ul>
            @endif

            <div class="venture-actions">
                @if ($v['url'])
                    <a href="{{ $v['url'] }}" target="_blank" rel="noopener noreferrer" class="venture-link">Visit website ↗</a>
                @else
                    <a href="{{ url('/contact') }}" class="venture-link">Enquire with us →</a>
                @endif
            </div>
        </article>
    @endforeach
</div>
