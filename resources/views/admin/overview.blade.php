@extends('layouts.admin', ['title' => 'Overview'])
@php
    use App\Support\Meta;
    use App\Support\Site;
    $user = auth()->user();
    $a = $attention;
    $clientLabel = fn ($c) => $c ? ($c['company'] ?: $c['name']) : '—';

    // "Needs attention" stat tiles.
    $stats = [];
    if (isset($a['receivables'])) {
        $r = $a['receivables'];
        $stats[] = ['label' => 'Outstanding', 'value' => Meta::money($r['outstanding']), 'sub' => $r['outstanding_count'].' unpaid invoice'.($r['outstanding_count'] === 1 ? '' : 's'), 'to' => route('admin.invoices.index'), 'icon' => 'receipt', 'accent' => '#4F46E5'];
        $stats[] = ['label' => 'Overdue', 'value' => Meta::money($r['overdue_total']), 'sub' => count($r['overdue']).' past due', 'tone' => $r['overdue_total'] > 0 ? 'expense-text' : null, 'to' => route('admin.invoices.index'), 'icon' => 'alert', 'accent' => '#DC2626'];
    }
    if (isset($a['projects'])) {
        $p = $a['projects'];
        $stats[] = ['label' => 'Active projects', 'value' => $p['active'], 'sub' => count($p['overdue']).' late · '.count($p['over_budget']).' over budget', 'to' => route('admin.projects.index'), 'icon' => 'briefcase', 'accent' => '#0891B2'];
    }
    if (isset($a['tickets'])) {
        $t = $a['tickets'];
        $stats[] = ['label' => 'Open tickets', 'value' => $t['open'], 'sub' => $t['breached'].' past SLA · '.$t['urgent'].' high/urgent', 'tone' => $t['breached'] > 0 ? 'expense-text' : null, 'to' => route('admin.tickets.index'), 'icon' => 'lifebuoy', 'accent' => '#D97706'];
    }
    if (array_key_exists('monthly_burn', $a)) {
        $stats[] = ['label' => 'Monthly burn', 'value' => Meta::money($a['monthly_burn']), 'sub' => 'Recurring expenses', 'to' => route('admin.finances.index'), 'icon' => 'wallet', 'accent' => '#7C3AED'];
    }

    $now = now(config('app.display_timezone'));
    $greeting = $now->hour < 12 ? 'Good morning' : ($now->hour < 17 ? 'Good afternoon' : 'Good evening');
    $quickLinks = array_filter([
        $user->canAccess('leads') ? ['url' => route('admin.leads.index'), 'label' => 'Leads', 'icon' => 'inbox'] : null,
        $user->canAccess('invoices') ? ['url' => route('admin.invoices.index'), 'label' => 'Invoices', 'icon' => 'receipt'] : null,
        $user->canAccess('projects') ? ['url' => route('admin.projects.index'), 'label' => 'Projects', 'icon' => 'briefcase'] : null,
    ]);

    $riskProjects = [];
    if (isset($a['projects'])) {
        foreach ($a['projects']['overdue'] as $p) {
            $riskProjects[] = $p + ['why' => 'Deadline '.Meta::date($p['deadline']).' passed'];
        }
        foreach ($a['projects']['over_budget'] as $p) {
            $riskProjects[] = $p + ['why' => 'Cost '.Meta::money($p['cost'], $p['currency']).' vs budget '.Meta::money($p['budget'], $p['currency'])];
        }
    }

    // Lead analytics (only for roles with access to leads).
    if ($contacts !== null) {
        $won = $contacts->where('status', 'won');
        $lost = $contacts->where('status', 'lost');
        $decided = $won->count() + $lost->count();
        $leadStats = [
            'total' => $contacts->count(),
            'won' => $won->count(),
            'totalRevenue' => $won->sum(fn ($c) => (float) $c->deal_value),
            'winRate' => $decided > 0 ? round($won->count() / $decided * 100) : null,
        ];

        $serviceBreakdown = $contacts->groupBy(fn ($c) => $c->service_interested ?: 'other')
            ->map(fn ($g, $id) => ['label' => $id === 'other' ? 'Not specified' : (Site::service($id)['title'] ?? $id), 'value' => $g->count()])
            ->sortByDesc('value')->take(6)->values();

        $statusBreakdown = collect(Meta::LEAD_STATUS)->map(fn ($m, $key) => $m + ['value' => $contacts->where('status', $key)->count()]);
        $maxStatus = max(1, $statusBreakdown->max('value'));

        // Pipeline insights. Win rate = won / (won + lost): only decided deals count.
        $winRates = function ($keyFn) use ($contacts) {
            return $contacts->groupBy($keyFn)->map(function ($g, $key) {
                $w = $g->where('status', 'won')->count();
                $l = $g->where('status', 'lost')->count();
                return ['key' => $key, 'total' => $g->count(), 'won' => $w, 'rate' => $w + $l > 0 ? round($w / ($w + $l) * 100) : null];
            })->sortByDesc('total')->values();
        };
        $withValue = $won->filter(fn ($c) => (float) $c->deal_value > 0);
        $withDates = $won->filter(fn ($c) => $c->won_at && $c->created_at);
        $lostReasons = $lost->groupBy(fn ($c) => $c->lost_reason ?: 'No reason recorded')->map->count()->sortDesc();
        $insights = [
            'avgDeal' => $withValue->count() ? $withValue->sum(fn ($c) => (float) $c->deal_value) / $withValue->count() : null,
            'avgDays' => $withDates->count() ? (int) round($withDates->sum(fn ($c) => ($c->won_at->getTimestamp() - $c->created_at->getTimestamp()) / 86400) / $withDates->count()) : null,
            'open' => $contacts->whereNotIn('status', ['won', 'lost'])->count(),
            'byService' => $winRates(fn ($c) => (!$c->service_interested || $c->service_interested === 'other') ? 'Not specified' : (Site::service($c->service_interested)['title'] ?? $c->service_interested)),
            'byBudget' => $winRates(fn ($c) => $c->budget_range ?: 'Not specified'),
            'lostReasons' => $lostReasons,
            'lostTotal' => $lost->count(),
        ];

        $recent = $contacts->sortByDesc('created_at')->take(5);
        $chartContacts = $contacts->map(fn ($c) => [
            'status' => $c->status,
            'deal_value' => (float) $c->deal_value,
            'created_at' => $c->created_at?->toIso8601String(),
            'won_at' => $c->won_at?->toIso8601String(),
        ])->values();
    }
