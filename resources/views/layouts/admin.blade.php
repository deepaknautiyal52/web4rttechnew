@php
    use App\Support\AdminNav;
    use App\Support\Meta;
    $user = auth()->user();
    $initials = strtoupper(collect(explode(' ', $user->name ?: 'A'))->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode(''));
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="theme-color" content="#4F46E5" />
    <meta name="robots" content="noindex, nofollow" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <title>{{ isset($title) ? $title.' · ' : '' }}Admin | Web4rtTech</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" />
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}" />
</head>
<body class="admin-body">
<div id="root">
    <div class="App">
        <div class="admin-shell" id="admin-shell">
            <aside class="admin-sidebar">
                <div class="admin-sidebar-logo">
                    <span class="admin-sidebar-logo-mark">4</span>
                    <span class="admin-sidebar-logo-copy">
                        <span class="admin-sidebar-logo-text">Web<span>4</span>rtTech</span>
                        <span class="admin-sidebar-logo-tagline">Build · Grow · Automate</span>
                    </span>
                    <button type="button" class="admin-nav-toggle" id="admin-nav-toggle" aria-label="Toggle menu">☰</button>
                </div>

                <nav class="admin-nav">
                    @foreach (AdminNav::SECTIONS as $section)
                        @php $items = array_filter($section['items'], fn ($i) => empty($i['area']) || $user->canAccess($i['area'])); @endphp
                        @if (count($items))
                            <div class="admin-nav-section">
                                @isset($section['heading'])
                                    <span class="admin-nav-heading">{{ $section['heading'] }}</span>
                                @endisset
                                @foreach ($items as $item)
                                    <a href="{{ url($item['url']) }}" class="admin-nav-link{{ AdminNav::isActive($item) ? ' active' : '' }}">
                                        @include('admin.partials.icon', ['name' => $item['icon']])
                                        {{ $item['label'] }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                    <a href="{{ url('/') }}" target="_blank" rel="noreferrer" class="admin-nav-link admin-nav-link-muted">
                        @include('admin.partials.icon', ['name' => 'globe'])
                        View Site
                    </a>
                </nav>

                <div class="admin-sidebar-footer">
                    <div class="admin-user">
                        <div class="admin-user-avatar">{{ $initials }}</div>
                        <div class="admin-user-meta">
                            <span class="admin-user-name">{{ $user->name ?: 'Admin' }}</span>
                            <span class="admin-user-email">{{ Meta::ROLE_META[$user->role ?: 'admin']['label'] ?? '' }} · {{ $user->email }}</span>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="admin-logout-btn" title="Log out">
                            @include('admin.partials.icon', ['name' => 'logout'])
                        </button>
                    </form>
                </div>
            </aside>

            <div class="admin-main">
                @yield('content')
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('js/admin.js') }}?v={{ filemtime(public_path('js/admin.js')) }}"></script>
@stack('scripts')
</body>
</html>
