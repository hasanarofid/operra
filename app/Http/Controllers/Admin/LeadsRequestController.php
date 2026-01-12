<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeadsRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LeadsRequestController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/LeadsRequest/Index', [
            'requests' => LeadsRequest::latest()->get()
        ]);
    }

    public function updateStatus(Request $request, LeadsRequest $leadsRequest)
    {
        $request->validate([
            'status' => 'required|in:new,contacted,closed'
        ]);

        $leadsRequest->update([
            'status' => $request->status
        ]);

        return back()->with('success', 'Status permintaan diperbarui.');
    }

    public function destroy(LeadsRequest $leadsRequest)
    {
        $leadsRequest->delete();
        return back()->with('success', 'Permintaan dihapus.');
    }
}
