<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InventoryController extends Controller
{
    public function index()
    {
        $inventory = Inventory::latest()->paginate(10);
        return Inertia::render('Inventory/Index', [
            'inventory' => $inventory
        ]);
    }

    public function create()
    {
        return Inertia::render('Inventory/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_name' => 'required|string|max:255',
            'sku' => 'required|unique:inventories',
            'stock_quantity' => 'required|integer|min:0',
            'unit_price' => 'required|numeric|min:0',
        ]);

        Inventory::create($request->all());

        return redirect()->route('inventory.index')->with('message', 'Item added to inventory.');
    }

    public function edit(Inventory $inventory)
    {
        return Inertia::render('Inventory/Edit', [
            'item' => $inventory
        ]);
    }

    public function update(Request $request, Inventory $inventory)
    {
        $request->validate([
            'item_name' => 'required|string|max:255',
            'sku' => 'required|unique:inventories,sku,' . $inventory->id,
            'stock_quantity' => 'required|integer|min:0',
            'unit_price' => 'required|numeric|min:0',
        ]);

        $inventory->update($request->all());

        return redirect()->route('inventory.index')->with('message', 'Inventory item updated.');
    }

    public function destroy(Inventory $inventory)
    {
        $inventory->delete();
        return redirect()->route('inventory.index')->with('message', 'Inventory item deleted.');
    }
}