@endphp

@section('content')
<section class="admin-welcome">
    <div class="admin-welcome-copy">
        <span class="admin-welcome-date">
            @include('admin.partials.icon', ['name' => 'calendar', 'class' => 'admin-welcome-date-icon'])
            {{ $now->format('l, j F Y') }}
        </span>
        <h1>{{ $greeting }}{{ $user->name ? ', '.$user->name : '' }}</h1>
        <p>Here is what needs attention today, plus inquiry volume and business performance at a glance.</p>
    </div>
    @if (count($quickLinks))
        <div class="admin-welcome-actions">
            @foreach ($quickLinks as $q)
                <a href="{{ $q['url'] }}" class="admin-welcome-btn">
                    @include('admin.partials.icon', ['name' => $q['icon'], 'class' => 'admin-welcome-btn-icon'])
                    {{ $q['label'] }}
                </a>
            @endforeach
        </div>
    @endif
</section>

{{-- What needs doing today --}}
@if (count($stats))
    <div class="admin-stat-grid attention-stats">
        @foreach ($stats as $s)
            @include('admin.partials.stat', $s + ['href' => $s['to']])
        @endforeach
    </div>
@endif

@php
    $showFollowUps = isset($a['follow_ups']);
    $showStale = !empty($a['stale_leads']);
    $showRenewals = isset($a['renewals']);
    $showOverdue = isset($a['receivables']) && count($a['receivables']['overdue']) > 0;
    $showRisk = count($riskProjects) > 0;
