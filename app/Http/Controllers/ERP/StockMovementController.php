<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\StockMovement;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StockMovementController extends Controller
{
    public function index()
    {
        return Inertia::render('ERP/Stock/Movements/Index', [
            'movements' => StockMovement::with(['product', 'warehouse'])->latest()->paginate(10)
        ]);
    }

    public function create()
    {
        return Inertia::render('ERP/Stock/Movements/Create', [
            'products' => Product::all(),
            'warehouses' => Warehouse::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'quantity' => 'required|integer',
            'type' => 'required|in:in,out,mutation',
        ]);

        StockMovement::create($request->all());

        return redirect()->route('stock.movements.index')->with('message', 'Stock movement recorded.');
    }
}
