<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\PipelineStage;
use App\Models\SalesOrder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class PipelineController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;

        // Fetch stages with their orders
        $stages = PipelineStage::where('company_id', $companyId)
            ->orderBy('position')
            ->get()
            ->map(function ($stage) use ($companyId) {
                $stage->orders = SalesOrder::where('company_id', $companyId)
                    ->where('stage_id', $stage->id)
                    ->with('customer') // Eager load customer for card display
                    ->orderBy('created_at', 'desc')
                    ->get();
                return $stage;
            });

        // Calculate Total Pipeline Value
        $totalValue = SalesOrder::where('company_id', $companyId)
            ->whereNotNull('stage_id')
            ->sum('total_amount');

        return Inertia::render('ERP/Sales/Pipeline/Index', [
            'stages' => $stages,
            'totalValue' => $totalValue
        ]);
    }

    public function updateOrderStage(Request $request, SalesOrder $order)
    {
        $request->validate([
            'stage_id' => 'required|exists:pipeline_stages,id'
        ]);

        // Security check
        if ($order->company_id !== $request->user()->company_id) {
            abort(403);
        }

        $order->update(['stage_id' => $request->stage_id]);

        return response()->json(['message' => 'Deal moved successfully']);
    }

    public function storeStage(Request $request)
    {
        // To IMPLEMENT: Add new custom stage
    }
}