@endphp
@if ($showFollowUps || $showStale || $showRenewals || $showOverdue || $showRisk)
    <div class="attention-grid">
        @if ($showFollowUps)
            <div class="admin-panel attention-list">
                <div class="admin-panel-header">
                    <h2>Follow-ups due</h2>
                    <a href="{{ route('admin.leads.index') }}" class="admin-panel-link">View all →</a>
                </div>
                @if (count($a['follow_ups']) === 0)
                    <div class="attention-empty">
                        @include('admin.partials.icon', ['name' => 'check', 'class' => 'attention-empty-icon'])
                        <span><strong>All caught up</strong>No follow-ups due right now.</span>
                    </div>
                @else
                    <ul>
                        @foreach ($a['follow_ups'] as $c)
                            <li>
                                <span><strong>{{ $c['name'] }}</strong>{{ $c['company'] ? ' ('.$c['company'].')' : '' }}: {{ $c['subject'] }}</span>
                                <span class="expense-cell">{{ Meta::date($c['next_follow_up_at']) }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif

        @if ($showStale)
            <div class="admin-panel attention-list">
                <div class="admin-panel-header">
                    <h2>New leads waiting over 48h</h2>
                    <a href="{{ route('admin.leads.index') }}" class="admin-panel-link">View all →</a>
                </div>
                <ul>
                    @foreach ($a['stale_leads'] as $c)
                        <li>
                            <span><strong>{{ $c['name'] }}</strong>: {{ $c['subject'] }}</span>
                            <span class="resource-muted">{{ Meta::date($c['created_at']) }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($showRenewals)
            <div class="admin-panel attention-list">
                <div class="admin-panel-header">
                    <h2>Renewals due in 30 days</h2>
                    <a href="{{ route('admin.renewals.index') }}" class="admin-panel-link">View all →</a>
                </div>
                @if (count($a['renewals']) === 0)
                    <div class="attention-empty">
                        @include('admin.partials.icon', ['name' => 'check', 'class' => 'attention-empty-icon'])
                        <span><strong>Nothing to renew</strong>Nothing expires in the next 30 days.</span>
                    </div>
                @else
                    <ul>
                        @foreach ($a['renewals'] as $r)
                            <li>
                                <span><strong>{{ Meta::RENEWAL_TYPE[$r['type']]['label'] ?? '' }}</strong> {{ $r['name'] }}{{ $r['client'] ? ' · '.$clientLabel($r['client']) : '' }}</span>
                                @include('admin.partials.days-left', ['days' => $r['days_left']])
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif

        @if ($showOverdue)
            <div class="admin-panel attention-list">
                <div class="admin-panel-header">
                    <h2>Overdue invoices</h2>
                    <a href="{{ route('admin.invoices.index') }}" class="admin-panel-link">View all →</a>
                </div>
                <ul>
                    @foreach ($a['receivables']['overdue'] as $i)
                        <li>
                            <span><strong>{{ $i['number'] }}</strong> · {{ $clientLabel($i['client']) }}</span>
                            <span class="expense-cell">{{ Meta::money($i['balance'], $i['currency']) }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($showRisk)
            <div class="admin-panel attention-list">
                <div class="admin-panel-header">
                    <h2>Projects at risk</h2>
                    <a href="{{ route('admin.projects.index') }}" class="admin-panel-link">View all →</a>
                </div>
                <ul>
                    @foreach ($riskProjects as $p)
                        <li>
                            <span><strong>{{ $p['name'] }}</strong>{{ !empty($p['client']) ? ' · '.$clientLabel($p['client']) : '' }}</span>
                            <span class="expense-cell">{{ $p['why'] }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@endif

@if ($contacts !== null)
    <h2 class="admin-section-title">Inquiries</h2>
    <div class="admin-stat-grid">
        @foreach ([
            ['label' => 'Total inquiries', 'value' => $leadStats['total'], 'icon' => 'inbox', 'accent' => '#4F46E5'],
            ['label' => 'Won deals', 'value' => $leadStats['won'], 'icon' => 'trophy', 'accent' => '#059669'],
            ['label' => 'Total revenue', 'value' => Meta::usd($leadStats['totalRevenue']), 'icon' => 'trend', 'accent' => '#0891B2'],
            ['label' => 'Win rate', 'value' => $leadStats['winRate'] === null ? '—' : $leadStats['winRate'].'%', 'icon' => 'percent', 'accent' => '#D97706'],
        ] as $s)
            @include('admin.partials.stat', $s)
        @endforeach
    </div>

    @include('admin.partials.period-toggle')

    <div class="admin-chart-grid">
        <div class="admin-panel">
            <h2 class="admin-chart-title">Inquiries Received</h2>
            <div class="chart-box" style="height: 240px"><canvas id="chart-inquiries"></canvas></div>
        </div>
        <div class="admin-panel">
            <h2 class="admin-chart-title">Revenue Won</h2>
            <div class="chart-box" style="height: 240px"><canvas id="chart-revenue"></canvas></div>
        </div>
    </div>

    <div class="admin-chart-grid">
        <div class="admin-panel">
            <h2 class="admin-chart-title">Top Services Requested</h2>
            @if ($serviceBreakdown->isEmpty())
                <div class="admin-empty-state">No service data yet.</div>
            @else
                <div class="chart-box" style="height: 240px"><canvas id="chart-services"></canvas></div>
            @endif
        </div>

        <div class="admin-panel">
            <h2 class="admin-chart-title">Pipeline by Status</h2>
            <div class="status-bars">
                @foreach ($statusBreakdown as $s)
                    <div class="status-bar-row">
                        <span class="status-bar-label">
                            <span class="status-dot" style="background-color: {{ $s['color'] }}"></span>
                            {{ $s['label'] }}
                        </span>
                        <div class="status-bar-track">
                            <div class="status-bar-fill" style="width: {{ $s['value'] / $maxStatus * 100 }}%; background-color: {{ $s['color'] }}"></div>
                        </div>
                        <span class="status-bar-value">{{ $s['value'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Sales pipeline --}}
    <h2 class="admin-section-title">Sales pipeline</h2>
    <div class="admin-stat-grid">
        @foreach ([
            ['label' => 'Open deals', 'value' => $insights['open'], 'icon' => 'briefcase', 'accent' => '#4F46E5'],
            ['label' => 'Average deal size', 'value' => $insights['avgDeal'] === null ? '—' : Meta::usd($insights['avgDeal']), 'icon' => 'wallet', 'accent' => '#0891B2'],
            ['label' => 'Average time to win', 'value' => $insights['avgDays'] === null ? '—' : $insights['avgDays'].' day'.($insights['avgDays'] === 1 ? '' : 's'), 'icon' => 'clock', 'accent' => '#7C3AED'],
            ['label' => 'Deals lost', 'value' => $insights['lostTotal'], 'icon' => 'alert', 'accent' => '#DC2626'],
        ] as $s)
            @include('admin.partials.stat', $s)
        @endforeach
    </div>

    <div class="admin-chart-grid insights-grid">
        @foreach (['Win rate by service' => $insights['byService'], 'Win rate by budget' => $insights['byBudget']] as $title => $rows)
            <div class="admin-panel">
                <h2 class="admin-chart-title">{{ $title }}</h2>
                @if ($rows->isEmpty())
                    <div class="client-section-empty">No data yet.</div>
                @else
                    <table class="finance-table compact-table insights-table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Leads</th>
                                <th>Won</th>
                                <th>Win rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows->take(7) as $r)
                                <tr>
                                    <td>{{ $r['key'] }}</td>
                                    <td>{{ $r['total'] }}</td>
                                    <td>{{ $r['won'] }}</td>
                                    <td>
                                        @if ($r['rate'] === null)
                                            <span class="resource-muted">—</span>
                                        @else
                                            <span class="insights-rate">
                                                <span class="insights-rate-bar" style="width: {{ $r['rate'] }}%"></span>
                                                {{ $r['rate'] }}%
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        @endforeach
    </div>

    @if ($insights['lostReasons']->isNotEmpty())
        @php $maxLost = $insights['lostReasons']->first(); @endphp
        <div class="admin-panel" style="margin-bottom: 20px">
            <h2 class="admin-chart-title">Why deals are lost</h2>
            <div class="status-bars">
                @foreach ($insights['lostReasons'] as $reason => $count)
                    <div class="status-bar-row lost-reason-row">
                        <span class="status-bar-label">{{ $reason }}</span>
                        <div class="status-bar-track">
                            <div class="status-bar-fill" style="width: {{ $count / $maxLost * 100 }}%; background-color: #d03b3b"></div>
                        </div>
                        <span class="status-bar-value">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="admin-panel">
        <div class="admin-panel-header">
            <h2>Recent Submissions</h2>
            <a href="{{ route('admin.leads.index') }}" class="admin-panel-link">View all →</a>
        </div>

        @if ($recent->isEmpty())
            <div class="admin-empty-state">No contact submissions yet.</div>
        @else
            <ul class="admin-recent-list">
                @foreach ($recent as $c)
                    <li class="admin-recent-item">
                        <div class="admin-recent-avatar">{{ mb_strtoupper(mb_substr($c->name, 0, 1)) }}</div>
                        <div class="admin-recent-info">
                            <span class="admin-recent-name">{{ $c->name }}</span>
                            <span class="admin-recent-subject">{{ $c->subject }}</span>
                        </div>
                        <span class="admin-recent-date">{{ $c->created_at->copy()->setTimezone(config('app.display_timezone'))->format('j/n/Y') }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endif
@endsection

@if ($contacts !== null)
@push('scripts')
<script src="{{ asset('js/vendor/chart.umd.min.js') }}"></script>
<script>
(function () {
    var contacts = @json($chartContacts);
    var services = @json($serviceBreakdown);
    var A = window.AdminCharts;
    var usd = function (v) { return '$' + Math.round(v).toLocaleString('en-US'); };
    var inquiries = function (v) { return v + ' inquir' + (v === 1 ? 'y' : 'ies'); };

    var inquiryChart = A.lineChart('chart-inquiries', '#4F46E5', inquiries, null, true);
    var revenueChart = A.lineChart('chart-revenue', '#0E7490', usd, function (v) { return '$' + (v >= 1000 ? (v / 1000).toFixed(0) + 'k' : v); }, true);

    A.onPeriod(function (period) {
        var buckets = A.buildBuckets(period);
        var labels = buckets.map(function (b) { return b.label; });
        var inq = buckets.map(function (b) {
            return contacts.filter(function (c) {
                var t = new Date(c.created_at);
                return t >= b.start && t < b.end;
            }).length;
        });
        var won = contacts.filter(function (c) { return c.status === 'won' && c.won_at; });
        var rev = buckets.map(function (b) {
            return won.filter(function (c) {
                var t = new Date(c.won_at);
                return t >= b.start && t < b.end;
            }).reduce(function (s, c) { return s + (c.deal_value || 0); }, 0);
        });
        A.setData(inquiryChart, labels, inq);
        A.setData(revenueChart, labels, rev);
    });

    if (services.length) {
        A.barChart('chart-services', '#4F46E5', services.map(function (s) { return s.label; }), services.map(function (s) { return s.value; }), inquiries, null, 140, true);
    }
})();
</script>
@endpush
@endif
