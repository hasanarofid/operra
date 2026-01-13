<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerController extends Controller
{
    public function index()
    {
        return Inertia::render('ERP/Master/Customers/Index', [
            'customers' => Customer::latest()->paginate(10)
        ]);
    }

    public function create()
    {
        return Inertia::render('ERP/Master/Customers/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
        ]);

        Customer::create($request->all());

        // Dynamic redirect based on current route group
        $routeName = $request->route()->getName();
        if (str_contains($routeName, 'crm.wa')) {
            return redirect()->route('crm.wa.leads.index')->with('message', 'Lead created.');
        }

        return redirect()->route('crm.sales.customers.index')->with('message', 'Customer created.');
    }

    public function updateStatus(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'status' => 'required|in:lead,prospect,customer,lost',
        ]);

        $customer->update(['status' => $validated['status']]);

        return response()->json([
            'message' => 'Status customer berhasil diperbarui',
            'customer' => $customer
        ]);
    }
}
