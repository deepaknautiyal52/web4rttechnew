<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\QuotationController;
use App\Support\Lookups;
use App\Support\Meta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class QuotationsController extends ResourcePageController
{
    protected string $api = QuotationController::class;

    protected string $view = 'admin.quotations';

    protected bool $needsLookups = true;

    protected function emptyForm(): array
    {
        return [
            'title' => '',
            'contact_id' => '',
            'client_id' => '',
            'status' => 'draft',
            'currency' => 'INR',
            'issue_date' => today()->toDateString(),
            'valid_until' => today()->addDays(30)->toDateString(),
            'tax_rate' => 18,
            'items' => [['description' => '', 'quantity' => 1, 'unit_price' => '']],
            'notes' => '',
        ];
    }

    protected function toForm(Model $q): array
    {
        return array_merge(parent::toForm($q), [
            'contact_id' => (string) ($q->contact_id ?? ''),
            'client_id' => (string) ($q->client_id ?? ''),
            'tax_rate' => (float) $q->tax_rate,
            'items' => $q->items,
        ]);
    }

    protected function extra(Request $request, array $l): array
    {
        return ['fields' => [
            ['name' => 'title', 'label' => 'Title', 'required' => true, 'full' => true, 'placeholder' => 'e.g. Business website + 1 year hosting'],
            ['name' => 'contact_id', 'label' => 'Lead', 'type' => 'select', 'placeholder' => 'None', 'options' => Lookups::contactOptions($l)],
            ['name' => 'client_id', 'label' => 'Client', 'type' => 'select', 'placeholder' => 'None (not a client yet)', 'options' => Lookups::clientOptions($l)],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => Meta::options(Meta::QUOTATION_STATUS)],
            ['name' => 'currency', 'label' => 'Currency', 'type' => 'select', 'options' => array_combine(Meta::CURRENCIES, Meta::CURRENCIES)],
            ['name' => 'issue_date', 'label' => 'Issue date', 'type' => 'date', 'required' => true],
            ['name' => 'valid_until', 'label' => 'Valid until', 'type' => 'date'],
            ['name' => 'tax_rate', 'label' => 'GST / tax %', 'type' => 'number', 'help' => '18% GST by default. Use 0 for export of services.'],
            ['name' => 'items', 'label' => 'Line items', 'type' => 'items', 'full' => true, 'required' => true],
            ['name' => 'notes', 'label' => 'Terms & notes', 'type' => 'textarea', 'full' => true, 'placeholder' => 'Payment terms, scope exclusions, timeline...'],
        ]];
    }

    /** Create a draft invoice from this quotation (marks it accepted). */
    public function toInvoice(Request $request, $id)
    {
        $response = $this->api()->toInvoice($id);

        return redirect()->route('admin.invoices.index')->with('notice', $response->getData(true)['message'] ?? 'Invoice created.');
    }
}
