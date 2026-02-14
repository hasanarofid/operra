<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #eee; padding-bottom: 20px; }
        .company-name { font-size: 24px; font-weight: bold; color: #4f46e5; margin-bottom: 5px; }
        .report-title { font-size: 18px; color: #666; text-transform: uppercase; letter-spacing: 1px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #f9fafb; color: #374151; font-weight: bold; text-align: left; padding: 12px 8px; border-bottom: 1px solid #e5e7eb; }
        td { padding: 10px 8px; border-bottom: 1px solid #f3f4f6; font-size: 12px; }
        .footer { margin-top: 50px; font-size: 10px; color: #9ca3af; text-align: center; }
        .total-row { background-color: #fef2f2; font-weight: bold; }
        .text-right { text-align: right; }
        .badge { padding: 2px 6px; border-radius: 4px; font-size: 10px; text-transform: uppercase; }
        .badge-success { background-color: #d1fae5; color: #065f46; }
        .badge-warning { background-color: #fef3c7; color: #92400e; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">{{ $company->name }}</div>
        <div class="report-title">Laporan Penjualan Operra CRM</div>
        <div style="font-size: 12px; margin-top: 10px;">Dicetak pada: {{ $generatedAt }} oleh {{ $user }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No. Order</th>
                <th>Tanggal</th>
                <th>Pelanggan</th>
                <th>Status</th>
                <th class="text-right">Total Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{ $order->so_number }}</td>
                <td>{{ \Carbon\Carbon::parse($order->order_date)->format('d/m/Y') }}</td>
                <td>{{ $order->customer->name ?? 'N/A' }}</td>
                <td>
                    <span class="badge {{ $order->status === 'completed' ? 'badge-success' : 'badge-warning' }}">
                        {{ $order->status }}
                    </span>
                </td>
                <td class="text-right">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" class="text-right">TOTAL KESELURUHAN</td>
                <td class="text-right">Rp {{ number_format($totalValue, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        * Dokumen ini digenerate secara otomatis oleh Operra CRM Portal. <br>
        &copy; {{ date('Y') }} Operra.id - All Rights Reserved.
    </div>
</body>
</html>
