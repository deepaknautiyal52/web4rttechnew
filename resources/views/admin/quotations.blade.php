@extends('layouts.admin', ['title' => 'Quotations'])
@php
    use App\Support\Meta;
    $today = today()->toDateString();
@endphp

@section('content')
@include('admin.resource.header', [
    'title' => 'Quotations',
    'subtitle' => 'Proposals built from your pricing guide. Accepted quotes turn into invoices in one click.',
    'total' => $page->total(),
    'addLabel' => '+ New Quotation',
])

@include('admin.resource.form', [
    'formTitle' => 'Quotation',
    'storeUrl' => route('admin.quotations.store'),
    'updateUrl' => $editing ? route('admin.quotations.update', $editing->id) : null,
])

@include('admin.resource.toolbar', [
    'searchPlaceholder' => 'Search by number or title...',
    'filters' => [['name' => 'status', 'label' => 'Status', 'options' => Meta::options(Meta::QUOTATION_STATUS)]],
])

@if ($page->isEmpty())
    <div class="admin-empty-state">{{ $filtered ? 'Nothing matches these filters.' : 'Nothing here yet.' }}</div>
@else
    <div class="admin-table-wrap">
        <table class="admin-table resource-table">
            <thead>
                <tr>
                    <th>Number</th>
                    <th>Quotation</th>
                    <th>Issued</th>
                    <th>Valid until</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($page as $q)
                    @php
                        $expired = $q->valid_until && $q->valid_until->toDateString() < $today && in_array($q->status, ['draft', 'sent'], true);
                        $before = '<button type="button" title="Print / save as PDF" data-href="'.e(route('admin.print', ['quotation', $q->id])).'" data-target="_blank">'.\App\Support\AdminNav::icon('printer').'</button>';
                        if ($q->status !== 'rejected') {
                            $before .= '<form method="POST" action="'.e(route('admin.quotations.invoice', $q->id)).'" style="display:inline" data-confirm="'.e('Create a draft invoice from '.$q->number.'? The quotation will be marked accepted.').'">'
                                .csrf_field().'<button type="submit" title="Create invoice from this quotation">'.\App\Support\AdminNav::icon('receipt').'</button></form>';
                        }
                    @endphp
                    <tr>
                        <td><strong>{{ $q->number }}</strong></td>
                        <td>
                            <div class="resource-primary">
                                <strong>{{ $q->title }}</strong>
                                <span>{{ $q->client ? Meta::clientLabel($q->client) : ($q->contact ? 'Lead: '.$q->contact->name : '—') }}</span>
                            </div>
                        </td>
                        <td>{{ Meta::date($q->issue_date) }}</td>
                        <td><span class="{{ $expired ? 'expense-cell' : '' }}">{{ Meta::date($q->valid_until) }}{{ $expired ? ' (expired)' : '' }}</span></td>
                        <td><strong>{{ Meta::money($q->total, $q->currency) }}</strong></td>
                        <td><x-badge :meta="Meta::QUOTATION_STATUS" :value="$q->status" /></td>
                        @include('admin.resource.actions', ['row' => $q, 'deleteUrl' => route('admin.quotations.destroy', $q->id), 'deleteLabel' => $q->number, 'before' => $before])
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @include('admin.resource.pagination')
@endif
@endsection
