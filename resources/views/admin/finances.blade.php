@extends('layouts.admin', ['title' => 'Company Finances'])
@php
    use App\Support\Meta;
    $useOld = session()->hasOldInput() && old('_form') === 'finance';
    $f = function ($name, $default = '') use ($useOld, $editing) {
        if ($useOld) return old($name);
        if ($editing) {
            $v = $editing->{$name};
            return $v instanceof \DateTimeInterface ? $v->format('Y-m-d') : ($v ?? $default);
        }
        return $default;
    };
    $categoryTotals = $entries->where('type', 'expense')->groupBy('category')
        ->map(fn ($group, $cat) => [
            'label' => Meta::CATEGORY_META[$cat]['label'] ?? $cat,
            'value' => $group->sum(fn ($e) => (float) $e->amount),
        ])->sortByDesc('value')->values();
    $chartEntries = $entries->map(fn ($e) => ['type' => $e->type, 'amount' => (float) $e->amount, 'period_start' => optional($e->period_start)->toDateString()])->values();
@endphp

@section('content')
<div class="admin-page-header">
    @include('admin.partials.page-title', ['title' => 'Company Finances', 'subtitle' => 'Domain, hosting, and salary costs alongside sales & income.'])
    <div class="admin-header-actions">
        <a href="{{ route('admin.export', 'finances') }}" class="admin-secondary-btn">@include('admin.partials.icon', ['name' => 'download', 'class' => 'admin-btn-icon']) Export CSV</a>
        <a href="{{ route('admin.finances.index') }}?create=1" class="admin-add-btn">+ Add Entry</a>
    </div>
</div>

@if ($errors->any() && !$formOpen)
    <div class="admin-dashboard-error">{{ $errors->first() }}</div>
@endif

