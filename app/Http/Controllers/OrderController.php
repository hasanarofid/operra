<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::latest()->paginate(10);
        return Inertia::render('Orders/Index', [
            'orders' => $orders
        ]);
    }

    public function create()
    {
        return Inertia::render('Orders/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_number' => 'required|unique:orders',
            'customer_name' => 'required|string|max:255',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|in:pending,processing,completed,cancelled',
        ]);

        Order::create($request->all());

        return redirect()->route('crm.sales.orders.index')->with('message', 'Order created successfully.');
    }

    public function edit(Order $order)
    {
        return Inertia::render('Orders/Edit', [
            'order' => $order
        ]);
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'order_number' => 'required|unique:orders,order_number,' . $order->id,
            'customer_name' => 'required|string|max:255',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|in:pending,processing,completed,cancelled',
        ]);

        $order->update($request->all());

        return redirect()->route('crm.sales.orders.index')->with('message', 'Order updated successfully.');
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('crm.sales.orders.index')->with('message', 'Order deleted successfully.');
    }
}
