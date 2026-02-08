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
            'customers' => $query->paginate(10),
            'canCreate' => !$user->hasRole('sales')
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
            return redirect()->route('crm.wa.leads.index')->with('success', 'Lead created successfully.');
        }

        return redirect()->route('crm.sales.customers.index')->with('success', 'Customer created successfully.');
    }

    public function edit(Request $request, Customer $customer)
    {
        $isWa = str_contains($request->route()->getName(), 'crm.wa');
        $user = $request->user();

        // Sales cannot edit leads not assigned to them (optional strict check, though UI hides it)
        if ($user->hasRole('sales') && $customer->assigned_to !== $user->id) {
             abort(403, 'Unauthorized');
        }

        $salesAgents = \App\Models\User::role('sales')->where('company_id', $user->company_id)->get();

        return Inertia::render('ERP/Master/Customers/Edit', [
            'customer' => $customer,
            'submitRoute' => $isWa ? 'crm.wa.leads.update' : 'crm.sales.customers.update',
            'cancelRoute' => $isWa ? 'crm.wa.leads.index' : 'crm.sales.customers.index',
            'pageTitle' => $isWa ? 'Edit Lead' : 'Edit Customer',
            'salesAgents' => $salesAgents
        ]);
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'status' => 'required|string',
            'lead_source' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $customer->update($validated);

        // Check if request is JSON (e.g. status toggle) or Inertia
        if ($request->wantsJson() && !$request->header('X-Inertia')) {
             return response()->json([
                'message' => 'Kontak berhasil diperbarui',
                'customer' => $customer
            ]);
        }

        $routeName = $request->route()->getName();
        if (str_contains($routeName, 'crm.wa')) {
            return redirect()->route('crm.wa.leads.index')->with('success', 'Lead updated successfully.');
        }

        return redirect()->route('crm.sales.customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(Request $request, Customer $customer)
    {
        // 1. Business Logic: Cannot delete if chat history exists
        if ($customer->chatSessions()->exists()) {
            return back()->with('error', 'Cannot delete lead with existing chat history. Mark as Lost instead.');
        }

        // 2. Perform delete
        $customer->delete();

        return back()->with('success', 'Lead deleted successfully.');
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
