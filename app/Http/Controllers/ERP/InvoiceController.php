<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    public function index()
    {
        return Inertia::render('ERP/Sales/Invoices/Index', [
            'invoices' => Invoice::with('salesOrder.customer')->latest()->paginate(10)
        ]);
    }

    public function downloadPdf(Invoice $invoice)
    {
        $invoice->load('salesOrder.customer');
        $pdf = Pdf::loadView('pdf.invoice', ['invoice' => $invoice]);
        return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    }
}
