<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebhookLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WebhookMonitorController extends Controller
{
    public function index()
    {
        // Must be system owner / super admin
        if (!auth()->user()->company->is_system_owner) {
            abort(403);
        }

        $logs = WebhookLog::latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/System/WebhookMonitor', [
            'logs' => $logs,
        ]);
    }
}
