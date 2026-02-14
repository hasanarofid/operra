<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\SalesOrder;
use App\Models\Order;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function salesReport(Request $request)
    {
        $companyId = $request->user()->company_id;
        
        // We use SalesOrder as it's more integrated with the Pipeline
        $orders = SalesOrder::where('company_id', $companyId)
            ->with(['customer', 'invoice'])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalValue = $orders->sum('total_amount');

        $data = [
            'company' => $request->user()->company,
            'orders' => $orders,
            'totalValue' => $totalValue,
            'generatedAt' => now()->format('d M Y H:i:s'),
            'user' => $request->user()->name
        ];

        $pdf = Pdf::loadView('reports.sales_report', $data);
        
        return $pdf->download('laporan-penjualan-' . now()->format('Ymd') . '.pdf');
    }
}
