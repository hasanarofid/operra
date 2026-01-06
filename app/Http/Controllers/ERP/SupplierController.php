<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SupplierController extends Controller
{
    public function index()
    {
        return Inertia::render('ERP/Master/Suppliers/Index', [
            'suppliers' => Supplier::latest()->paginate(10)
        ]);
    }

    public function create()
    {
        return Inertia::render('ERP/Master/Suppliers/Create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        Supplier::create($request->all());
        return redirect()->route('master.suppliers.index')->with('message', 'Supplier created.');
    }
}
