<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Purchase Report — {{ date('d M Y', strtotime($from)) }} to {{ date('d M Y', strtotime($to)) }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 13px; color: #1a1a2e; }
        .page { max-width: 820px; margin: 0 auto; padding: 40px; }
        .doc-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 36px; padding-bottom: 24px; border-bottom: 3px solid #8b5cf6; }
        .company-name { font-size: 22px; font-weight: 800; }
        .company-meta { font-size: 11px; color: #6b7280; margin-top: 8px; line-height: 1.7; }
        .report-title { font-size: 26px; font-weight: 900; color: #8b5cf6; text-align: right; }
        .report-period { font-size: 11px; color: #9ca3af; text-align: right; margin-top: 4px; }
        .generated { font-size: 10px; color: #9ca3af; text-align: right; margin-top: 2px; }

        .stats-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; margin-bottom: 32px; }
        .stat-card { background: #f9fafb; border-radius: 10px; padding: 16px; border-left: 4px solid #8b5cf6; }
        .stat-label { font-size: 10px; color: #9ca3af; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; }
        .stat-value { font-size: 22px; font-weight: 800; color: #1a1a2e; margin-top: 4px; }

        .section-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #9ca3af; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        thead tr { background: #1a1a2e; color: #fff; }
        thead th { padding: 9px 12px; text-align: left; font-size: 11px; font-weight: 600; text-transform: uppercase; }
        thead th.right { text-align: right; }
        thead th.center { text-align: center; }
        tbody tr { border-bottom: 1px solid #f3f4f6; }
        tbody tr:nth-child(even) { background: #f9fafb; }
        tbody td { padding: 8px 12px; font-size: 11px; color: #374151; }
        tbody td.right { text-align: right; }
        tbody td.center { text-align: center; }
        tfoot tr { background: #f3f4f6; font-weight: 700; border-top: 2px solid #1a1a2e; }
        tfoot td { padding: 10px 12px; font-size: 12px; }
        tfoot td.right { text-align: right; color: #8b5cf6; }

        .status-pill { display: inline-flex; padding: 2px 8px; border-radius: 20px; font-size: 9px; font-weight: 700; text-transform: uppercase; }
        .s-pending   { background:#fef9c3;color:#a16207; }
        .s-approved  { background:#dcfce7;color:#15803d; }
        .s-ordered   { background:#dbeafe;color:#1d4ed8; }
        .s-received  { background:#ede9fe;color:#7c3aed; }
        .s-cancelled { background:#fee2e2;color:#dc2626; }

        .doc-footer { text-align: center; border-top: 1px solid #f3f4f6; padding-top: 16px; font-size: 10px; color: #9ca3af; margin-top: 24px; }
        .accent { color: #8b5cf6; font-weight: 700; }

        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .page { padding: 20px; }
        }
    </style>
</head>
<body>
<div class="page">

    <div class="doc-header">
        <div>
            <div class="company-name">{{ $settings['company_name'] ?? 'SolarTech ERP' }}</div>
            <div class="company-meta">
                @if(!empty($settings['company_email'])){{ $settings['company_email'] }}<br>@endif
                @if(!empty($settings['company_phone'])){{ $settings['company_phone'] }}<br>@endif
                @if(!empty($settings['gst_number']))GST: {{ $settings['gst_number'] }}@endif
            </div>
        </div>
        <div>
            <div class="report-title">Purchase Report</div>
            <div class="report-period">{{ date('d M Y', strtotime($from)) }} — {{ date('d M Y', strtotime($to)) }}</div>
            <div class="generated">Generated: {{ date('d M Y, h:i A') }}</div>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Orders</div>
            <div class="stat-value">{{ $orders->count() }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Expense</div>
            <div class="stat-value">₹{{ number_format($totalExpense, 0) }}</div>
        </div>
    </div>

    <div class="section-title">Purchase Order Breakdown</div>
    <table>
        <thead>
            <tr>
                <th>PO #</th>
                <th>Supplier</th>
                <th class="center">Date</th>
                <th class="center">Status</th>
                <th class="right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
            <tr>
                <td>{{ $order->po_number }}</td>
                <td>{{ $order->supplier_name }}</td>
                <td class="center">{{ $order->created_at->format('d M Y') }}</td>
                <td class="center"><span class="status-pill s-{{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
                <td class="right">₹{{ number_format($order->final_amount, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center;color:#9ca3af;padding:24px;">No purchase orders in this period.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4">Total Expense</td>
                <td class="right">₹{{ number_format($totalExpense, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="doc-footer">
        Confidential — For internal use only.<br>
        <span class="accent">{{ $settings['company_name'] ?? 'SolarTech ERP' }}</span>
    </div>
</div>
</body>
</html>
