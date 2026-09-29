<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ClientController;
use App\Support\Meta;
use Illuminate\Http\Request;

class ClientsController extends ResourcePageController
{
    protected string $api = ClientController::class;

    protected string $view = 'admin.clients.index';

    public static function fields(): array
    {
        return [
            ['name' => 'name', 'label' => 'Contact person', 'required' => true, 'placeholder' => 'e.g. Priya Mehta'],
            ['name' => 'company', 'label' => 'Company', 'placeholder' => 'e.g. Acme Pvt Ltd'],
            ['name' => 'email', 'label' => 'Email', 'type' => 'email'],
            ['name' => 'phone', 'label' => 'Phone'],
            ['name' => 'website', 'label' => 'Website', 'placeholder' => 'https://'],
            ['name' => 'gst_number', 'label' => 'GST number', 'placeholder' => '22AAAAA0000A1Z5'],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => Meta::options(Meta::ACTIVE_STATUS)],
            ['name' => 'billing_address', 'label' => 'Billing address', 'type' => 'textarea', 'full' => true],
            ['name' => 'notes', 'label' => 'Notes', 'type' => 'textarea', 'full' => true],
        ];
    }

    protected function emptyForm(): array
    {
        return ['name' => '', 'company' => '', 'email' => '', 'phone' => '', 'website' => '', 'gst_number' => '', 'status' => 'active', 'billing_address' => '', 'notes' => ''];
    }

    protected function extra(Request $request, array $lookups): array
    {
        return ['fields' => self::fields()];
    }

    /** The client page: profile plus everything linked to the client. */
    public function show($id)
    {
        return view('admin.clients.show', ['client' => $this->api()->show($id)]);
    }

    public function destroy(Request $request, $id)
    {
        $this->api()->destroy($id);

        return redirect()->route('admin.clients.index');
    }
}
