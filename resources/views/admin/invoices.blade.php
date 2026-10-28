@extends('layouts.admin', ['title' => 'Invoices'])
@php
    use App\Support\Meta;
    $page->load('payments');
    $paymentOpen = old('_open');
@endphp

@section('content')
@include('admin.resource.header', [
    'title' => 'Invoices',
    'subtitle' => 'Bill clients with GST, track what is paid and what is overdue.',
    'total' => $page->total(),
    'exportType' => 'invoices',
    'addLabel' => '+ New Invoice',
    'suppressErrorBanner' => (bool) $paymentOpen,
])

@include('admin.resource.form', [
    'formTitle' => 'Invoice',
    'storeUrl' => route('admin.invoices.store'),
    'updateUrl' => $editing ? route('admin.invoices.update', $editing->id) : null,
])

<div class="admin-stat-grid admin-stat-grid-2">
    @include('admin.partials.stat', ['label' => 'Outstanding receivables', 'value' => Meta::money($receivables['outstanding']), 'icon' => 'receipt', 'accent' => '#4F46E5', 'sub' => $receivables['count'].' unpaid invoice'.($receivables['count'] === 1 ? '' : 's')])
    @include('admin.partials.stat', ['label' => 'Overdue', 'value' => Meta::money($receivables['overdue']), 'tone' => $receivables['overdue'] > 0 ? 'expense-text' : null, 'icon' => 'alert', 'accent' => '#DC2626', 'sub' => $receivables['overdueCount'].' past due date'])
</div>

@include('admin.resource.toolbar', [
    'searchPlaceholder' => 'Search by invoice number or notes...',
    'filters' => [['name' => 'status', 'label' => 'Status', 'options' => ['unpaid' => 'Unpaid (sent + partial)'] + Meta::options(Meta::INVOICE_STATUS)]],
])

@if ($page->isEmpty())
    <div class="admin-empty-state">{{ $filtered ? 'Nothing matches these filters.' : 'Nothing here yet.' }}</div>
@else
    <div class="admin-table-wrap">
        <table class="admin-table resource-table">
            <thead>
                <tr>
                    <th>Number</th>
                    <th>Client</th>
                    <th>Issued</th>
                    <th>Due</th>
                    <th>Total</th>
                    <th>Balance</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($page as $i)
                    @php
                        $expanded = (string) $openId === (string) $i->id || (string) $paymentOpen === (string) $i->id;
                        $showErrors = (string) $paymentOpen === (string) $i->id;
                    @endphp
                    <tr class="admin-table-row" data-expand="invoice-{{ $i->id }}">
                        <td><strong>{{ $i->number }}</strong></td>
                        <td>
                            <div class="resource-primary">
                                <a href="{{ route('admin.clients.show', $i->client_id) }}">{{ Meta::clientLabel($i->client) }}</a>
                                @if ($i->project)<span>{{ $i->project->name }}</span>@endif
                            </div>
                        </td>
                        <td>{{ Meta::date($i->issue_date) }}</td>
                        <td>{{ Meta::date($i->due_date) }}</td>
                        <td><strong>{{ Meta::money($i->total, $i->currency) }}</strong></td>
                        <td>
                            @if ($i->status === 'cancelled')
                                —
                            @else
                                <span class="{{ $i->balance > 0 ? 'expense-cell' : 'finance-amount-income' }}">{{ Meta::money($i->balance, $i->currency) }}</span>
                            @endif
                        </td>
                        <td><x-badge :meta="Meta::INVOICE_STATUS" :value="$i->display_status" /></td>
                        @include('admin.resource.actions', [
                            'row' => $i,
                            'deleteUrl' => route('admin.invoices.destroy', $i->id),
                            'deleteLabel' => $i->number,
                            'before' => '<button type="button" title="Print / save as PDF" data-href="'.e(route('admin.print', ['invoice', $i->id])).'" data-target="_blank">'.\App\Support\AdminNav::icon('printer').'</button>',
                        ])
                    </tr>
                    <tr class="admin-table-detail-row resource-detail-row" id="invoice-{{ $i->id }}" @if (!$expanded) hidden @endif>
                        <td colspan="8">
                            <div class="payments-panel">
                                <strong>Payments</strong>
                                @if ($i->payments->isEmpty())
                                    <p class="resource-muted">No payments recorded yet.</p>
                                @else
                                    <table class="finance-table compact-table payments-table">
                                        <tbody>
                                            @foreach ($i->payments as $p)
                                                <tr>
                                                    <td>{{ Meta::date($p->paid_on) }}</td>
                                                    <td class="finance-amount-income">{{ Meta::money($p->amount, $i->currency) }}</td>
                                                    <td>{{ $p->method ?: '—' }}</td>
                                                    <td>{{ $p->reference ?: '' }}</td>
                                                    <td class="finance-row-actions">
                                                        <form method="POST" action="{{ route('admin.payments.destroy', $p->id) }}" style="display:inline" data-confirm="Remove this payment of {{ Meta::money($p->amount, $i->currency) }}? Its Finances entry is removed too.">
                                                            @csrf
                                                            @method('DELETE')
                                                            <input type="hidden" name="_back" value="{{ $listUrl }}" />
                                                            <button type="submit" title="Remove payment" aria-label="Remove payment" class="row-action-danger">@include('admin.partials.icon', ['name' => 'trash', 'class' => ''])</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif

                                @if ($i->status !== 'cancelled' && $i->balance > 0)
                                    <form method="POST" action="{{ route('admin.invoices.payments.store', $i->id) }}" class="deal-edit payment-form" data-submitting-label="Saving...">
                                        @csrf
                                        <input type="hidden" name="_back" value="{{ $listUrl }}" />
                                        <input type="hidden" name="_open" value="{{ $i->id }}" />
                                        <div class="deal-edit-field">
                                            <label>Amount</label>
                                            <input type="number" min="0" step="0.01" name="amount" value="{{ $showErrors ? old('amount') : ($i->balance > 0 ? $i->balance : '') }}" />
                                        </div>
                                        <div class="deal-edit-field">
                                            <label>Paid on</label>
                                            <input type="date" name="paid_on" value="{{ $showErrors ? old('paid_on') : today()->toDateString() }}" />
                                        </div>
                                        <div class="deal-edit-field">
                                            <label>Method</label>
                                            <select name="method">
                                                @foreach (Meta::PAYMENT_METHODS as $m)
                                                    <option @selected(($showErrors ? old('method') : 'Bank transfer') === $m)>{{ $m }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="deal-edit-field">
                                            <label>Reference</label>
                                            <input type="text" name="reference" placeholder="UTR / txn id" value="{{ $showErrors ? old('reference') : '' }}" />
                                        </div>
                                        <button type="submit" class="deal-save-btn">Record payment</button>
                                    </form>
                                @endif
                                @if ($showErrors)
                                    @foreach ($errors->all() as $error)
                                        <div class="deal-save-error">{{ $error }}</div>
                                    @endforeach
                                @endif
                                <p class="resource-field-help">Each payment is also added to Finances as income.</p>
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
