@extends('layouts.admin', ['title' => 'Renewals'])
@php use App\Support\Meta; @endphp

@section('content')
@include('admin.resource.header', [
    'title' => 'Renewals',
    'subtitle' => 'Domains, hosting, SSL certificates and AMC contracts, sorted by what expires first.',
    'total' => $page->total(),
    'exportType' => 'renewals',
    'addLabel' => '+ Add Renewal',
])

@include('admin.resource.form', [
    'formTitle' => 'Renewal',
    'storeUrl' => route('admin.renewals.store'),
    'updateUrl' => $editing ? route('admin.renewals.update', $editing->id) : null,
])

@include('admin.resource.toolbar', [
    'searchPlaceholder' => 'Search by name or provider...',
    'filters' => [
        ['name' => 'type', 'label' => 'Type', 'options' => Meta::options(Meta::RENEWAL_TYPE)],
        ['name' => 'due_within', 'label' => 'Expiring', 'options' => ['7' => 'Within 7 days', '30' => 'Within 30 days', '90' => 'Within 90 days']],
    ],
])

@if ($page->isEmpty())
    <div class="admin-empty-state">{{ $filtered ? 'Nothing matches these filters.' : 'Nothing here yet.' }}</div>
@else
    <div class="admin-table-wrap">
        <table class="admin-table resource-table">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Name</th>
                    <th>Client</th>
                    <th>Expires</th>
                    <th>Left</th>
                    <th>Cycle</th>
                    <th>Cost</th>
                    <th>Charged</th>
                    <th>Auto</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($page as $r)
                    @php
                        $renew = '<form method="POST" action="'.e(route('admin.renewals.renew', $r->id)).'" style="display:inline" data-confirm="'.e('Mark '.$r->name.' as renewed for another '.str_replace('ly', '', $r->billing_cycle).'?').'">'
                            .csrf_field().'<input type="hidden" name="_back" value="'.e($listUrl).'" />'
                            .'<button type="submit" title="Mark as renewed (extend by one billing cycle)">'.\App\Support\AdminNav::icon('refresh').'</button></form>';
                    @endphp
                    <tr>
                        <td><x-badge :meta="Meta::RENEWAL_TYPE" :value="$r->type" /></td>
                        <td>
                            <div class="resource-primary">
                                <strong>{{ $r->name }}</strong>
                                @if ($r->provider)<span>{{ $r->provider }}</span>@endif
                            </div>
                        </td>
                        <td>
                            @if ($r->client)
                                <a href="{{ route('admin.clients.show', $r->client_id) }}">{{ Meta::clientLabel($r->client) }}</a>
                            @else
                                <span class="resource-muted">Web4rtTech</span>
                            @endif
                        </td>
                        <td>{{ Meta::date($r->expiry_date) }}</td>
                        <td>@include('admin.partials.days-left', ['days' => $r->days_left])</td>
                        <td>{{ Meta::BILLING_CYCLES[$r->billing_cycle] ?? '' }}</td>
                        <td>{{ (float) $r->cost ? Meta::money($r->cost, $r->currency) : '—' }}</td>
                        <td>{{ (float) $r->price ? Meta::money($r->price, $r->currency) : '—' }}</td>
                        <td>{{ $r->auto_renew ? '✓' : '—' }}</td>
                        @include('admin.resource.actions', ['row' => $r, 'deleteUrl' => route('admin.renewals.destroy', $r->id), 'deleteLabel' => $r->name, 'before' => $renew])
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @include('admin.resource.pagination')
@endif
@endsection
