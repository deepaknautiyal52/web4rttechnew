@extends('layouts.admin', ['title' => 'Support Tickets'])
@php use App\Support\Meta; @endphp

@section('content')
@include('admin.resource.header', [
    'title' => 'Support Tickets',
    'subtitle' => 'Client bug reports and change requests, with priority-based SLA deadlines.',
    'total' => $page->total(),
    'exportType' => 'tickets',
    'addLabel' => '+ New Ticket',
])

@include('admin.resource.form', [
    'formTitle' => 'Ticket',
    'storeUrl' => route('admin.tickets.store'),
    'updateUrl' => $editing ? route('admin.tickets.update', $editing->id) : null,
])

@include('admin.resource.toolbar', [
    'searchPlaceholder' => 'Search tickets...',
    'filters' => [
        ['name' => 'status', 'label' => 'Status', 'options' => ['open_all' => 'All unresolved'] + Meta::options(Meta::TICKET_STATUS)],
        ['name' => 'priority', 'label' => 'Priority', 'options' => Meta::options(Meta::TICKET_PRIORITY)],
    ],
])

@if ($page->isEmpty())
    <div class="admin-empty-state">{{ $filtered ? 'Nothing matches these filters.' : 'Nothing here yet.' }}</div>
@else
    <div class="admin-table-wrap">
        <table class="admin-table resource-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Ticket</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Assignee</th>
                    <th>SLA due</th>
                    <th>Opened</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($page as $t)
                    <tr class="admin-table-row" data-expand="ticket-{{ $t->id }}">
                        <td>#{{ $t->id }}</td>
                        <td>
                            <div class="resource-primary">
                                <strong>{{ $t->title }}</strong>
                                <span>
                                    @if ($t->client)
                                        <a href="{{ route('admin.clients.show', $t->client_id) }}">{{ Meta::clientLabel($t->client) }}</a>
                                    @else
                                        Internal
                                    @endif
                                    {{ $t->project ? ' · '.$t->project->name : '' }}
                                </span>
                            </div>
                        </td>
                        <td><x-badge :meta="Meta::TICKET_PRIORITY" :value="$t->priority" /></td>
                        <td><x-badge :meta="Meta::TICKET_STATUS" :value="$t->status" /></td>
                        <td>{{ $t->employee->name ?? '—' }}</td>
                        <td><span class="{{ $t->sla_breached ? 'expense-cell' : '' }}">{{ Meta::shortDateTime($t->due_at) }}{{ $t->sla_breached ? ' (breached)' : '' }}</span></td>
                        <td>{{ Meta::shortDateTime($t->created_at) }}</td>
                        @include('admin.resource.actions', ['row' => $t, 'deleteUrl' => route('admin.tickets.destroy', $t->id), 'deleteLabel' => $t->title])
                    </tr>
                    <tr class="admin-table-detail-row resource-detail-row" id="ticket-{{ $t->id }}" @if ((string) $openId !== (string) $t->id) hidden @endif>
                        <td colspan="8">
                            <div class="admin-detail-meta">
                                <span><strong>Opened:</strong> {{ Meta::shortDateTime($t->created_at) }}</span>
                                <span><strong>SLA due:</strong> {{ Meta::shortDateTime($t->due_at) }}</span>
                                <span><strong>Resolved:</strong> {{ Meta::shortDateTime($t->resolved_at) }}</span>
                            </div>
                            @if ($t->description)
                                <p>{{ $t->description }}</p>
                            @else
                                <p class="resource-muted">No details.</p>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @include('admin.resource.pagination')
@endif
@endsection
