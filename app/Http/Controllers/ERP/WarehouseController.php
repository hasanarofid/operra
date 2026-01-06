<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WarehouseController extends Controller
{
    public function index()
    {
        return Inertia::render('ERP/Master/Warehouses/Index', [
            'warehouses' => Warehouse::latest()->paginate(10)
        ]);
    }

    public function create()
    {
        return Inertia::render('ERP/Master/Warehouses/Create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        Warehouse::create($request->all());
        return redirect()->route('master.warehouses.index')->with('message', 'Warehouse created.');
    }
}