@if ($formOpen)
    <div class="admin-panel finance-form-panel">
        <div class="admin-panel-header">
            <h2>{{ $editing ? 'Edit Entry' : 'Add Entry' }}</h2>
            <a href="{{ route('admin.finances.index') }}" class="admin-panel-link">Cancel</a>
        </div>

        @if ($errors->any())
            <div class="form-alert form-alert-error">Please check the highlighted fields and try again.</div>
        @endif

        <form method="POST" action="{{ $editing ? route('admin.finances.update', $editing->id) : route('admin.finances.store') }}" class="finance-form" novalidate data-submitting-label="Saving...">
            @csrf
            @if ($editing) @method('PUT') @endif
            <input type="hidden" name="_form" value="finance" />
            <div class="finance-form-row">
                <div class="admin-form-group">
                    <label for="type">Type</label>
                    <select id="type" name="type">
                        <option value="expense" @selected($f('type', 'expense') === 'expense')>Expense</option>
                        <option value="income" @selected($f('type', 'expense') === 'income')>Income</option>
                    </select>
                </div>
                <div class="admin-form-group">
                    <label for="category">Category</label>
                    <select id="category" name="category">
                        @foreach (Meta::CATEGORY_META as $value => $meta)
                            <option value="{{ $value }}" @selected($f('category', 'domain') === $value)>{{ $meta['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="admin-form-group">
                <label for="title">Title *</label>
                <input type="text" id="title" name="title" value="{{ $f('title') }}" required placeholder="e.g. web4rttech.com domain renewal" />
                @error('title')<span class="field-error">{{ $message }}</span>@enderror
            </div>

            <div class="admin-form-group">
                <label for="party_name">Vendor / Employee / Client</label>
                <input type="text" id="party_name" name="party_name" value="{{ $f('party_name') }}" placeholder="e.g. Namecheap, Rahul Sharma, Acme Corp" />
            </div>

            <div class="finance-form-row">
                <div class="admin-form-group">
                    <label for="amount">Amount *</label>
                    <input type="number" id="amount" name="amount" min="0" step="0.01" value="{{ $f('amount') }}" required />
                    @error('amount')<span class="field-error">{{ $message }}</span>@enderror
                </div>
                <div class="admin-form-group">
                    <label for="currency">Currency</label>
                    <select id="currency" name="currency">
                        @foreach (Meta::CURRENCIES as $c)
                            <option value="{{ $c }}" @selected($f('currency', 'INR') === $c)>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="finance-form-row">
                <div class="admin-form-group">
                    <label for="period_start">Valid From / Date *</label>
                    <input type="date" id="period_start" name="period_start" value="{{ $f('period_start') }}" required />
                    @error('period_start')<span class="field-error">{{ $message }}</span>@enderror
                </div>
                <div class="admin-form-group">
                    <label for="period_end">Valid To (optional)</label>
                    <input type="date" id="period_end" name="period_end" value="{{ $f('period_end') }}" />
                    @error('period_end')<span class="field-error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="admin-form-group">
                <label for="recurrence">Repeats</label>
                <select id="recurrence" name="recurrence">
                    @foreach (Meta::RECURRENCE_OPTIONS as $value => $label)
                        <option value="{{ $value }}" @selected((string) $f('recurrence') === (string) $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <span class="resource-field-help">
                    Recurring expenses (salaries, tools, hosting) count towards the monthly burn figure.
                </span>
            </div>

            <div class="admin-form-group">
                <label for="notes">Notes</label>
                <textarea id="notes" name="notes" rows="3" placeholder="Optional details...">{{ $f('notes') }}</textarea>
            </div>

            <div class="admin-form-actions">
                <a href="{{ route('admin.finances.index') }}" class="admin-secondary-btn">Cancel</a>
                <button type="submit" class="admin-settings-btn">{{ $editing ? 'Update Entry' : 'Add Entry' }}</button>
            </div>
        </form>
    </div>
@endif

<div class="admin-stat-grid">
    @include('admin.partials.stat', ['label' => 'Total income', 'value' => Meta::money($stats['totalIncome']), 'tone' => 'income-text', 'icon' => 'trend', 'accent' => '#059669'])
    @include('admin.partials.stat', ['label' => 'Total expenses', 'value' => Meta::money($stats['totalExpense']), 'tone' => 'expense-text', 'icon' => 'receipt', 'accent' => '#DC2626'])
    @include('admin.partials.stat', ['label' => 'Net', 'value' => ($stats['net'] >= 0 ? '+' : '-').Meta::money(abs($stats['net'])), 'tone' => $stats['net'] >= 0 ? 'income-text' : 'expense-text', 'icon' => 'wallet', 'accent' => '#4F46E5'])
    @include('admin.partials.stat', ['label' => 'Monthly burn', 'value' => Meta::money($stats['monthlyBurn']), 'tone' => 'expense-text', 'icon' => 'refresh', 'accent' => '#D97706', 'sub' => $stats['recurringCount'].' recurring expense'.($stats['recurringCount'] === 1 ? '' : 's')])
</div>

@include('admin.partials.period-toggle')

<div class="admin-chart-grid">
    <div class="admin-panel">
        <h2 class="admin-chart-title">Income Over Time</h2>
        <div class="chart-box" style="height: 220px"><canvas id="chart-income"></canvas></div>
    </div>
    <div class="admin-panel">
        <h2 class="admin-chart-title">Expenses Over Time</h2>
        <div class="chart-box" style="height: 220px"><canvas id="chart-expense"></canvas></div>
    </div>
</div>

<div class="admin-panel" style="margin-bottom: 20px">
    <h2 class="admin-chart-title">Profit Margin</h2>
    <div class="admin-empty-state" id="margin-empty" hidden>No income recorded in this period yet.</div>
    <div class="chart-box" id="margin-box" style="height: 200px"><canvas id="chart-margin"></canvas></div>
</div>

<div class="admin-panel" style="margin-bottom: 20px">
    <h2 class="admin-chart-title">Expenses by Category</h2>
    @if ($categoryTotals->isEmpty())
        <div class="admin-empty-state">No expenses recorded yet.</div>
    @else
        <div class="chart-box" style="height: {{ max(120, $categoryTotals->count() * 46) }}px"><canvas id="chart-category"></canvas></div>
    @endif
</div>

<div class="admin-panel">
    <div class="admin-panel-header">
        <h2>All Entries</h2>
    </div>

    @if ($sorted->isEmpty())
        <div class="admin-empty-state">No finance entries yet. Add your first one above.</div>
    @else
        <div class="finance-table-wrap">
            <table class="finance-table">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Category</th>
                        <th>Title</th>
                        <th>Vendor / Party</th>
                        <th>Amount</th>
                        <th>Period</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sorted as $entry)
                        @php
                            $start = optional($entry->period_start)->toDateString();
                            $end = optional($entry->period_end)->toDateString();
                        @endphp
                        <tr>
                            <td><span class="finance-type-badge {{ $entry->type }}">{{ $entry->type === 'income' ? 'Income' : 'Expense' }}</span></td>
                            <td>
                                <span class="finance-category-dot" style="background-color: {{ Meta::CATEGORY_META[$entry->category]['color'] ?? '#898781' }}"></span>
                                {{ Meta::CATEGORY_META[$entry->category]['label'] ?? $entry->category }}
                            </td>
                            <td>
                                {{ $entry->title }}
                                @if ($entry->recurrence)
                                    <span class="finance-recurring-tag" title="Recurring">↻ {{ Meta::RECURRENCE_OPTIONS[$entry->recurrence] ?? '' }}</span>
                                @endif
                            </td>
                            <td>{{ $entry->party_name ?: '—' }}</td>
                            <td class="{{ $entry->type === 'income' ? 'finance-amount-income' : 'finance-amount-expense' }}">{{ Meta::money($entry->amount, $entry->currency) }}</td>
                            <td>{{ $start }}{{ $end && $end !== $start ? ' → '.$end : '' }}</td>
                            <td class="finance-row-actions">
                                <button type="button" data-href="{{ route('admin.finances.index') }}?edit={{ $entry->id }}" title="Edit" aria-label="Edit">@include('admin.partials.icon', ['name' => 'edit', 'class' => ''])</button>
                                <form method="POST" action="{{ route('admin.finances.destroy', $entry->id) }}" style="display:inline" data-confirm="Delete &quot;{{ $entry->title }}&quot;? This cannot be undone.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Delete" aria-label="Delete" class="row-action-danger">@include('admin.partials.icon', ['name' => 'trash', 'class' => ''])</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/vendor/chart.umd.min.js') }}"></script>
<script>
(function () {
    var entries = @json($chartEntries);
    var categories = @json($categoryTotals);
    var A = window.AdminCharts;
    var rupeeTick = function (v) { return '₹' + (v >= 1000 ? (v / 1000).toFixed(0) + 'k' : v); };
    var money = function (v) { return A.formatMoney(v); };

    function byType(buckets, type) {
        var list = entries.filter(function (e) { return e.type === type; });
        return buckets.map(function (b) {
            return list.filter(function (e) {
                var t = A.parseDate(e.period_start);
                return t >= b.start && t < b.end;
            }).reduce(function (s, e) { return s + (e.amount || 0); }, 0);
        });
    }

    var income = A.lineChart('chart-income', '#0ca30c', money, rupeeTick);
    var expense = A.lineChart('chart-expense', '#d03b3b', money, rupeeTick);
    var margin = A.lineChart('chart-margin', '#4F46E5', function (v) { return v + '% margin'; }, function (v) { return v + '%'; });

    A.onPeriod(function (period) {
        var buckets = A.buildBuckets(period);
        var labels = buckets.map(function (b) { return b.label; });
        var inc = byType(buckets, 'income');
        var exp = byType(buckets, 'expense');
        var mar = inc.map(function (v, i) { return v > 0 ? Math.round(((v - exp[i]) / v) * 100) : null; });
        A.setData(income, labels, inc);
        A.setData(expense, labels, exp);
        A.setData(margin, labels, mar);
        var none = mar.every(function (m) { return m === null; });
        document.getElementById('margin-empty').hidden = !none;
        document.getElementById('margin-box').hidden = none;
    });

    if (categories.length) {
        A.barChart('chart-category', '#d03b3b', categories.map(function (c) { return c.label; }), categories.map(function (c) { return c.value; }), money, rupeeTick, 100);
    }
})();
</script>
@endpush
