<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $invoice->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 13px; color: #1a1a2e; background: #fff; }
        .page { max-width: 820px; margin: 0 auto; padding: 40px; }
        .doc-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 36px; padding-bottom: 24px; border-bottom: 3px solid #f97316; }
        .company-name { font-size: 22px; font-weight: 800; color: #1a1a2e; }
        .company-meta { margin-top: 10px; font-size: 11px; color: #6b7280; line-height: 1.7; }
        .doc-badge { text-align: right; }
        .doc-type { font-size: 28px; font-weight: 900; color: #f97316; text-transform: uppercase; }
        .doc-number { font-size: 13px; font-weight: 600; color: #374151; margin-top: 4px; }
        .doc-date { font-size: 11px; color: #9ca3af; margin-top: 2px; }
        .status-pill { display: inline-flex; padding: 4px 10px; border-radius: 999px; font-size: 10px; font-weight: 700; text-transform: uppercase; margin-top: 6px; }
        .s-unpaid { background: #fee2e2; color: #dc2626; }
        .s-partially_paid { background: #fef3c7; color: #b45309; }
        .s-paid { background: #dcfce7; color: #15803d; }
        .parties { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 32px; }
        .party-card { background: #f9fafb; border-radius: 10px; padding: 16px 18px; }
        .party-label { font-size: 10px; font-weight: 700; text-transform: uppercase; color: #9ca3af; margin-bottom: 8px; }
        .party-name { font-size: 14px; font-weight: 700; color: #111827; margin-bottom: 4px; }
        .party-detail { font-size: 11px; color: #6b7280; line-height: 1.7; }
        .section-title { font-size: 10px; font-weight: 700; text-transform: uppercase; color: #9ca3af; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        thead tr { background: #1a1a2e; color: #fff; }
        thead th { padding: 10px 14px; text-align: left; font-size: 11px; font-weight: 600; text-transform: uppercase; }
        thead th.right { text-align: right; }
        tbody tr { border-bottom: 1px solid #f3f4f6; }
        tbody tr:nth-child(even) { background: #f9fafb; }
        tbody td { padding: 10px 14px; font-size: 12px; color: #374151; }
        tbody td.right { text-align: right; font-weight: 600; }
        .totals-wrap { display: flex; justify-content: flex-end; margin-bottom: 32px; }
        .totals-box { width: 300px; }
        .totals-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 12px; border-bottom: 1px solid #f3f4f6; color: #6b7280; }
        .totals-row.grand { font-size: 15px; font-weight: 800; color: #1a1a2e; border-top: 2px solid #1a1a2e; border-bottom: none; padding-top: 10px; margin-top: 4px; }
        .totals-row.grand span:last-child { color: #f97316; }
        .totals-row.paid span:last-child { color: #15803d; }
        .totals-row.balance span:last-child { color: #dc2626; }
        .notes-box { background: #f9fafb; border-radius: 10px; padding: 16px 18px; margin-bottom: 24px; }
        .notes-title { font-size: 10px; font-weight: 700; text-transform: uppercase; color: #9ca3af; margin-bottom: 8px; }
        .notes-box p { font-size: 12px; color: #374151; line-height: 1.6; white-space: pre-line; }
        .doc-footer { text-align: center; border-top: 1px solid #f3f4f6; padding-top: 16px; font-size: 10px; color: #9ca3af; line-height: 1.8; }
    </style>
</head>
<body>
<div class="no-print" style="position:fixed;top:0;left:0;right:0;z-index:9999;background:#1e293b;color:#fff;padding:10px 20px;display:flex;align-items:center;gap:12px;box-shadow:0 2px 8px rgba(0,0,0,0.3);">
    <span style="flex:1;font-size:13px;font-weight:600;">Invoice — {{ $invoice->invoice_number }}</span>
    <button onclick="window.print()" style="background:#f59e0b;color:#fff;border:none;padding:8px 20px;border-radius:8px;font-weight:700;font-size:13px;cursor:pointer;">⬇ Save as PDF</button>
    <button onclick="window.close()" style="background:rgba(255,255,255,0.1);color:#fff;border:none;padding:8px 14px;border-radius:8px;font-size:13px;cursor:pointer;">✕ Close</button>
</div>
<div style="height:48px;" class="no-print"></div>
<div class="page">
        <div>
            @if(!empty($settings['company_logo']))
            <img src="{{ \App\Support\SupabaseStorage::url($settings['company_logo']) }}" style="max-height:50px;max-width:160px;object-fit:contain;margin-bottom:8px;display:block;">
            @endif
            <div class="company-name">{{ $settings['company_name'] ?? 'SolarTech ERP' }}</div>
            <div class="company-meta">
                @if(!empty($settings['company_email'])){{ $settings['company_email'] }}<br>@endif
                @if(!empty($settings['company_phone'])){{ $settings['company_phone'] }}<br>@endif
                @if(!empty($settings['company_address'])){{ $settings['company_address'] }}<br>@endif
                @if(!empty($settings['gst_number']))<strong>GST:</strong> {{ $settings['gst_number'] }}@endif
            </div>
        </div>
        <div class="doc-badge">
            <div class="doc-type">Invoice</div>
            <div class="doc-number">{{ $invoice->invoice_number }}</div>
            <div class="doc-date">Issued: {{ $invoice->invoice_date?->format('d M Y') }}</div>
            @if($invoice->due_date)
            <div class="doc-date">Due: {{ $invoice->due_date->format('d M Y') }}</div>
            @endif
            <span class="status-pill s-{{ $invoice->status }}">{{ str_replace('_', ' ', $invoice->status) }}</span>
        </div>
    </div>

    <div class="parties">
        <div class="party-card">
            <div class="party-label">Bill To</div>
            <div class="party-name">{{ $invoice->customer->name ?? 'Customer' }}</div>
            <div class="party-detail">
                {{ $invoice->customer->email ?? '' }}<br>
                {{ $invoice->customer->phone ?? '' }}<br>
                {{ $invoice->customer->address ?? '' }}
            </div>
        </div>
        <div class="party-card">
            <div class="party-label">From</div>
            <div class="party-name">{{ $settings['company_name'] ?? 'SolarTech ERP' }}</div>
            <div class="party-detail">
                @if(!empty($settings['company_email'])){{ $settings['company_email'] }}<br>@endif
                @if(!empty($settings['company_phone'])){{ $settings['company_phone'] }}<br>@endif
                @if(!empty($settings['gst_number']))GST: {{ $settings['gst_number'] }}<br>@endif
                @if(!empty($settings['pan_number']))PAN: {{ $settings['pan_number'] }}@endif
            </div>
        </div>
    </div>

    <div class="section-title">Invoice Items</div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Description</th>
                <th class="right">Qty</th>
                <th class="right">Unit Price</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->product_name }}</td>
                <td class="right">{{ $item->quantity }}</td>
                <td class="right">₹{{ number_format($item->unit_price, 2) }}</td>
                <td class="right">₹{{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals-wrap">
        <div class="totals-box">
            <div class="totals-row"><span>Subtotal</span><span>₹{{ number_format($invoice->sub_total, 2) }}</span></div>
            <div class="totals-row grand"><span>Grand Total</span><span>₹{{ number_format($invoice->grand_total, 2) }}</span></div>
            <div class="totals-row paid"><span>Paid Amount</span><span>₹{{ number_format($invoice->paid_amount, 2) }}</span></div>
            <div class="totals-row balance"><span>Balance Due</span><span>₹{{ number_format($invoice->balance_due, 2) }}</span></div>
        </div>
    </div>

    @if($invoice->notes)
    <div class="notes-box">
        <div class="notes-title">Notes</div>
        <p>{{ $invoice->notes }}</p>
    </div>
    @endif

    <div class="doc-footer">
        {{ $settings['invoice_footer'] ?? 'Thank you for your business.' }}<br>
        {{ $settings['company_name'] ?? 'SolarTech ERP' }}
    </div>
</div>
<script>window.addEventListener('load', function(){ setTimeout(function(){ window.print(); }, 800); });</script>
</body>
</html>
