@extends('layouts.admin', ['title' => 'Users & Roles'])
@php use App\Support\Meta; @endphp

@section('content')
@include('admin.resource.header', [
    'title' => 'Users & Roles',
    'subtitle' => 'Who can log in to this admin panel, and what each person can see.',
    'total' => $page->total(),
    'addLabel' => '+ Add User',
])

@include('admin.resource.form', [
    'formTitle' => 'User',
    'storeUrl' => route('admin.users.store'),
    'updateUrl' => $editing ? route('admin.users.update', $editing->id) : null,
])

<div class="admin-panel role-legend">
    @foreach (Meta::ROLE_META as $role => $meta)
        <div>
            <x-badge :meta="Meta::ROLE_META" :value="$role" /> <span>{{ Meta::areasFor($role) }}</span>
        </div>
    @endforeach
</div>

@include('admin.resource.toolbar', [
    'searchPlaceholder' => 'Search by name or email...',
    'filters' => [['name' => 'role', 'label' => 'Role', 'options' => Meta::options(Meta::ROLE_META)]],
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
                    <th>Role</th>
                    <th>Can access</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($page as $u)
                    <tr>
                        <td><strong>{{ $u->name }}{{ auth()->id() === $u->id ? ' (you)' : '' }}</strong></td>
                        <td>{{ $u->email }}</td>
                        <td><x-badge :meta="Meta::ROLE_META" :value="$u->role" /></td>
                        <td><span class="resource-muted">{{ Meta::areasFor($u->role) }}</span></td>
                        @include('admin.resource.actions', ['row' => $u, 'deleteUrl' => route('admin.users.destroy', $u->id), 'deleteLabel' => $u->name])
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @include('admin.resource.pagination')
@endif
@endsection
