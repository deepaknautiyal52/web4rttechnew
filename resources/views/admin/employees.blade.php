@extends('layouts.admin', ['title' => 'Team'])
@php use App\Support\Meta; @endphp

@section('content')
@include('admin.resource.header', [
    'title' => 'Team',
    'subtitle' => 'Employees, their cost, and who can be assigned to projects, timesheets and tickets.',
    'total' => $page->total(),
    'addLabel' => '+ Add Employee',
])

@include('admin.resource.form', [
    'formTitle' => 'Employee',
    'storeUrl' => route('admin.team.store'),
    'updateUrl' => $editing ? route('admin.team.update', $editing->id) : null,
])

@include('admin.resource.toolbar', [
    'searchPlaceholder' => 'Search by name, email or designation...',
    'filters' => [['name' => 'status', 'label' => 'Status', 'options' => Meta::options(Meta::ACTIVE_STATUS)]],
])

@if ($page->isEmpty())
    <div class="admin-empty-state">{{ $filtered ? 'Nothing matches these filters.' : 'Nothing here yet.' }}</div>
@else
    <div class="admin-table-wrap">
        <table class="admin-table resource-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Salary / month</th>
                    <th>Hourly cost</th>
                    <th>Joined</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($page as $e)
                    <tr>
                        <td>
                            <div class="resource-primary">
                                <strong>{{ $e->name }}</strong>
                                @if ($e->designation)<span>{{ $e->designation }}</span>@endif
                            </div>
                        </td>
                        <td>{{ $e->email ?: '—' }}</td>
                        <td>{{ $e->phone ?: '—' }}</td>
                        <td>{{ (float) $e->monthly_salary ? Meta::money($e->monthly_salary, $e->currency) : '—' }}</td>
                        <td>{{ (float) $e->hourly_cost ? Meta::money($e->hourly_cost, $e->currency) : '—' }}</td>
                        <td>{{ Meta::date($e->joined_on) }}</td>
                        <td><x-badge :meta="Meta::ACTIVE_STATUS" :value="$e->status" /></td>
                        @include('admin.resource.actions', ['row' => $e, 'deleteUrl' => route('admin.team.destroy', $e->id), 'deleteLabel' => $e->name])
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @include('admin.resource.pagination')
@endif
@endsection
