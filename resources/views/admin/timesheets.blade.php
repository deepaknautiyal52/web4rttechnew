@extends('layouts.admin', ['title' => 'Timesheets'])
@php
    use App\Support\Meta;
    $barColor = fn ($pct) => $pct > 100 ? '#d03b3b' : ($pct >= 50 ? '#0ca30c' : '#898781');
@endphp

@section('content')
@include('admin.resource.header', [
    'title' => 'Timesheets',
    'subtitle' => "Hours logged per project. Staff time feeds each project's real cost and profit.",
    'total' => $page->total(),
    'exportType' => 'timesheets',
    'addLabel' => '+ Log Time',
])

@include('admin.resource.form', [
    'formTitle' => 'Time Entry',
    'storeUrl' => route('admin.timesheets.store'),
    'updateUrl' => $editing ? route('admin.timesheets.update', $editing->id) : null,
])

<div class="admin-panel" style="margin-bottom: 20px">
    <div class="admin-panel-header">
        <h2>Team utilization</h2>
        <form method="GET" action="{{ url()->current() }}">
            @if (request('search'))<input type="hidden" name="search" value="{{ request('search') }}" />@endif
            <select name="days" class="resource-filter" data-autosubmit>
                @foreach ([7 => 'Last 7 days', 30 => 'Last 30 days', 90 => 'Last 90 days'] as $value => $label)
                    <option value="{{ $value }}" @selected($days === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </form>
    </div>
    @if (count($utilization['data']) === 0)
        <div class="client-section-empty">Add active employees to see utilization.</div>
    @else
        <div class="status-bars">
            @foreach ($utilization['data'] as $e)
                <div class="status-bar-row utilization-row">
                    <span class="status-bar-label">{{ $e['name'] }}</span>
                    <div class="status-bar-track">
                        <div class="status-bar-fill" style="width: {{ min(100, $e['utilization']) }}%; background-color: {{ $barColor($e['utilization']) }}"></div>
                    </div>
                    <span class="status-bar-value utilization-value">{{ $e['utilization'] }}% <small>({{ Meta::num($e['hours']) }}h)</small></span>
                </div>
            @endforeach
        </div>
        <p class="resource-field-help" style="margin-top: 14px">
            Capacity is 8h per weekday: {{ $utilization['capacity'] }}h over the last {{ $utilization['days'] }} days.
        </p>
    @endif
</div>

@include('admin.resource.toolbar', [
    'searchPlaceholder' => 'Search descriptions...',
    'keep' => request()->has('days') ? ['days' => $days] : [],
])

@if ($page->isEmpty())
    <div class="admin-empty-state">{{ request()->filled('search') ? 'Nothing matches these filters.' : 'Nothing here yet.' }}</div>
@else
    <div class="admin-table-wrap">
        <table class="admin-table resource-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Employee</th>
                    <th>Project</th>
                    <th>Hours</th>
                    <th>Description</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($page as $t)
                    <tr>
                        <td>{{ Meta::date($t->work_date) }}</td>
                        <td>{{ $t->employee->name ?? '—' }}</td>
                        <td>
                            <div class="resource-primary">
                                <strong>{{ $t->project->name ?? '—' }}</strong>
                                @if ($t->project && $t->project->client)<span>{{ Meta::clientLabel($t->project->client) }}</span>@endif
                            </div>
                        </td>
                        <td>{{ Meta::num($t->hours) }}h</td>
                        <td>{{ $t->description ?: '—' }}</td>
                        @include('admin.resource.actions', ['row' => $t, 'deleteUrl' => route('admin.timesheets.destroy', $t->id), 'deleteLabel' => Meta::num($t->hours).'h on '.Meta::date($t->work_date)])
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @include('admin.resource.pagination')
@endif
@endsection
