@extends('layouts.admin', ['title' => 'Projects'])
@php
    use App\Support\Meta;
    use App\Support\Site;
    $today = today()->toDateString();
@endphp

@section('content')
@include('admin.resource.header', [
    'title' => 'Projects',
    'subtitle' => 'Delivery status, deadlines, and budget against actual cost for every client project.',
    'total' => $page->total(),
    'exportType' => 'projects',
    'addLabel' => '+ New Project',
])

@include('admin.resource.form', [
    'formTitle' => 'Project',
    'storeUrl' => route('admin.projects.store'),
    'updateUrl' => $editing ? route('admin.projects.update', $editing->id) : null,
])

@include('admin.resource.toolbar', [
    'searchPlaceholder' => 'Search projects...',
    'filters' => [
        ['name' => 'status', 'label' => 'Status', 'options' => Meta::options(Meta::PROJECT_STATUS)],
        ['name' => 'active', 'label' => 'Scope', 'options' => ['1' => 'Active only']],
    ],
])

@if ($page->isEmpty())
    <div class="admin-empty-state">{{ $filtered ? 'Nothing matches these filters.' : 'Nothing here yet.' }}</div>
@else
    <div class="admin-table-wrap">
        <table class="admin-table resource-table">
            <thead>
                <tr>
                    <th>Project</th>
                    <th>Service</th>
                    <th>Lead</th>
                    <th>Status</th>
                    <th>Deadline</th>
                    <th>Budget</th>
                    <th>Cost so far</th>
                    <th>Received</th>
                    <th>Profit</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($page as $p)
                    @php
                        $cost = (float) $p->labour_cost + (float) $p->other_costs;
                        $late = $p->deadline && $p->deadline->toDateString() < $today && in_array($p->status, ['planning', 'in_progress', 'review'], true);
                        $over = (float) $p->budget && $cost > (float) $p->budget;
                        $profit = (float) $p->revenue - $cost;
                    @endphp
                    <tr class="admin-table-row" data-expand="project-{{ $p->id }}">
                        <td>
                            <div class="resource-primary">
                                <strong>{{ $p->name }}</strong>
                                <span><a href="{{ route('admin.clients.show', $p->client_id) }}">{{ Meta::clientLabel($p->client) }}</a></span>
                            </div>
                        </td>
                        <td>{{ $p->service_id ? (Site::service($p->service_id)['title'] ?? $p->service_id) : '—' }}</td>
                        <td>{{ $p->employee->name ?? '—' }}</td>
                        <td><x-badge :meta="Meta::PROJECT_STATUS" :value="$p->status" /></td>
                        <td><span class="{{ $late ? 'expense-cell' : '' }}">{{ Meta::date($p->deadline) }}{{ $late ? ' (late)' : '' }}</span></td>
                        <td>{{ (float) $p->budget ? Meta::money($p->budget, $p->currency) : '—' }}</td>
                        <td><span class="{{ $over ? 'expense-cell' : '' }}">{{ Meta::money($cost, $p->currency) }}{{ $over ? ' ⚠' : '' }}</span></td>
                        <td>{{ Meta::money($p->revenue, $p->currency) }}</td>
                        <td><span class="{{ $profit >= 0 ? 'finance-amount-income' : 'finance-amount-expense' }}">{{ $profit < 0 ? '-' : '' }}{{ Meta::money(abs($profit), $p->currency) }}</span></td>
                        @include('admin.resource.actions', ['row' => $p, 'deleteUrl' => route('admin.projects.destroy', $p->id), 'deleteLabel' => $p->name])
                    </tr>
                    <tr class="admin-table-detail-row resource-detail-row" id="project-{{ $p->id }}" @if ((string) $openId !== (string) $p->id) hidden @endif>
                        <td colspan="10">
                            <div class="admin-detail-meta">
                                <span><strong>Start:</strong> {{ Meta::date($p->start_date) }}</span>
                                <span><strong>Hours logged:</strong> {{ Meta::num($p->hours_logged) }}h</span>
                                <span><strong>Staff cost:</strong> {{ Meta::money($p->labour_cost, $p->currency) }}</span>
                                <span><strong>Other costs:</strong> {{ Meta::money($p->other_costs ?: 0, $p->currency) }}</span>
                            </div>
                            @if ($p->description)
                                <p>{{ $p->description }}</p>
                            @else
                                <p class="resource-muted">No description.</p>
                            @endif
                            <div class="resource-detail-links">
                                @if (auth()->user()->canAccess('timesheets'))
                                    <a href="{{ route('admin.timesheets.index') }}?create=1&prefill[project_id]={{ $p->id }}">+ Log time</a>
                                @endif
                                @if (auth()->user()->canAccess('invoices'))
                                    <a href="{{ route('admin.invoices.index') }}?create=1&prefill[client_id]={{ $p->client_id }}&prefill[project_id]={{ $p->id }}&prefill[currency]={{ $p->currency }}">+ Create invoice</a>
                                @endif
                                @if (auth()->user()->canAccess('tickets'))
                                    <a href="{{ route('admin.tickets.index') }}?create=1&prefill[client_id]={{ $p->client_id }}&prefill[project_id]={{ $p->id }}">+ New ticket</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @include('admin.resource.pagination')
@endif
@endsection
