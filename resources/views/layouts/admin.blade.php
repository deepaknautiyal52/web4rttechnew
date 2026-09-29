@php
    use App\Support\Meta;
    $user = auth()->user();
    $nav = [
        ['items' => [['url' => '/admin', 'label' => 'Overview', 'icon' => 'grid', 'end' => true]]],
        ['heading' => 'Sales', 'items' => [
            ['url' => '/admin/submissions', 'label' => 'Leads', 'icon' => 'inbox', 'area' => 'leads'],
            ['url' => '/admin/quotations', 'label' => 'Quotations', 'icon' => 'file', 'area' => 'quotations'],
            ['url' => '/admin/clients', 'label' => 'Clients', 'icon' => 'building', 'area' => 'clients'],
        ]],
        ['heading' => 'Delivery', 'items' => [
            ['url' => '/admin/projects', 'label' => 'Projects', 'icon' => 'briefcase', 'area' => 'projects'],
            ['url' => '/admin/timesheets', 'label' => 'Timesheets', 'icon' => 'clock', 'area' => 'timesheets'],
            ['url' => '/admin/tickets', 'label' => 'Support Tickets', 'icon' => 'lifebuoy', 'area' => 'tickets'],
        ]],
        ['heading' => 'Money', 'items' => [
            ['url' => '/admin/invoices', 'label' => 'Invoices', 'icon' => 'receipt', 'area' => 'invoices'],
            ['url' => '/admin/finances', 'label' => 'Finances', 'icon' => 'wallet', 'area' => 'finances'],
            ['url' => '/admin/renewals', 'label' => 'Renewals', 'icon' => 'refresh', 'area' => 'renewals'],
        ]],
        ['heading' => 'Company', 'items' => [
            ['url' => '/admin/team', 'label' => 'Team', 'icon' => 'users', 'area' => 'employees'],
            ['url' => '/admin/news', 'label' => 'News', 'icon' => 'news', 'area' => 'content'],
            ['url' => '/admin/users', 'label' => 'Users & Roles', 'icon' => 'shield', 'area' => 'users'],
            ['url' => '/admin/audit-log', 'label' => 'Audit Log', 'icon' => 'list', 'area' => 'audit'],
            ['url' => '/admin/settings', 'label' => 'Settings', 'icon' => 'settings'],
        ]],
    ];
    $isActive = fn ($item) => !empty($item['end'])
        ? request()->is(ltrim($item['url'], '/'))
        : request()->is(ltrim($item['url'], '/'), ltrim($item['url'], '/').'/*');
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
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}" />
</head>
<body>
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
                    @foreach ($nav as $section)
                        @php $items = array_filter($section['items'], fn ($i) => empty($i['area']) || $user->canAccess($i['area'])); @endphp
                        @if (count($items))
                            <div class="admin-nav-section">
                                @isset($section['heading'])
                                    <span class="admin-nav-heading">{{ $section['heading'] }}</span>
                                @endisset
                                @foreach ($items as $item)
                                    <a href="{{ url($item['url']) }}" class="admin-nav-link{{ $isActive($item) ? ' active' : '' }}">
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
