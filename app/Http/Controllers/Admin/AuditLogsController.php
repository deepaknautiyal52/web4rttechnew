<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuditLogsController extends Controller
{
    public function index(Request $request)
    {
        $state = ResourcePageController::takeState($request);
        $page = app(AuditLogController::class)->index($request);

        return view('admin.audit-log', [
            'page' => $page,
            'openId' => $state['open'] ?? null,
            'listUrl' => ResourcePageController::listUrl($request),
            'filtered' => collect($request->query())->except('page')->filter(fn ($v) => $v !== null && $v !== '')->isNotEmpty(),
        ]);
    }
}
