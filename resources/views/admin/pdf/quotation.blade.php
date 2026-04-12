<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quotation — {{ $quotation->quotation_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 13px; color: #1a1a2e; background: #fff; }
        .page { max-width: 820px; margin: 0 auto; padding: 40px; }

        .doc-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 36px; padding-bottom: 24px; border-bottom: 3px solid #f97316; }
        .company-name { font-size: 22px; font-weight: 800; color: #1a1a2e; }
        .company-tagline { font-size: 11px; color: #6b7280; margin-top: 2px; }
        .company-meta { margin-top: 10px; font-size: 11px; color: #6b7280; line-height: 1.7; }
        .doc-badge { text-align: right; }
        .doc-type { font-size: 28px; font-weight: 900; color: #f97316; letter-spacing: -1px; text-transform: uppercase; }
        .doc-number { font-size: 13px; font-weight: 600; color: #374151; margin-top: 4px; }
        .doc-date { font-size: 11px; color: #9ca3af; margin-top: 2px; }
        .validity { font-size: 11px; margin-top: 4px; }
        .validity.expired { color: #dc2626; font-weight: 600; }
        .validity.valid   { color: #16a34a; font-weight: 600; }

        .status-pill { display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 20px; font-size: 10px; font-weight: 700; text-transform: uppercase; margin-top: 6px; }
        .s-pending  { background: #fef9c3; color: #a16207; }
        .s-sent     { background: #dbeafe; color: #1d4ed8; }
        .s-approved { background: #dcfce7; color: #15803d; }
        .s-rejected { background: #fee2e2; color: #dc2626; }
        .s-expired  { background: #f3f4f6; color: #6b7280; }

        .parties { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 32px; }
        .party-card { background: #f9fafb; border-radius: 10px; padding: 16px 18px; }
        .party-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #9ca3af; margin-bottom: 8px; }
        .party-name { font-size: 14px; font-weight: 700; color: #111827; margin-bottom: 4px; }
        .party-detail { font-size: 11px; color: #6b7280; line-height: 1.7; }

        .section-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #9ca3af; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        thead tr { background: #1a1a2e; color: #fff; }
        thead th { padding: 10px 14px; text-align: left; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        thead th.right { text-align: right; }
        thead th.center { text-align: center; }
        tbody tr { border-bottom: 1px solid #f3f4f6; }
        tbody tr:nth-child(even) { background: #f9fafb; }
        tbody td { padding: 10px 14px; font-size: 12px; color: #374151; }
        tbody td.center { text-align: center; }
        tbody td.right { text-align: right; font-weight: 600; }

        .totals-wrap { display: flex; justify-content: flex-end; margin-bottom: 32px; }
        .totals-box { width: 280px; }
        .totals-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 12px; border-bottom: 1px solid #f3f4f6; color: #6b7280; }
        .totals-row.grand { font-size: 15px; font-weight: 800; color: #1a1a2e; border-bottom: none; border-top: 2px solid #1a1a2e; padding-top: 10px; margin-top: 4px; }
        .totals-row.grand span:last-child { color: #f97316; }
        .totals-row.discount span:last-child { color: #16a34a; }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 32px; }
        .info-box { background: #f9fafb; border-radius: 10px; padding: 16px 18px; }
        .info-box-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #9ca3af; margin-bottom: 8px; }
        .info-box p { font-size: 12px; color: #374151; line-height: 1.6; }
        .bank-row { font-size: 11px; color: #6b7280; margin-bottom: 3px; }
        .bank-row strong { color: #374151; }

        .terms-box { background: #fff7ed; border: 1px solid #fed7aa; border-radius: 10px; padding: 16px 18px; margin-bottom: 32px; }
        .terms-box-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #c2410c; margin-bottom: 8px; }
        .terms-box p { font-size: 11px; color: #7c2d12; line-height: 1.7; white-space: pre-line; }

        .doc-footer { text-align: center; border-top: 1px solid #f3f4f6; padding-top: 16px; font-size: 10px; color: #9ca3af; line-height: 1.8; }
        .accent { color: #f97316; font-weight: 700; }

        @media print {
            .no-print { display: none !important; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .page { padding: 20px; }
        }
    </style>
</head>
<body>
<div class="no-print" style="position:fixed;top:0;left:0;right:0;z-index:9999;background:#1e293b;color:#fff;padding:10px 20px;display:flex;align-items:center;gap:12px;box-shadow:0 2px 8px rgba(0,0,0,0.3);">
    <span style="flex:1;font-size:13px;font-weight:600;">Quotation — {{ $quotation->quotation_number }}</span>
    <span style="font-size:11px;color:#94a3b8;">Uncheck "Headers and footers" in print dialog</span>
    <button onclick="window.print()" style="background:#f59e0b;color:#fff;border:none;padding:8px 20px;border-radius:8px;font-weight:700;font-size:13px;cursor:pointer;">⬇ Save as PDF</button>
    <button onclick="window.close()" style="background:rgba(255,255,255,0.1);color:#fff;border:none;padding:8px 14px;border-radius:8px;font-size:13px;cursor:pointer;">✕ Close</button>
</div>
<div style="height:48px;" class="no-print"></div>
<div class="page">

    <div class="doc-header">
        <div>
            @if(!empty($settings['company_logo']))
            <img src="{{ \App\Support\SupabaseStorage::url($settings['company_logo']) }}" style="max-height:50px;max-width:160px;object-fit:contain;margin-bottom:8px;display:block;">
            @endif
            <div class="company-name">{{ $settings['company_name'] ?? 'SolarTech ERP' }}</div>
            @if(!empty($settings['company_tagline']))<div class="company-tagline">{{ $settings['company_tagline'] }}</div>@endif
            <div class="company-meta">
                @if(!empty($settings['company_email'])){{ $settings['company_email'] }}<br>@endif
                @if(!empty($settings['company_phone'])){{ $settings['company_phone'] }}<br>@endif
                @if(!empty($settings['company_address'])){{ $settings['company_address'] }}<br>@endif
                @if(!empty($settings['gst_number']))<strong>GST:</strong> {{ $settings['gst_number'] }}@endif
            </div>
        </div>
        <div class="doc-badge">
            <div class="doc-type">Quotation</div>
            <div class="doc-number">{{ $quotation->quotation_number }}</div>
            <div class="doc-date">Date: {{ $quotation->created_at->format('d M Y') }}</div>
            @if($quotation->valid_until)
            <div class="validity {{ $quotation->valid_until->isPast() ? 'expired' : 'valid' }}">
                Valid Until: {{ $quotation->valid_until->format('d M Y') }}
            </div>
            @endif
            <span class="status-pill s-{{ $quotation->status }}">{{ ucfirst($quotation->status) }}</span>
        </div>
    </div>

    <div class="parties">
        <div class="party-card">
            <div class="party-label">Quotation For</div>
            <div class="party-name">{{ $quotation->customer_name }}</div>
            <div class="party-detail">
                {{ $quotation->customer_email }}<br>
                {{ $quotation->customer_phone }}<br>
                {{ $quotation->customer_address }}
            </div>
        </div>
        <div class="party-card">
            <div class="party-label">Issued By</div>
            <div class="party-name">{{ $settings['company_name'] ?? 'SolarTech ERP' }}</div>
            <div class="party-detail">
                @if(!empty($settings['company_email'])){{ $settings['company_email'] }}<br>@endif
                @if(!empty($settings['company_phone'])){{ $settings['company_phone'] }}<br>@endif
                @if(!empty($settings['gst_number']))GST: {{ $settings['gst_number'] }}<br>@endif
                @if(!empty($settings['pan_number']))PAN: {{ $settings['pan_number'] }}@endif
            </div>
        </div>
    </div>

    <div class="section-title">Quoted Items</div>
    <table>
        <thead>
            <tr>
                <th style="width:40px;">#</th>
                <th>Description</th>
                <th class="center" style="width:70px;">Qty</th>
                <th class="right" style="width:120px;">Unit Price</th>
                <th class="right" style="width:120px;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quotation->items as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->description }}</td>
                <td class="center">{{ $item->quantity }}</td>
                <td class="right">₹{{ number_format($item->unit_price, 2) }}</td>
                <td class="right">₹{{ number_format($item->total_price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals-wrap">
        <div class="totals-box">
            <div class="totals-row"><span>Subtotal</span><span>₹{{ number_format($quotation->total_amount, 2) }}</span></div>
            @if($quotation->tax_amount > 0)
            <div class="totals-row"><span>Tax</span><span>+₹{{ number_format($quotation->tax_amount, 2) }}</span></div>
            @endif
            @if($quotation->discount_amount > 0)
            <div class="totals-row discount"><span>Discount</span><span>-₹{{ number_format($quotation->discount_amount, 2) }}</span></div>
            @endif
            <div class="totals-row grand"><span>Grand Total</span><span>₹{{ number_format($quotation->final_amount, 2) }}</span></div>
        </div>
    </div>

    <div class="info-grid">
        @if($quotation->notes)
        <div class="info-box">
            <div class="info-box-title">Notes</div>
            <p>{{ $quotation->notes }}</p>
        </div>
        @endif
        @if(!empty($settings['bank_name']))
        <div class="info-box">
            <div class="info-box-title">Bank Details</div>
            <div class="bank-row"><strong>Bank:</strong> {{ $settings['bank_name'] }}</div>
            <div class="bank-row"><strong>A/C Name:</strong> {{ $settings['bank_account_name'] ?? '' }}</div>
            <div class="bank-row"><strong>A/C No:</strong> {{ $settings['bank_account_number'] ?? '' }}</div>
            <div class="bank-row"><strong>IFSC:</strong> {{ $settings['bank_ifsc'] ?? '' }}</div>
            @if(!empty($settings['upi_id']))<div class="bank-row"><strong>UPI:</strong> {{ $settings['upi_id'] }}</div>@endif
        </div>
        @endif
    </div>

    @if(!empty($settings['quotation_terms']))
    <div class="terms-box">
        <div class="terms-box-title">Terms & Conditions</div>
        <p>{{ $settings['quotation_terms'] }}</p>
    </div>
    @endif

    <div class="doc-footer">
        @if(!empty($settings['invoice_footer'])){{ $settings['invoice_footer'] }}<br>@else
        This quotation is valid for the period mentioned above. Prices are subject to change thereafter.<br>@endif
        <span class="accent">{{ $settings['company_name'] ?? 'SolarTech ERP' }}</span>
        @if(!empty($settings['company_website'])) · {{ $settings['company_website'] }}@endif
    </div>

</div>
<script>window.addEventListener('load', function(){ setTimeout(function(){ window.print(); }, 800); });</script>
</body>
</html>
