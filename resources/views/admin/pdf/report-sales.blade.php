<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales & Payment Analysis Report — {{ date('d M Y', strtotime($from)) }} to {{ date('d M Y', strtotime($to)) }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 12px; color: #1a1a2e; }
        .page { max-width: 900px; margin: 0 auto; padding: 40px; }
        .doc-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 36px; padding-bottom: 24px; border-bottom: 3px solid #3b82f6; }
        .company-name { font-size: 22px; font-weight: 800; color: #1a1a2e; }
        .company-meta { font-size: 11px; color: #6b7280; margin-top: 8px; line-height: 1.7; }
        .report-title { font-size: 26px; font-weight: 900; color: #3b82f6; text-align: right; }
        .report-subtitle { font-size: 13px; color: #6b7280; text-align: right; margin-top: 4px; font-weight: 600; }
        .report-period { font-size: 11px; color: #9ca3af; text-align: right; margin-top: 4px; }
        .generated { font-size: 10px; color: #9ca3af; text-align: right; margin-top: 2px; }

        .summary-section { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; padding: 24px; border-radius: 12px; margin-bottom: 32px; }
        .summary-title { font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 16px; opacity: 0.9; }
        .summary-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
        .summary-item { text-align: center; }
        .summary-label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.8; margin-bottom: 6px; }
        .summary-value { font-size: 24px; font-weight: 900; }
        .summary-sub { font-size: 10px; opacity: 0.7; margin-top: 4px; }

        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 32px; }
        .stat-card { background: #f9fafb; border-radius: 8px; padding: 14px; border-left: 3px solid #3b82f6; }
        .stat-label { font-size: 9px; color: #9ca3af; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; }
        .stat-value { font-size: 18px; font-weight: 800; color: #1a1a2e; margin-top: 4px; }

        .section-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #6b7280; margin-bottom: 12px; margin-top: 24px; padding-bottom: 8px; border-bottom: 2px solid #e5e7eb; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; font-size: 11px; }
        thead tr { background: #1f2937; color: #fff; }
        thead th { padding: 10px 8px; text-align: left; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        thead th.right { text-align: right; }
        thead th.center { text-align: center; }
        tbody tr { border-bottom: 1px solid #f3f4f6; }
        tbody tr:nth-child(even) { background: #f9fafb; }
        tbody td { padding: 8px; color: #374151; }
        tbody td.right { text-align: right; font-weight: 600; }
        tbody td.center { text-align: center; }
        tbody td.bold { font-weight: 700; color: #1a1a2e; }
        tfoot tr { background: #1f2937; color: white; font-weight: 700; }
        tfoot td { padding: 12px 8px; font-size: 12px; }
        tfoot td.right { text-align: right; }

        .status-pill { display: inline-flex; padding: 3px 8px; border-radius: 12px; font-size: 9px; font-weight: 700; text-transform: uppercase; }
        .s-paid { background:#dcfce7;color:#15803d; }
        .s-partial { background:#fef9c3;color:#a16207; }
        .s-unpaid { background:#fee2e2;color:#dc2626; }

        .payment-methods { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 24px; }
        .payment-method-card { background: #f9fafb; padding: 12px; border-radius: 8px; text-align: center; border: 1px solid #e5e7eb; }
        .payment-method-label { font-size: 9px; color: #6b7280; text-transform: uppercase; font-weight: 700; margin-bottom: 6px; }
        .payment-method-value { font-size: 16px; font-weight: 800; color: #1a1a2e; }

        .highlight-box { background: #fef3c7; border-left: 4px solid #f59e0b; padding: 12px; border-radius: 6px; margin-bottom: 20px; }
        .highlight-text { font-size: 11px; color: #92400e; font-weight: 600; }

        .doc-footer { text-align: center; border-top: 2px solid #e5e7eb; padding-top: 16px; font-size: 10px; color: #9ca3af; margin-top: 32px; }
        .accent { color: #3b82f6; font-weight: 700; }

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
            <div class="report-title">Sales & Payment Analysis</div>
            <div class="report-subtitle">Financial Performance Report</div>
            <div class="report-period">{{ date('d M Y', strtotime($from)) }} — {{ date('d M Y', strtotime($to)) }}</div>
            <div class="generated">Generated: {{ date('d M Y, h:i A') }}</div>
        </div>
    </div>

    {{-- Payment Summary --}}
    <div class="summary-section">
        <div class="summary-title">💰 Payment Summary Overview</div>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-label">Total Invoiced</div>
                <div class="summary-value">₹{{ number_format($totalInvoiced, 0) }}</div>
                <div class="summary-sub">Total billed amount</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Amount Received</div>
                <div class="summary-value">₹{{ number_format($totalReceived, 0) }}</div>
                <div class="summary-sub">{{ $totalInvoiced > 0 ? number_format(($totalReceived/$totalInvoiced)*100, 1) : 0 }}% collected</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Balance Pending</div>
                <div class="summary-value">₹{{ number_format($totalPending, 0) }}</div>
                <div class="summary-sub">Outstanding dues</div>
            </div>
        </div>
    </div>

    {{-- Key Metrics --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Invoices</div>
            <div class="stat-value">{{ count($invoices) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Fully Paid</div>
            <div class="stat-value">{{ $invoices->where('status', 'paid')->count() }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Partial Payment</div>
            <div class="stat-value">{{ $invoices->where('status', 'partial')->count() }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Unpaid</div>
            <div class="stat-value">{{ $invoices->where('status', 'unpaid')->count() }}</div>
        </div>
    </div>

    {{-- Payment Methods Breakdown --}}
    @if($paymentsByMethod->count() > 0)
    <div class="section-title">💳 Payment Methods Breakdown</div>
    <div class="payment-methods">
        @foreach($paymentsByMethod as $method => $amount)
        <div class="payment-method-card">
            <div class="payment-method-label">{{ $method }}</div>
            <div class="payment-method-value">₹{{ number_format($amount, 0) }}</div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Collection Rate Alert --}}
    @php
        $collectionRate = $totalInvoiced > 0 ? ($totalReceived / $totalInvoiced) * 100 : 0;
    @endphp
    @if($collectionRate < 80)
    <div class="highlight-box">
        <div class="highlight-text">
            ⚠️ <strong>Collection Alert:</strong> Current collection rate is {{ number_format($collectionRate, 1) }}%. 
            Focus on collecting ₹{{ number_format($totalPending, 0) }} in outstanding payments.
        </div>
    </div>
    @endif

    {{-- Detailed Invoice List --}}
    <div class="section-title">📋 Detailed Invoice & Payment Records</div>
    <table>
        <thead>
            <tr>
                <th>Invoice No</th>
                <th>Customer</th>
                <th>Order No</th>
                <th class="center">Date</th>
                <th class="right">Invoice Amt</th>
                <th class="right">Received</th>
                <th class="right">Balance</th>
                <th class="center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $invoice)
            <tr>
                <td class="bold">{{ $invoice->invoice_number }}</td>
                <td>{{ $invoice->customer->name ?? 'N/A' }}</td>
                <td>{{ $invoice->salesOrder->order_number ?? '-' }}</td>
                <td class="center">{{ $invoice->invoice_date->format('d/m/Y') }}</td>
                <td class="right">₹{{ number_format($invoice->grand_total, 2) }}</td>
                <td class="right" style="color: #15803d;">₹{{ number_format($invoice->paid_amount, 2) }}</td>
                <td class="right" style="color: {{ $invoice->balance_due > 0 ? '#dc2626' : '#6b7280' }};">₹{{ number_format($invoice->balance_due, 2) }}</td>
                <td class="center"><span class="status-pill s-{{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span></td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align:center;color:#9ca3af;padding:24px;">No invoices found for this period.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4"><strong>TOTALS</strong></td>
                <td class="right">₹{{ number_format($totalInvoiced, 2) }}</td>
                <td class="right">₹{{ number_format($totalReceived, 2) }}</td>
                <td class="right">₹{{ number_format($totalPending, 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    {{-- Summary Notes --}}
    <div class="section-title">📊 Financial Summary</div>
    <table style="margin-bottom: 0;">
        <tbody>
            <tr>
                <td style="font-weight: 700; width: 70%;">Total Amount Invoiced</td>
                <td class="right" style="font-size: 14px; font-weight: 800;">₹{{ number_format($totalInvoiced, 2) }}</td>
            </tr>
            <tr style="background: #dcfce7;">
                <td style="font-weight: 700;">Amount Received (Collected)</td>
                <td class="right" style="font-size: 14px; font-weight: 800; color: #15803d;">₹{{ number_format($totalReceived, 2) }}</td>
            </tr>
            <tr style="background: #fee2e2;">
                <td style="font-weight: 700;">Outstanding Balance (Pending)</td>
                <td class="right" style="font-size: 14px; font-weight: 800; color: #dc2626;">₹{{ number_format($totalPending, 2) }}</td>
            </tr>
            <tr>
                <td style="font-weight: 700;">Collection Rate</td>
                <td class="right" style="font-size: 14px; font-weight: 800; color: #3b82f6;">{{ number_format($collectionRate, 2) }}%</td>
            </tr>
        </tbody>
    </table>

    <div class="doc-footer">
        <strong>Confidential Document — For Internal Use Only</strong><br>
        This report contains sensitive financial information. Handle with care.<br>
        <span class="accent">{{ $settings['company_name'] ?? 'SolarTech ERP' }}</span> • Generated on {{ date('d M Y, h:i A') }}
    </div>
</div>
</body>
</html>

// Made with Bob
