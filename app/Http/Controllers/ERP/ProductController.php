<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index()
    {
        return Inertia::render('ERP/Master/Products/Index', [
            'products' => Product::latest()->paginate(10)
        ]);
    }

    public function create()
    {
        return Inertia::render('ERP/Master/Products/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|unique:products',
            'selling_price' => 'required|numeric|min:0',
        ]);

        Product::create($request->all());

        return redirect()->route('master.products.index')->with('message', 'Product created.');
    }

    public function edit(Product $product)
    {
        return Inertia::render('ERP/Master/Products/Edit', ['product' => $product]);
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|unique:products,sku,' . $product->id,
            'selling_price' => 'required|numeric|min:0',
        ]);

        $product->update($request->all());

        return redirect()->route('master.products.index')->with('message', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('master.products.index')->with('message', 'Product deleted.');
    }
}
