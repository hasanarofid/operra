<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Customer::with('assignedSales')->latest();

        if ($user->hasRole('sales')) {
            $query->where('assigned_to', $user->id);
        }

        return Inertia::render('ERP/Master/Customers/Index', [
            'customers' => $query->paginate(10)
        ]);
    }

    public function create(Request $request)
    {
        $isWa = str_contains($request->route()->getName(), 'crm.wa');
        $user = $request->user();

        // Sales cannot create leads
        if ($user->hasRole('sales')) {
            abort(403, 'Sales users are not allowed to create leads directly.');
        }

        // Fetch eligible sales agents for assignment
        $salesAgents = \App\Models\User::role('sales')->where('company_id', $user->company_id)->get();

        return Inertia::render('ERP/Master/Customers/Create', [
            'submitRoute' => $isWa ? 'crm.wa.leads.store' : 'crm.sales.customers.store',
            'cancelRoute' => $isWa ? 'crm.wa.leads.index' : 'crm.sales.customers.index',
            'pageTitle' => $isWa ? 'Add New Lead' : 'Add New Customer',
            'salesAgents' => $salesAgents
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        Customer::create($request->all());

        // Dynamic redirect based on current route group
        $routeName = $request->route()->getName();
        if (str_contains($routeName, 'crm.wa')) {
            return redirect()->route('crm.wa.leads.index')->with('message', 'Lead created.');
        }

        return redirect()->route('crm.sales.customers.index')->with('message', 'Customer created.');
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $customer->update($validated);

        return response()->json([
            'message' => 'Kontak berhasil diperbarui',
            'customer' => $customer
        ]);
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
