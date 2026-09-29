@extends('layouts.admin', ['title' => 'Clients'])
@php use App\Support\Meta; @endphp

@section('content')
@include('admin.resource.header', [
    'title' => 'Clients',
    'subtitle' => 'Everyone you have won work from, with their projects and billing.',
    'total' => $page->total(),
    'exportType' => 'clients',
    'addLabel' => '+ Add Client',
])

@include('admin.resource.form', [
    'formTitle' => 'Client',
    'storeUrl' => route('admin.clients.store'),
    'updateUrl' => $editing ? route('admin.clients.update', $editing->id) : null,
])

@include('admin.resource.toolbar', [
    'searchPlaceholder' => 'Search by name, company, email, phone or GST...',
    'filters' => [['name' => 'status', 'label' => 'Status', 'options' => Meta::options(Meta::ACTIVE_STATUS)]],
])

@if ($page->isEmpty())
    <div class="admin-empty-state">{{ $filtered ? 'Nothing matches these filters.' : 'Nothing here yet.' }}</div>
@else
    <div class="admin-table-wrap">
        <table class="admin-table resource-table">
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Projects</th>
                    <th>Invoiced</th>
                    <th>Received</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($page as $c)
                    <tr class="admin-table-row" data-row-href="{{ route('admin.clients.show', $c->id) }}">
                        <td>
                            <div class="resource-primary">
                                <strong>{{ $c->company ?: $c->name }}</strong>
                                @if ($c->company)<span>{{ $c->name }}</span>@endif
                            </div>
                        </td>
                        <td>{{ $c->email ?: '—' }}</td>
                        <td>{{ $c->phone ?: '—' }}</td>
                        <td>{{ $c->projects_count }}</td>
                        <td>{{ Meta::money($c->total_invoiced ?? 0) }}</td>
                        <td>{{ Meta::money($c->total_paid ?? 0) }}</td>
                        <td><x-badge :meta="Meta::ACTIVE_STATUS" :value="$c->status" /></td>
                        @include('admin.resource.actions', ['row' => $c, 'canDelete' => false])
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @include('admin.resource.pagination')
@endif
@endsection
