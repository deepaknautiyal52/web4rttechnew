@extends('layouts.admin', ['title' => $client->company ?: $client->name])
@php
    use App\Support\Meta;
    $user = auth()->user();
    $invoiced = $client->invoices->where('status', '!=', 'cancelled')->sum(fn ($i) => (float) $i->total);
    $received = $client->invoices->sum(fn ($i) => (float) $i->amount_paid);
    $outstanding = $client->invoices->whereIn('status', ['sent', 'partially_paid'])->sum(fn ($i) => (float) $i->balance);
    $prefill = fn ($route) => route($route).'?create=1&prefill[client_id]='.$client->id;
@endphp

@section('content')
<a href="{{ route('admin.clients.index') }}" class="admin-panel-link">← All clients</a>
<div class="admin-page-header client-header">
    <div>
        <h1>{{ $client->company ?: $client->name }}</h1>
        <p>
            @if ($client->company){{ $client->name }} · @endif{{ $client->email ?: 'No email' }} · {{ $client->phone ?: 'No phone' }}@if ($client->gst_number) · GST {{ $client->gst_number }}@endif
        </p>
    </div>
    <div class="admin-header-actions">
        <x-badge :meta="Meta::ACTIVE_STATUS" :value="$client->status" />
        <a href="{{ route('admin.clients.index') }}?edit={{ $client->id }}" class="admin-secondary-btn">Edit client</a>
        <form method="POST" action="{{ route('admin.clients.destroy', $client->id) }}" data-confirm="Delete {{ $client->company ?: $client->name }}? Their projects and invoices are deleted too. This cannot be undone.">
            @csrf
            @method('DELETE')
            <button type="submit" class="admin-secondary-btn admin-danger-btn">Delete client</button>
        </form>
    </div>
</div>

@if ($errors->any())
    <div class="admin-dashboard-error">{{ $errors->first() }}</div>
@endif

<div class="admin-stat-grid">
    <div class="admin-stat-card">
        <span class="admin-stat-label">Projects</span>
        <span class="admin-stat-value">{{ $client->projects->count() }}</span>
    </div>
    <div class="admin-stat-card">
        <span class="admin-stat-label">Invoiced</span>
        <span class="admin-stat-value admin-stat-value-small">{{ Meta::money($invoiced) }}</span>
    </div>
    <div class="admin-stat-card">
        <span class="admin-stat-label">Received</span>
        <span class="admin-stat-value admin-stat-value-small admin-stat-value-flat income-text">{{ Meta::money($received) }}</span>
    </div>
    <div class="admin-stat-card">
        <span class="admin-stat-label">Outstanding</span>
        <span class="admin-stat-value admin-stat-value-small admin-stat-value-flat {{ $outstanding > 0 ? 'expense-text' : '' }}">{{ Meta::money($outstanding) }}</span>
    </div>
</div>

@if ($client->billing_address || $client->notes || $client->website)
    <div class="admin-panel client-section">
        @if ($client->website || $client->billing_address)
            <div class="admin-detail-meta client-profile">
                @if ($client->website)
                    <span><strong>Website:</strong> <a href="{{ $client->website }}" target="_blank" rel="noreferrer">{{ $client->website }}</a></span>
                @endif
                @if ($client->billing_address)
                    <span><strong>Billing address:</strong> {{ $client->billing_address }}</span>
                @endif
            </div>
        @endif
        @if ($client->notes)
            <p class="client-notes{{ $client->website || $client->billing_address ? '' : ' client-notes-only' }}">{{ $client->notes }}</p>
        @endif
    </div>
@endif

