<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\RenewalController;
use App\Support\Lookups;
use App\Support\Meta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class RenewalsController extends ResourcePageController
{
    protected string $api = RenewalController::class;

    protected string $view = 'admin.renewals';

    protected bool $needsLookups = true;

    protected function emptyForm(): array
    {
        return ['type' => 'domain', 'name' => '', 'client_id' => '', 'provider' => '', 'expiry_date' => '', 'billing_cycle' => 'yearly', 'cost' => '', 'price' => '', 'currency' => 'INR', 'auto_renew' => false, 'notes' => ''];
    }

    protected function toForm(Model $r): array
    {
        return array_merge(parent::toForm($r), [
            'client_id' => (string) ($r->client_id ?? ''),
            'auto_renew' => (bool) $r->auto_renew,
        ]);
    }

    protected function extra(Request $request, array $l): array
    {
        return ['fields' => [
            ['name' => 'type', 'label' => 'Type', 'type' => 'select', 'options' => Meta::options(Meta::RENEWAL_TYPE)],
            ['name' => 'name', 'label' => 'Name', 'required' => true, 'placeholder' => 'e.g. acme.com, VPS plan, AMC 2026'],
            ['name' => 'client_id', 'label' => 'Client', 'type' => 'select', 'placeholder' => 'Web4rtTech (our own)', 'options' => Lookups::clientOptions($l)],
            ['name' => 'provider', 'label' => 'Provider', 'placeholder' => 'e.g. GoDaddy, Hostinger, AWS'],
            ['name' => 'expiry_date', 'label' => 'Expiry date', 'type' => 'date', 'required' => true],
            ['name' => 'billing_cycle', 'label' => 'Billing cycle', 'type' => 'select', 'options' => Meta::BILLING_CYCLES],
            ['name' => 'cost', 'label' => 'Our cost', 'type' => 'number'],
            ['name' => 'price', 'label' => 'Price charged to client', 'type' => 'number'],
            ['name' => 'currency', 'label' => 'Currency', 'type' => 'select', 'options' => array_combine(Meta::CURRENCIES, Meta::CURRENCIES)],
            ['name' => 'auto_renew', 'label' => 'Auto-renew', 'type' => 'checkbox', 'checkboxLabel' => 'Auto-renews with the provider'],
            ['name' => 'notes', 'label' => 'Notes', 'type' => 'textarea', 'full' => true],
        ]];
    }

    /** Mark as renewed: push the expiry date forward by one billing cycle. */
    public function renew(Request $request, $id)
    {
        return $this->done($request, $this->api()->renew($id));
    }
}
