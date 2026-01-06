<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Inventory;
use App\Models\Approval;
use App\Models\StaffActivity;
use App\Models\Subscription;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        // 1. KPI Summary
        $stats = [
            'today_orders' => Order::whereDate('created_at', $today)->count(),
            'monthly_revenue' => Order::where('status', 'completed')
                ->where('created_at', '>=', $startOfMonth)
                ->sum('total_amount'),
            'low_stock_alerts' => Inventory::where('stock_quantity', '<=', 10)->count(),
            'pending_approvals' => Approval::where('status', 'pending')->count(),
        ];

        // 2. Operational Timeline
        $recentOrders = Order::latest()->limit(5)->get();
        $recentActivities = StaffActivity::with('user')->latest()->limit(5)->get();

        // 3. Performance Analytics (7 Days Trend)
        $chartData = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count'),
                DB::raw('sum(total_amount) as revenue')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // 4. Action Center
        $urgentApprovals = Approval::with('requester')
            ->where('status', 'pending')
            ->latest()
            ->limit(3)
            ->get();
            
        $criticalInventory = Inventory::where('stock_quantity', '<=', 5)
            ->orderBy('stock_quantity', 'asc')
            ->limit(3)
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'recentActivities' => $recentActivities,
            'chartData' => $chartData,
            'actionCenter' => [
                'approvals' => $urgentApprovals,
                'inventory' => $criticalInventory,
            ]
        ]);
    }
}