<div class="client-grid">
    @if ($user->canAccess('projects'))
        <div class="admin-panel client-section">
            <div class="admin-panel-header">
                <h2>Projects</h2>
                <a class="admin-panel-link" href="{{ $prefill('admin.projects.index') }}">+ New project</a>
            </div>
            @if ($client->projects->isEmpty())
                <div class="client-section-empty">No projects yet.</div>
            @else
                <table class="finance-table compact-table">
                    <tbody>
                        @foreach ($client->projects as $p)
                            <tr>
                                <td>{{ $p->name }}</td>
                                <td><x-badge :meta="Meta::PROJECT_STATUS" :value="$p->status" /></td>
                                <td>Due {{ Meta::date($p->deadline) }}</td>
                                <td>{{ Meta::money($p->revenue, $p->currency) }} received</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @endif

    @if ($user->canAccess('invoices'))
        <div class="admin-panel client-section">
            <div class="admin-panel-header">
                <h2>Invoices</h2>
                <a class="admin-panel-link" href="{{ $prefill('admin.invoices.index') }}">+ New invoice</a>
            </div>
            @if ($client->invoices->isEmpty())
                <div class="client-section-empty">No invoices yet.</div>
            @else
                <table class="finance-table compact-table">
                    <tbody>
                        @foreach ($client->invoices as $i)
                            <tr>
                                <td><a href="{{ route('admin.print', ['invoice', $i->id]) }}" target="_blank">{{ $i->number }}</a></td>
                                <td>{{ Meta::date($i->issue_date) }}</td>
                                <td><x-badge :meta="Meta::INVOICE_STATUS" :value="$i->display_status" /></td>
                                <td>{{ Meta::money($i->total, $i->currency) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @endif

    @if ($user->canAccess('renewals'))
        <div class="admin-panel client-section">
            <div class="admin-panel-header">
                <h2>Domains, hosting &amp; AMC</h2>
                <a class="admin-panel-link" href="{{ $prefill('admin.renewals.index') }}">+ Add</a>
            </div>
            @if ($client->renewals->isEmpty())
                <div class="client-section-empty">Nothing tracked yet.</div>
            @else
                <table class="finance-table compact-table">
                    <tbody>
                        @foreach ($client->renewals as $r)
                            <tr>
                                <td><x-badge :meta="Meta::RENEWAL_TYPE" :value="$r->type" /></td>
                                <td>{{ $r->name }}</td>
                                <td class="{{ $r->days_left <= 30 ? 'expense-cell' : '' }}">
                                    Expires {{ Meta::date($r->expiry_date) }} ({{ $r->days_left < 0 ? (-$r->days_left).'d ago' : $r->days_left.'d' }})
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @endif

    @if ($user->canAccess('tickets'))
        <div class="admin-panel client-section">
            <div class="admin-panel-header">
                <h2>Support tickets</h2>
                <a class="admin-panel-link" href="{{ $prefill('admin.tickets.index') }}">+ New ticket</a>
            </div>
            @if ($client->tickets->isEmpty())
                <div class="client-section-empty">No tickets.</div>
            @else
                <table class="finance-table compact-table">
                    <tbody>
                        @foreach ($client->tickets as $t)
                            <tr>
                                <td>{{ $t->title }}</td>
                                <td><x-badge :meta="Meta::TICKET_PRIORITY" :value="$t->priority" /></td>
                                <td><x-badge :meta="Meta::TICKET_STATUS" :value="$t->status" /></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @endif

    @if ($user->canAccess('quotations'))
        <div class="admin-panel client-section">
            <div class="admin-panel-header">
                <h2>Quotations</h2>
                <a class="admin-panel-link" href="{{ $prefill('admin.quotations.index') }}">+ New quotation</a>
            </div>
            @if ($client->quotations->isEmpty())
                <div class="client-section-empty">No quotations.</div>
            @else
                <table class="finance-table compact-table">
                    <tbody>
                        @foreach ($client->quotations as $q)
                            <tr>
                                <td><a href="{{ route('admin.print', ['quotation', $q->id]) }}" target="_blank">{{ $q->number }}</a></td>
                                <td>{{ $q->title }}</td>
                                <td><x-badge :meta="Meta::QUOTATION_STATUS" :value="$q->status" /></td>
                                <td>{{ Meta::money($q->total, $q->currency) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @endif

    @if ($user->canAccess('leads') && $client->contacts->isNotEmpty())
        <div class="admin-panel client-section">
            <div class="admin-panel-header">
                <h2>Original inquiries</h2>
            </div>
            <table class="finance-table compact-table">
                <tbody>
                    @foreach ($client->contacts as $c)
                        <tr>
                            <td>{{ $c->subject }}</td>
                            <td><x-badge :meta="Meta::LEAD_STATUS" :value="$c->status" /></td>
                            <td>{{ Meta::date($c->created_at) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
