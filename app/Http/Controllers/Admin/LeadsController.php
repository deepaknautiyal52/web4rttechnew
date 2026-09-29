<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ContactController;
use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

/**
 * Leads (contact form submissions): list, deal status, activity log and
 * conversion to a client. Writes go through the API ContactController.
 */
class LeadsController extends Controller
{
    public function index(Request $request)
    {
        $state = ResourcePageController::takeState($request);

        // The "view" dropdown maps onto the API's follow_up / stale filters.
        $view = (string) $request->query('view', '');
        if ($view === 'follow_up') {
            $request->query->set('follow_up', 'due');
        } elseif ($view === 'stale') {
            $request->query->set('stale', '1');
        }

        $page = app(ContactController::class)->index($request);
        $page->withPath($request->url());
        $page->appends($request->only(['search', 'status', 'view']));

        $openId = $state['open'] ?? old('_open');
        $page->load(['activities.user:id,name', 'quotations:id,contact_id,number,status']);

        return view('admin.leads', [
            'page' => $page,
            'openId' => $openId,
            'listUrl' => ResourcePageController::listUrl($request),
            'filtered' => $request->filled('search') || $request->filled('status') || $view !== '',
        ]);
    }

    public function update(Request $request, Contact $contact)
    {
        $status = $request->input('status');
        // Only keep the fields that apply to the chosen status.
        $request->merge([
            'deal_value' => $status === 'won' ? $request->input('deal_value') : null,
            'next_follow_up_at' => in_array($status, ['won', 'lost'], true) ? null : $request->input('next_follow_up_at'),
            'lost_reason' => $status === 'lost' ? $request->input('lost_reason') : null,
        ]);

        app(ContactController::class)->update($request, $contact);

        return $this->back($request, $contact)->with('saved', $contact->id);
    }

    public function addActivity(Request $request, Contact $contact)
    {
        app(ContactController::class)->addActivity($request, $contact);

        return $this->back($request, $contact);
    }

    public function deleteActivity(Request $request, Contact $contact, $activity)
    {
        app(ContactController::class)->deleteActivity($contact, $activity);

        return $this->back($request, $contact);
    }

    public function convert(Contact $contact)
    {
        $client = ContactController::convertToClient($contact);

        return redirect()->route('admin.clients.show', $client->id);
    }

    private function back(Request $request, Contact $contact)
    {
        $back = (string) $request->input('_back', route('admin.leads.index'));
        if (! str_starts_with($back, url('/admin'))) {
            $back = route('admin.leads.index');
        }

        return redirect()->to($back.(str_contains($back, '?') ? '&' : '?').'open='.$contact->id);
    }
}
