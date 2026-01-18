<?php

namespace App\Http\Controllers;

use App\Models\CustomerStatus;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerStatusController extends Controller
{
    public function index()
    {
        return Inertia::render('Settings/CustomerStatuses', [
            'statuses' => CustomerStatus::orderBy('order')->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'color' => 'required|string',
            'order' => 'required|integer',
        ]);

        CustomerStatus::create($validated);

        return redirect()->back()->with('message', 'Status created successfully.');
    }

    public function update(Request $request, CustomerStatus $customerStatus)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'color' => 'required|string',
            'order' => 'required|integer',
        ]);

        $customerStatus->update($validated);

        return redirect()->back()->with('message', 'Status updated successfully.');
    }

    public function destroy(CustomerStatus $customerStatus)
    {
        $customerStatus->delete();
        return redirect()->back()->with('message', 'Status deleted successfully.');
    }
}

