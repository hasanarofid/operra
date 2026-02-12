<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index()
    {
        $routePrefix = request()->is('crm-sales/*') ? 'crm.sales.products' : 'master.products';
        $products = Product::latest()->paginate(10);
        return Inertia::render('Master/Products/Index', [
            'products' => $products,
            'route_names' => [
                'index' => "$routePrefix.index",
                'create' => "$routePrefix.create",
                'edit' => "$routePrefix.edit",
                'store' => "$routePrefix.store",
                'update' => "$routePrefix.update",
                'destroy' => "$routePrefix.destroy",
            ]
        ]);
    }

    public function create()
    {
        $routePrefix = request()->is('crm-sales/*') ? 'crm.sales.products' : 'master.products';
        return Inertia::render('Master/Products/Create', [
            'route_names' => [
                'index' => "$routePrefix.index",
                'store' => "$routePrefix.store",
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'min_stock' => 'required|integer|min:0',
        ]);

        Product::create($request->all());

        $routePrefix = request()->is('crm-sales/*') ? 'crm.sales.products' : 'master.products';
        return redirect()->route("$routePrefix.index")->with('message', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $routePrefix = request()->is('crm-sales/*') ? 'crm.sales.products' : 'master.products';
        return Inertia::render('Master/Products/Edit', [
            'product' => $product,
            'route_names' => [
                'index' => "$routePrefix.index",
                'update' => "$routePrefix.update",
            ]
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'min_stock' => 'required|integer|min:0',
        ]);

        $product->update($request->all());

        $routePrefix = request()->is('crm-sales/*') ? 'crm.sales.products' : 'master.products';
        return redirect()->route("$routePrefix.index")->with('message', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        $routePrefix = request()->is('crm-sales/*') ? 'crm.sales.products' : 'master.products';
        return redirect()->route("$routePrefix.index")->with('message', 'Product deleted successfully.');
    }
}
