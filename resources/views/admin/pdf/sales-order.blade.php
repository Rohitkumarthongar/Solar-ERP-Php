<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Order — {{ $order->order_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 13px; color: #1a1a2e; background: #fff; }

        .page { max-width: 820px; margin: 0 auto; padding: 40px; }

        /* Header */
        .doc-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 36px; padding-bottom: 24px; border-bottom: 3px solid #f97316; }
        .company-brand .company-name { font-size: 22px; font-weight: 800; color: #1a1a2e; letter-spacing: -0.5px; }
        .company-brand .company-tagline { font-size: 11px; color: #6b7280; margin-top: 2px; }
        .company-brand .company-meta { margin-top: 10px; font-size: 11px; color: #6b7280; line-height: 1.7; }
        .doc-badge { text-align: right; }
        .doc-badge .doc-type { font-size: 28px; font-weight: 900; color: #f97316; letter-spacing: -1px; text-transform: uppercase; }
        .doc-badge .doc-number { font-size: 13px; font-weight: 600; color: #374151; margin-top: 4px; }
        .doc-badge .doc-date { font-size: 11px; color: #9ca3af; margin-top: 2px; }

        /* Status Banner */
        .status-banner { display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; margin-top: 6px; text-transform: uppercase; letter-spacing: 0.5px; }
        .status-confirmed  { background: #dbeafe; color: #1d4ed8; }
        .status-processing { background: #fef9c3; color: #a16207; }
        .status-dispatched { background: #ede9fe; color: #7c3aed; }
        .status-completed  { background: #dcfce7; color: #15803d; }
        .status-cancelled  { background: #fee2e2; color: #dc2626; }
        .pay-pending  { background: #fef9c3; color: #a16207; }
        .pay-partial  { background: #dbeafe; color: #1d4ed8; }
        .pay-paid     { background: #dcfce7; color: #15803d; }

        /* Parties */
        .parties { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 32px; }
        .party-card { background: #f9fafb; border-radius: 10px; padding: 16px 18px; }
        .party-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #9ca3af; margin-bottom: 8px; }
        .party-name { font-size: 14px; font-weight: 700; color: #111827; margin-bottom: 4px; }
        .party-detail { font-size: 11px; color: #6b7280; line-height: 1.7; }

        /* Items Table */
        .section-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #9ca3af; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        thead tr { background: #1a1a2e; color: #fff; }
        thead th { padding: 10px 14px; text-align: left; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        thead th:last-child { text-align: right; }
        thead th.center { text-align: center; }
        tbody tr { border-bottom: 1px solid #f3f4f6; }
        tbody tr:nth-child(even) { background: #f9fafb; }
        tbody td { padding: 10px 14px; font-size: 12px; color: #374151; }
        tbody td.center { text-align: center; }
        tbody td.right { text-align: right; font-weight: 600; }

        /* Totals */
        .totals-wrap { display: flex; justify-content: flex-end; margin-bottom: 32px; }
        .totals-box { width: 280px; }
        .totals-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 12px; border-bottom: 1px solid #f3f4f6; color: #6b7280; }
        .totals-row.grand { font-size: 15px; font-weight: 800; color: #1a1a2e; border-bottom: none; border-top: 2px solid #1a1a2e; padding-top: 10px; margin-top: 4px; }
        .totals-row.grand span:last-child { color: #f97316; }
        .totals-row.discount span:last-child { color: #16a34a; }

        /* Notes & Bank */
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 32px; }
        .info-box { background: #f9fafb; border-radius: 10px; padding: 16px 18px; }
        .info-box-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #9ca3af; margin-bottom: 8px; }
        .info-box p { font-size: 12px; color: #374151; line-height: 1.6; }
        .info-box .bank-row { font-size: 11px; color: #6b7280; margin-bottom: 3px; }
        .info-box .bank-row strong { color: #374151; }

        /* Footer */
        .doc-footer { text-align: center; border-top: 1px solid #f3f4f6; padding-top: 16px; font-size: 10px; color: #9ca3af; line-height: 1.8; }
        .accent { color: #f97316; font-weight: 700; }

        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .page { padding: 20px; }
        }
    </style>
</head>
<body>
<div class="page">

    {{-- ═══ HEADER ═══ --}}
    <div class="doc-header">
        <div class="company-brand">
            @if(!empty($settings['company_logo']))
            <img src="{{ \App\Support\SupabaseStorage::url($settings['company_logo']) }}" style="max-height:50px;max-width:160px;object-fit:contain;margin-bottom:8px;display:block;">
            @endif
            <div class="company-name">{{ $settings['company_name'] ?? 'SolarTech ERP' }}</div>
            @if(!empty($settings['company_tagline']))
            <div class="company-tagline">{{ $settings['company_tagline'] }}</div>
            @endif
            <div class="company-meta">
                @if(!empty($settings['company_email'])){{ $settings['company_email'] }}<br>@endif
                @if(!empty($settings['company_phone'])){{ $settings['company_phone'] }}<br>@endif
                @if(!empty($settings['company_address'])){{ $settings['company_address'] }}<br>@endif
                @if(!empty($settings['gst_number']))<strong>GST:</strong> {{ $settings['gst_number'] }}@endif
            </div>
        </div>
        <div class="doc-badge">
            <div class="doc-type">Sales Order</div>
            <div class="doc-number">{{ $order->order_number }}</div>
            <div class="doc-date">Date: {{ $order->created_at->format('d M Y') }}</div>
            <div>
                @php
                    $sc = ['confirmed'=>'confirmed','processing'=>'processing','dispatched'=>'dispatched','completed'=>'completed','cancelled'=>'cancelled'];
                    $pc = ['pending'=>'pay-pending','partial'=>'pay-partial','paid'=>'pay-paid'];
                @endphp
                <span class="status-banner status-{{ $sc[$order->status] ?? 'confirmed' }}">{{ ucfirst($order->status) }}</span>
                <span class="status-banner {{ $pc[$order->payment_status] ?? 'pay-pending' }}">{{ ucfirst($order->payment_status) }}</span>
            </div>
        </div>
    </div>

    {{-- ═══ PARTIES ═══ --}}
    <div class="parties">
        <div class="party-card">
            <div class="party-label">Bill To</div>
            <div class="party-name">{{ $order->customer_name }}</div>
            <div class="party-detail">
                {{ $order->customer_email }}<br>
                {{ $order->customer_phone }}<br>
                {{ $order->customer_address }}
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

    {{-- ═══ ITEMS ═══ --}}
    <div class="section-title">Order Items</div>
    <table>
        <thead>
            <tr>
                <th style="width:40px;">#</th>
                <th>Description</th>
                <th class="center" style="width:70px;">Qty</th>
                <th style="width:120px; text-align:right;">Unit Price</th>
                <th style="width:120px; text-align:right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $i => $item)
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

    {{-- ═══ TOTALS ═══ --}}
    <div class="totals-wrap">
        <div class="totals-box">
            <div class="totals-row">
                <span>Subtotal</span>
                <span>₹{{ number_format($order->total_amount, 2) }}</span>
            </div>
            @if($order->tax_amount > 0)
            <div class="totals-row">
                <span>Tax</span>
                <span>+₹{{ number_format($order->tax_amount, 2) }}</span>
            </div>
            @endif
            @if($order->discount_amount > 0)
            <div class="totals-row discount">
                <span>Discount</span>
                <span>-₹{{ number_format($order->discount_amount, 2) }}</span>
            </div>
            @endif
            <div class="totals-row grand">
                <span>Grand Total</span>
                <span>₹{{ number_format($order->final_amount, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- ═══ NOTES + BANK ═══ --}}
    <div class="info-grid">
        @if($order->notes)
        <div class="info-box">
            <div class="info-box-title">Notes</div>
            <p>{{ $order->notes }}</p>
        </div>
        @endif
        @if(!empty($settings['bank_name']))
        <div class="info-box">
            <div class="info-box-title">Bank Details</div>
            <div class="bank-row"><strong>Bank:</strong> {{ $settings['bank_name'] }}</div>
            <div class="bank-row"><strong>A/C Name:</strong> {{ $settings['bank_account_name'] ?? '' }}</div>
            <div class="bank-row"><strong>A/C No:</strong> {{ $settings['bank_account_number'] ?? '' }}</div>
            <div class="bank-row"><strong>IFSC:</strong> {{ $settings['bank_ifsc'] ?? '' }}</div>
            @if(!empty($settings['upi_id']))
            <div class="bank-row"><strong>UPI:</strong> {{ $settings['upi_id'] }}</div>
            @endif
        </div>
        @endif
    </div>

    {{-- ═══ FOOTER ═══ --}}
    <div class="doc-footer">
        @if(!empty($settings['invoice_footer']))
            {{ $settings['invoice_footer'] }}<br>
        @else
            Thank you for your business. This is a computer-generated document and does not require a signature.<br>
        @endif
        <span class="accent">{{ $settings['company_name'] ?? 'SolarTech ERP' }}</span>
        @if(!empty($settings['company_website'])) · {{ $settings['company_website'] }}@endif
    </div>

</div>
</body>
</html>
