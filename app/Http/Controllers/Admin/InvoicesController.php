<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\InvoiceController;
use App\Models\Invoice;
use App\Models\Payment;
use App\Support\Lookups;
use App\Support\Meta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class InvoicesController extends ResourcePageController
{
    protected string $api = InvoiceController::class;

    protected string $view = 'admin.invoices';

    protected bool $needsLookups = true;

    protected function emptyForm(): array
    {
        return [
            'client_id' => '',
            'project_id' => '',
            'status' => 'draft',
            'currency' => 'INR',
            'issue_date' => today()->toDateString(),
            'due_date' => today()->addDays(15)->toDateString(),
            'tax_rate' => 18,
            'items' => [['description' => '', 'quantity' => 1, 'unit_price' => '']],
            'notes' => '',
        ];
    }

    protected function toForm(Model $i): array
    {
        return array_merge(parent::toForm($i), [
            'client_id' => (string) $i->client_id,
            'project_id' => (string) ($i->project_id ?? ''),
            // Keep paid/partial invoices as "sent" in the form; the backend re-derives them.
            'status' => in_array($i->status, ['paid', 'partially_paid'], true) ? 'sent' : $i->status,
            'tax_rate' => (float) $i->tax_rate,
            'items' => $i->items,
        ]);
    }

    protected function extra(Request $request, array $l): array
    {
        $unpaid = Invoice::whereIn('status', ['sent', 'partially_paid'])->get();
        $overdue = $unpaid->filter(fn ($i) => $i->display_status === 'overdue');

        return [
            'receivables' => [
                'outstanding' => $unpaid->sum('balance'),
                'count' => $unpaid->count(),
                'overdue' => $overdue->sum('balance'),
                'overdueCount' => $overdue->count(),
            ],
            'fields' => [
                ['name' => 'client_id', 'label' => 'Client', 'type' => 'select', 'required' => true, 'placeholder' => 'Select a client...', 'options' => Lookups::clientOptions($l)],
                ['name' => 'project_id', 'label' => 'Project', 'type' => 'select', 'placeholder' => 'None', 'options' => Lookups::projectOptions($l), 'dependsOn' => 'client_id', 'strict' => true],
                ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['draft' => 'Draft', 'sent' => 'Sent', 'cancelled' => 'Cancelled'], 'help' => 'Paid and partially paid are set automatically when you record payments.'],
                ['name' => 'currency', 'label' => 'Currency', 'type' => 'select', 'options' => array_combine(Meta::CURRENCIES, Meta::CURRENCIES)],
                ['name' => 'issue_date', 'label' => 'Issue date', 'type' => 'date', 'required' => true],
                ['name' => 'due_date', 'label' => 'Due date', 'type' => 'date'],
                ['name' => 'tax_rate', 'label' => 'GST / tax %', 'type' => 'number', 'help' => '18% GST by default. Use 0 for export of services.'],
                ['name' => 'items', 'label' => 'Line items', 'type' => 'items', 'full' => true, 'required' => true],
                ['name' => 'notes', 'label' => 'Notes / payment instructions', 'type' => 'textarea', 'full' => true, 'placeholder' => 'Bank details, UPI id, terms...'],
            ],
        ];
    }

    /** Record a payment; it is also added to Finances as income. */
    public function addPayment(Request $request, $id)
    {
        $this->api()->addPayment($request, $id);

        return $this->backToList($request, ['open' => $id])->with('notice', 'Payment recorded.');
    }

    public function deletePayment(Request $request, $paymentId)
    {
        $invoiceId = Payment::findOrFail($paymentId)->invoice_id;
        $this->api()->deletePayment($paymentId);

        return $this->backToList($request, ['open' => $invoiceId])->with('notice', 'Payment removed.');
    }
}
