<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DashboardController;
use App\Models\Contact;
use Illuminate\Http\Request;

class OverviewController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        // "Needs attention" data: only the sections this role can access.
        $attention = app(DashboardController::class)($request)->getData(true)['data'];

        $contacts = null;
        if ($user->canAccess('leads')) {
            $contacts = Contact::query()
                ->select(['id', 'name', 'subject', 'service_interested', 'budget_range', 'status', 'deal_value', 'won_at', 'lost_reason', 'created_at'])
                ->orderBy('created_at', 'asc')
                ->get();
        }

        return view('admin.overview', [
            'attention' => $attention,
            'contacts' => $contacts,
        ]);
    }
}
