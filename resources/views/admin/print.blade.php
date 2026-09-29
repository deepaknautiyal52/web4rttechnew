@php
    use App\Support\Meta;
    $company = Meta::COMPANY;
    $bank = $company['bank'];
    $hasBank = collect($bank)->filter()->isNotEmpty();
    $party = $doc->client ?? ($doc->contact ?? null);
    $status = $isInvoice ? (Meta::INVOICE_STATUS[$doc->display_status] ?? null) : (Meta::QUOTATION_STATUS[$doc->status] ?? null);
    $taxed = (float) $doc->tax_rate > 0;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="robots" content="noindex, nofollow" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <title>{{ $doc->number }} - {{ $company['name'] }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}" />
</head>
<body>
<div id="root">
    <div class="App">
        <div class="print-page">
            <div class="print-toolbar">
                <button type="button" onclick="window.print()">🖨️ Print / Save as PDF</button>
                <button type="button" onclick="window.close()">Close</button>
            </div>

            <div class="print-doc">
                <header class="print-header">
                    <div>
                        <div class="print-brand">Web<span>4</span>rtTech</div>
                        <div class="print-muted">{{ $company['tagline'] }}</div>
                        @if ($company['address'])
                            <div class="print-muted print-pre">{{ $company['address'] }}</div>
                        @endif
                        <div class="print-muted">{{ $company['email'] }} · {{ $company['phone'] }} · {{ $company['website'] }}</div>
                        @if ($company['gstin'])
                            <div class="print-muted">GSTIN: {{ $company['gstin'] }}</div>
                        @endif
                    </div>
                    <div class="print-title">
                        <h1>{{ $isInvoice ? ($taxed ? 'TAX INVOICE' : 'INVOICE') : 'QUOTATION' }}</h1>
                        <div class="print-number">{{ $doc->number }}</div>
                        @if ($status)
                            <div class="print-status" style="color: {{ $status['color'] }}; border-color: {{ $status['color'] }}">{{ $status['label'] }}</div>
                        @endif
                    </div>
                </header>

                <section class="print-meta">
                    <div>
                        <div class="print-label">{{ $isInvoice ? 'Bill to' : 'Prepared for' }}</div>
                        @if ($party)
                            <strong>{{ $party->company ?: $party->name }}</strong>
                            @if ($party->company)<div>{{ $party->name }}</div>@endif
                            @if (!empty($party->billing_address))<div class="print-pre">{{ $party->billing_address }}</div>@endif
                            @if ($party->email)<div>{{ $party->email }}</div>@endif
                            @if ($party->phone)<div>{{ $party->phone }}</div>@endif
                            @if (!empty($party->gst_number))<div>GSTIN: {{ $party->gst_number }}</div>@endif
                        @else
                            <div>—</div>
                        @endif
                    </div>
                    <div class="print-dates">
                        <div><span class="print-label">Date</span> {{ Meta::date($doc->issue_date) }}</div>
                        @if ($isInvoice && $doc->due_date)
                            <div><span class="print-label">Due</span> {{ Meta::date($doc->due_date) }}</div>
                        @endif
                        @if (!$isInvoice && $doc->valid_until)
                            <div><span class="print-label">Valid until</span> {{ Meta::date($doc->valid_until) }}</div>
                        @endif
                        @if ($isInvoice && $doc->project)
                            <div><span class="print-label">Project</span> {{ $doc->project->name }}</div>
                        @endif
                        @if (!$isInvoice)
                            <div><span class="print-label">Subject</span> {{ $doc->title }}</div>
                        @endif
                    </div>
                </section>

                <table class="print-items">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Description</th>
                            <th class="num">Qty</th>
                            <th class="num">Rate</th>
                            <th class="num">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($doc->items as $i => $item)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $item['description'] }}</td>
                                <td class="num">{{ Meta::num($item['quantity']) }}</td>
                                <td class="num">{{ Meta::money2($item['unit_price'], $doc->currency) }}</td>
                                <td class="num">{{ Meta::money2($item['quantity'] * $item['unit_price'], $doc->currency) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="print-totals">
                    <div><span>Subtotal</span><span>{{ Meta::money2($doc->subtotal, $doc->currency) }}</span></div>
                    @if ($taxed)
                        <div><span>GST @ {{ Meta::num($doc->tax_rate) }}%</span><span>{{ Meta::money2($doc->tax_amount, $doc->currency) }}</span></div>
                    @endif
                    <div class="print-grand"><span>Total</span><span>{{ Meta::money2($doc->total, $doc->currency) }}</span></div>
                    @if ($isInvoice && (float) $doc->amount_paid > 0)
                        <div><span>Paid</span><span>-{{ Meta::money2($doc->amount_paid, $doc->currency) }}</span></div>
                        <div class="print-grand"><span>Balance due</span><span>{{ Meta::money2($doc->balance, $doc->currency) }}</span></div>
                    @endif
                </div>

                @if ($doc->notes)
                    <section class="print-notes">
                        <div class="print-label">{{ $isInvoice ? 'Notes' : 'Terms & notes' }}</div>
                        <p class="print-pre">{{ $doc->notes }}</p>
                    </section>
                @endif

                @if ($isInvoice && $hasBank)
                    <section class="print-notes">
                        <div class="print-label">Payment details</div>
                        @if ($bank['accountName'])<div>Account name: {{ $bank['accountName'] }}</div>@endif
                        @if ($bank['accountNumber'])<div>Account no.: {{ $bank['accountNumber'] }}</div>@endif
                        @if ($bank['ifsc'])<div>IFSC: {{ $bank['ifsc'] }}</div>@endif
                        @if ($bank['bankName'])<div>Bank: {{ $bank['bankName'] }}</div>@endif
                        @if ($bank['upi'])<div>UPI: {{ $bank['upi'] }}</div>@endif
                    </section>
                @endif

                <footer class="print-footer">Thank you for your business. · {{ $company['website'] }}</footer>
            </div>
        </div>
    </div>
</div>
</body>
</html>
