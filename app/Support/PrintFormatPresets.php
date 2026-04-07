<?php

namespace App\Support;

class PrintFormatPresets
{
    public static function all(): array
    {
        return [
            'quotation_standard' => self::quotationStandard(),
            'sales_order_standard' => self::salesOrderStandard(),
            'sales_invoice_standard' => self::salesInvoiceStandard(),
            'purchase_order_standard' => self::purchaseOrderStandard(),
            'quotation_pdf_replica' => self::quotationPdfReplica(),
            'salary_slip_standard' => self::salarySlipStandard(),
            'work_application_standard' => self::workApplicationStandard(),
            'dcr_form_standard' => self::dcrFormStandard(),
            'installation_certificate_standard' => self::installationCertificateStandard(),
            'service_report_standard' => self::serviceReportStandard(),
            'site_visit_report_standard' => self::siteVisitReportStandard(),
        ];
    }

    public static function quotationStandard(): array
    {
        return [
            'label' => 'Default Quotation',
            'name' => 'Default Quotation',
            'document_type' => 'quotation',
            'paper_size' => 'A4',
            'orientation' => 'portrait',
            'header_html' => '',
            'footer_html' => '',
            'body_template' => <<<'BLADE'
<div style="padding: 12px 4px;">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;border-bottom:3px solid #f97316;padding-bottom:18px;margin-bottom:24px;">
        <div>
            <div style="font-size:24px;font-weight:800;">{{ $settings['company_name'] ?? 'Solar ERP' }}</div>
            <div style="font-size:12px;color:#6b7280;">{{ $settings['company_tagline'] ?? '' }}</div>
            <div style="margin-top:10px;font-size:12px;color:#4b5563;line-height:1.7;">
                {{ $settings['company_email'] ?? '' }}<br>
                {{ $settings['company_phone'] ?? '' }}<br>
                {{ $settings['company_address'] ?? '' }}
            </div>
        </div>
        <div style="text-align:right;">
            <div style="font-size:28px;font-weight:900;color:#f97316;">QUOTATION</div>
            <div style="font-size:13px;font-weight:700;">{{ $quotation->quotation_number }}</div>
            <div style="font-size:11px;color:#6b7280;">{{ optional($quotation->created_at)->format('d M Y') }}</div>
        </div>
    </div>

    <table style="margin-bottom:24px;">
        <tr>
            <td style="width:50%;vertical-align:top;padding-right:12px;">
                <div style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;">Quotation For</div>
                <div style="font-size:15px;font-weight:700;">{{ $quotation->customer_name }}</div>
                <div style="font-size:12px;color:#4b5563;line-height:1.7;">
                    {{ $quotation->customer_email }}<br>
                    {{ $quotation->customer_phone }}<br>
                    {{ $quotation->customer_address }}
                </div>
            </td>
            <td style="width:50%;vertical-align:top;padding-left:12px;">
                <div style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;">Status</div>
                <div style="font-size:14px;font-weight:700;">{{ ucfirst($quotation->status) }}</div>
                @if($quotation->valid_until)
                <div style="margin-top:10px;font-size:12px;color:#4b5563;">Valid Until: {{ $quotation->valid_until->format('d M Y') }}</div>
                @endif
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr style="background:#111827;color:#fff;">
                <th style="padding:10px;">#</th>
                <th style="padding:10px;text-align:left;">Description</th>
                <th style="padding:10px;">Qty</th>
                <th style="padding:10px;text-align:right;">Unit Price</th>
                <th style="padding:10px;text-align:right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quotation->items as $i => $item)
            <tr>
                <td style="padding:10px;border-bottom:1px solid #e5e7eb;text-align:center;">{{ $i + 1 }}</td>
                <td style="padding:10px;border-bottom:1px solid #e5e7eb;">{{ $item->description }}</td>
                <td style="padding:10px;border-bottom:1px solid #e5e7eb;text-align:center;">{{ $item->quantity }}</td>
                <td style="padding:10px;border-bottom:1px solid #e5e7eb;text-align:right;">₹{{ number_format($item->unit_price, 2) }}</td>
                <td style="padding:10px;border-bottom:1px solid #e5e7eb;text-align:right;">₹{{ number_format($item->total_price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
BLADE,
        ];
    }

    public static function salesOrderStandard(): array
    {
        return [
            'label' => 'Default Sales Order',
            'name' => 'Default Sales Order',
            'document_type' => 'sales_order',
            'paper_size' => 'A4',
            'orientation' => 'portrait',
            'header_html' => '',
            'footer_html' => '',
            'body_template' => <<<'BLADE'
<div style="padding: 12px 4px;">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;border-bottom:3px solid #f97316;padding-bottom:18px;margin-bottom:24px;">
        <div>
            <div style="font-size:24px;font-weight:800;">{{ $settings['company_name'] ?? 'Solar ERP' }}</div>
            <div style="font-size:12px;color:#6b7280;">{{ $settings['company_tagline'] ?? '' }}</div>
        </div>
        <div style="text-align:right;">
            <div style="font-size:28px;font-weight:900;color:#f97316;">SALES ORDER</div>
            <div style="font-size:13px;font-weight:700;">{{ $order->order_number }}</div>
            <div style="font-size:11px;color:#6b7280;">{{ optional($order->created_at)->format('d M Y') }}</div>
        </div>
    </div>
    <div style="margin-bottom:16px;font-size:13px;"><strong>Customer:</strong> {{ $order->customer_name }}</div>
    <table>
        <thead>
            <tr style="background:#111827;color:#fff;">
                <th style="padding:10px;">#</th>
                <th style="padding:10px;text-align:left;">Description</th>
                <th style="padding:10px;">Qty</th>
                <th style="padding:10px;text-align:right;">Unit Price</th>
                <th style="padding:10px;text-align:right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $i => $item)
            <tr>
                <td style="padding:10px;border-bottom:1px solid #e5e7eb;text-align:center;">{{ $i + 1 }}</td>
                <td style="padding:10px;border-bottom:1px solid #e5e7eb;">{{ $item->description }}</td>
                <td style="padding:10px;border-bottom:1px solid #e5e7eb;text-align:center;">{{ $item->quantity }}</td>
                <td style="padding:10px;border-bottom:1px solid #e5e7eb;text-align:right;">₹{{ number_format($item->unit_price, 2) }}</td>
                <td style="padding:10px;border-bottom:1px solid #e5e7eb;text-align:right;">₹{{ number_format($item->total_price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
BLADE,
        ];
    }

    public static function salesInvoiceStandard(): array
    {
        return [
            'label' => 'Default Sales Invoice',
            'name' => 'Default Sales Invoice',
            'document_type' => 'invoice',
            'paper_size' => 'A4',
            'orientation' => 'portrait',
            'header_html' => '',
            'footer_html' => '',
            'body_template' => <<<'BLADE'
<div style="padding: 12px 4px;">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;border-bottom:3px solid #f97316;padding-bottom:18px;margin-bottom:24px;">
        <div>
            <div style="font-size:24px;font-weight:800;">{{ $settings['company_name'] ?? 'Solar ERP' }}</div>
            <div style="font-size:12px;color:#6b7280;">{{ $settings['company_tagline'] ?? '' }}</div>
        </div>
        <div style="text-align:right;">
            <div style="font-size:28px;font-weight:900;color:#f97316;">SALES INVOICE</div>
            <div style="font-size:13px;font-weight:700;">{{ $invoice->invoice_number }}</div>
            <div style="font-size:11px;color:#6b7280;">{{ optional($invoice->created_at)->format('d M Y') }}</div>
        </div>
    </div>
    <div style="margin-bottom:16px;font-size:13px;"><strong>Customer:</strong> {{ $invoice->customer_name }}</div>
    <table>
        <thead>
            <tr style="background:#111827;color:#fff;">
                <th style="padding:10px;">#</th>
                <th style="padding:10px;text-align:left;">Description</th>
                <th style="padding:10px;">Qty</th>
                <th style="padding:10px;text-align:right;">Unit Price</th>
                <th style="padding:10px;text-align:right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $i => $item)
            <tr>
                <td style="padding:10px;border-bottom:1px solid #e5e7eb;text-align:center;">{{ $i + 1 }}</td>
                <td style="padding:10px;border-bottom:1px solid #e5e7eb;">{{ $item->description }}</td>
                <td style="padding:10px;border-bottom:1px solid #e5e7eb;text-align:center;">{{ $item->quantity }}</td>
                <td style="padding:10px;border-bottom:1px solid #e5e7eb;text-align:right;">₹{{ number_format($item->unit_price, 2) }}</td>
                <td style="padding:10px;border-bottom:1px solid #e5e7eb;text-align:right;">₹{{ number_format($item->total_price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
BLADE,
        ];
    }

    public static function purchaseOrderStandard(): array
    {
        return [
            'label' => 'Default Purchase Order',
            'name' => 'Default Purchase Order',
            'document_type' => 'purchase_order',
            'paper_size' => 'A4',
            'orientation' => 'portrait',
            'header_html' => '',
            'footer_html' => '',
            'body_template' => <<<'BLADE'
<div style="padding: 12px 4px;">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;border-bottom:3px solid #f97316;padding-bottom:18px;margin-bottom:24px;">
        <div>
            <div style="font-size:24px;font-weight:800;">{{ $settings['company_name'] ?? 'Solar ERP' }}</div>
            <div style="font-size:12px;color:#6b7280;">{{ $settings['company_tagline'] ?? '' }}</div>
        </div>
        <div style="text-align:right;">
            <div style="font-size:28px;font-weight:900;color:#f97316;">PURCHASE ORDER</div>
            <div style="font-size:13px;font-weight:700;">{{ $order->po_number }}</div>
            <div style="font-size:11px;color:#6b7280;">{{ optional($order->created_at)->format('d M Y') }}</div>
        </div>
    </div>
    <div style="margin-bottom:16px;font-size:13px;"><strong>Supplier:</strong> {{ $order->supplier_name }}</div>
    <table>
        <thead>
            <tr style="background:#111827;color:#fff;">
                <th style="padding:10px;">#</th>
                <th style="padding:10px;text-align:left;">Description</th>
                <th style="padding:10px;">Qty</th>
                <th style="padding:10px;text-align:right;">Unit Price</th>
                <th style="padding:10px;text-align:right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $i => $item)
            <tr>
                <td style="padding:10px;border-bottom:1px solid #e5e7eb;text-align:center;">{{ $i + 1 }}</td>
                <td style="padding:10px;border-bottom:1px solid #e5e7eb;">{{ $item->description }}</td>
                <td style="padding:10px;border-bottom:1px solid #e5e7eb;text-align:center;">{{ $item->quantity }}</td>
                <td style="padding:10px;border-bottom:1px solid #e5e7eb;text-align:right;">₹{{ number_format($item->unit_price, 2) }}</td>
                <td style="padding:10px;border-bottom:1px solid #e5e7eb;text-align:right;">₹{{ number_format($item->total_price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
BLADE,
        ];
    }

    public static function workApplicationStandard(): array
    {
        return [
            'label' => 'Default Work Application',
            'name' => 'Default Work Application',
            'document_type' => 'work_application',
            'paper_size' => 'A4',
            'orientation' => 'portrait',
            'header_html' => '',
            'footer_html' => '',
            'body_template' => <<<'BLADE'
<div style="padding:16px 8px;">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;border-bottom:3px solid #f59e0b;padding-bottom:16px;margin-bottom:24px;">
        <div>
            <div style="font-size:24px;font-weight:800;">{{ $settings['company_name'] ?? 'Solar ERP' }}</div>
            <div style="font-size:12px;color:#6b7280;">{{ $settings['company_tagline'] ?? '' }}</div>
            <div style="margin-top:8px;font-size:12px;color:#4b5563;line-height:1.7;">
                {{ $settings['company_phone'] ?? '' }}<br>
                {{ $settings['company_email'] ?? '' }}<br>
                {{ $settings['company_address'] ?? '' }}
            </div>
        </div>
        <div style="text-align:right;">
            <div style="font-size:28px;font-weight:900;color:#f59e0b;">WORK APPLICATION</div>
            <div style="font-size:13px;font-weight:700;">{{ $installation->installation_number }}</div>
            <div style="font-size:11px;color:#6b7280;">{{ optional($installation->scheduled_date)->format('d M Y') }}</div>
        </div>
    </div>

    <table style="margin-bottom:20px;">
        <tr>
            <td style="width:50%;padding-right:12px;vertical-align:top;">
                <div style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;">Customer & DISCOM</div>
                <div style="margin-top:8px;font-size:13px;line-height:1.8;">
                    <strong>{{ $installation->customer->name ?? 'N/A' }}</strong><br>
                    {{ $installation->customer->phone ?? 'N/A' }}<br>
                    DISCOM: {{ $installation->customer->discom->discom_name ?? 'N/A' }}<br>
                    K-Number: {{ $installation->customer->discom->k_number ?? 'N/A' }}<br>
                    Application No: {{ $installation->customer->discom->application_number ?? 'N/A' }}
                </div>
            </td>
            <td style="width:50%;padding-left:12px;vertical-align:top;">
                <div style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;">Installation Details</div>
                <div style="margin-top:8px;font-size:13px;line-height:1.8;">
                    System Size: {{ $installation->system_size_kw }} kW<br>
                    Roof Type: {{ $installation->roof_type }}<br>
                    Team: {{ $installation->assigned_team ?? 'TBD' }}<br>
                    Address: {{ $installation->installation_address }}
                </div>
            </td>
        </tr>
    </table>

    @if(!empty($installation->panel_serial_details))
    <table style="width:100%;border-collapse:collapse;margin-top:12px;">
        <thead>
            <tr style="background:#111827;color:#fff;">
                <th style="padding:10px;text-align:left;">Panel Serial</th>
                <th style="padding:10px;text-align:left;">Make</th>
                <th style="padding:10px;text-align:left;">Wattage</th>
                <th style="padding:10px;text-align:left;">String</th>
            </tr>
        </thead>
        <tbody>
            @foreach($installation->panel_serial_details as $panel)
            <tr>
                <td style="padding:10px;border-bottom:1px solid #e5e7eb;">{{ $panel['serial_number'] ?? '-' }}</td>
                <td style="padding:10px;border-bottom:1px solid #e5e7eb;">{{ $panel['module_make'] ?? '-' }}</td>
                <td style="padding:10px;border-bottom:1px solid #e5e7eb;">{{ $panel['wattage'] ?? '-' }}</td>
                <td style="padding:10px;border-bottom:1px solid #e5e7eb;">{{ $panel['string_number'] ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
BLADE,
        ];
    }

    public static function dcrFormStandard(): array
    {
        return [
            'label' => 'Default DCR Form',
            'name' => 'Default DCR Form',
            'document_type' => 'dcr_form',
            'paper_size' => 'A4',
            'orientation' => 'portrait',
            'header_html' => '',
            'footer_html' => '',
            'body_template' => <<<'BLADE'
<div style="padding:16px 8px;">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;border-bottom:3px solid #10b981;padding-bottom:16px;margin-bottom:24px;">
        <div>
            <div style="font-size:24px;font-weight:800;">{{ $settings['company_name'] ?? 'Solar ERP' }}</div>
            <div style="font-size:12px;color:#6b7280;">DCR Certificate</div>
        </div>
        <div style="text-align:right;">
            <div style="font-size:28px;font-weight:900;color:#10b981;">DCR</div>
            <div style="font-size:13px;font-weight:700;">{{ $installation->installation_number }}</div>
            <div style="font-size:11px;color:#6b7280;">{{ now()->format('d M Y') }}</div>
        </div>
    </div>

    <table style="width:100%;margin-bottom:20px;">
        <tr>
            <td style="width:50%;padding-right:12px;vertical-align:top;">
                <div style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;">Consumer Details</div>
                <div style="margin-top:8px;font-size:13px;line-height:1.8;">
                    <strong>{{ $installation->customer->name ?? 'N/A' }}</strong><br>
                    {{ $installation->customer->phone ?? 'N/A' }}<br>
                    {{ $installation->installation_address }}
                </div>
            </td>
            <td style="width:50%;padding-left:12px;vertical-align:top;">
                <div style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;">Technical Summary</div>
                <div style="margin-top:8px;font-size:13px;line-height:1.8;">
                    System Size: {{ $installation->system_size_kw }} kW<br>
                    Roof Type: {{ $installation->roof_type }}<br>
                    Meter No: {{ $installation->net_meter_serial_number ?? 'N/A' }}<br>
                    Inverter No: {{ $installation->inverter_serial_number ?? 'N/A' }}
                </div>
            </td>
        </tr>
    </table>

    <div style="font-size:13px;line-height:1.9;color:#374151;">
        Certified that the installed solar plant under {{ $installation->installation_number }} has been inspected and the material details recorded in this document are true to the best of our knowledge.
    </div>
</div>
BLADE,
        ];
    }

    public static function installationCertificateStandard(): array
    {
        return [
            'label' => 'Installation Completion Certificate',
            'name' => 'Installation Completion Certificate',
            'document_type' => 'installation_certificate',
            'paper_size' => 'A4',
            'orientation' => 'portrait',
            'header_html' => '',
            'footer_html' => '',
            'body_template' => <<<'BLADE'
<div style="padding:20px 12px;">
    <div style="text-align:center;border-bottom:4px solid #3b82f6;padding-bottom:20px;margin-bottom:28px;">
        <div style="font-size:32px;font-weight:900;color:#3b82f6;">INSTALLATION COMPLETION CERTIFICATE</div>
        <div style="font-size:14px;color:#6b7280;margin-top:8px;">{{ $settings['company_name'] ?? 'Solar ERP' }}</div>
    </div>

    <div style="margin-bottom:24px;font-size:14px;line-height:1.9;color:#374151;">
        This is to certify that the solar photovoltaic system has been successfully installed and commissioned at the following location:
    </div>

    <table style="width:100%;margin-bottom:24px;border-collapse:collapse;">
        <tr>
            <td style="width:35%;padding:12px;background:#f3f4f6;font-weight:700;border:1px solid #d1d5db;">Installation Number</td>
            <td style="padding:12px;border:1px solid #d1d5db;">{{ $installation->installation_number }}</td>
        </tr>
        <tr>
            <td style="padding:12px;background:#f3f4f6;font-weight:700;border:1px solid #d1d5db;">Customer Name</td>
            <td style="padding:12px;border:1px solid #d1d5db;">{{ $installation->customer->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td style="padding:12px;background:#f3f4f6;font-weight:700;border:1px solid #d1d5db;">Installation Address</td>
            <td style="padding:12px;border:1px solid #d1d5db;">{{ $installation->installation_address }}</td>
        </tr>
        <tr>
            <td style="padding:12px;background:#f3f4f6;font-weight:700;border:1px solid #d1d5db;">System Capacity</td>
            <td style="padding:12px;border:1px solid #d1d5db;">{{ $installation->system_size_kw }} kW</td>
        </tr>
        <tr>
            <td style="padding:12px;background:#f3f4f6;font-weight:700;border:1px solid #d1d5db;">Installation Date</td>
            <td style="padding:12px;border:1px solid #d1d5db;">{{ optional($installation->completed_at)->format('d M Y') ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td style="padding:12px;background:#f3f4f6;font-weight:700;border:1px solid #d1d5db;">Roof Type</td>
            <td style="padding:12px;border:1px solid #d1d5db;">{{ $installation->roof_type }}</td>
        </tr>
        <tr>
            <td style="padding:12px;background:#f3f4f6;font-weight:700;border:1px solid #d1d5db;">Net Meter Serial</td>
            <td style="padding:12px;border:1px solid #d1d5db;">{{ $installation->net_meter_serial_number ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td style="padding:12px;background:#f3f4f6;font-weight:700;border:1px solid #d1d5db;">Inverter Serial</td>
            <td style="padding:12px;border:1px solid #d1d5db;">{{ $installation->inverter_serial_number ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td style="padding:12px;background:#f3f4f6;font-weight:700;border:1px solid #d1d5db;">Installation Team</td>
            <td style="padding:12px;border:1px solid #d1d5db;">{{ $installation->assigned_team ?? 'N/A' }}</td>
        </tr>
    </table>

    <div style="margin-bottom:24px;font-size:14px;line-height:1.9;color:#374151;">
        The installation has been completed as per the approved design and all safety standards have been followed. The system has been tested and is operational.
    </div>

    <div style="margin-top:48px;display:flex;justify-content:space-between;">
        <div style="text-align:center;">
            <div style="border-top:2px solid #000;width:200px;padding-top:8px;font-size:13px;font-weight:700;">Authorized Signature</div>
            <div style="font-size:12px;color:#6b7280;margin-top:4px;">{{ $settings['company_name'] ?? 'Solar ERP' }}</div>
        </div>
        <div style="text-align:center;">
            <div style="border-top:2px solid #000;width:200px;padding-top:8px;font-size:13px;font-weight:700;">Customer Signature</div>
            <div style="font-size:12px;color:#6b7280;margin-top:4px;">{{ $installation->customer->name ?? 'N/A' }}</div>
        </div>
    </div>

    <div style="margin-top:32px;text-align:center;font-size:11px;color:#9ca3af;">
        Certificate Date: {{ now()->format('d M Y') }}
    </div>
</div>
BLADE,
        ];
    }

    public static function serviceReportStandard(): array
    {
        return [
            'label' => 'Service Report',
            'name' => 'Service Report',
            'document_type' => 'service_report',
            'paper_size' => 'A4',
            'orientation' => 'portrait',
            'header_html' => '',
            'footer_html' => '',
            'body_template' => <<<'BLADE'
<div style="padding:16px 8px;">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;border-bottom:3px solid #8b5cf6;padding-bottom:16px;margin-bottom:24px;">
        <div>
            <div style="font-size:24px;font-weight:800;">{{ $settings['company_name'] ?? 'Solar ERP' }}</div>
            <div style="font-size:12px;color:#6b7280;">Service & Maintenance</div>
            <div style="margin-top:8px;font-size:12px;color:#4b5563;line-height:1.7;">
                {{ $settings['company_phone'] ?? '' }}<br>
                {{ $settings['company_email'] ?? '' }}
            </div>
        </div>
        <div style="text-align:right;">
            <div style="font-size:28px;font-weight:900;color:#8b5cf6;">SERVICE REPORT</div>
            <div style="font-size:13px;font-weight:700;">{{ $service->service_number }}</div>
            <div style="font-size:11px;color:#6b7280;">{{ optional($service->created_at)->format('d M Y') }}</div>
        </div>
    </div>

    <table style="width:100%;margin-bottom:20px;">
        <tr>
            <td style="width:50%;padding-right:12px;vertical-align:top;">
                <div style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;">Customer Details</div>
                <div style="margin-top:8px;font-size:13px;line-height:1.8;">
                    <strong>{{ $service->customer->name ?? 'N/A' }}</strong><br>
                    {{ $service->customer->phone ?? 'N/A' }}<br>
                    {{ $service->customer->email ?? 'N/A' }}<br>
                    {{ $service->customer->address ?? 'N/A' }}
                </div>
            </td>
            <td style="width:50%;padding-left:12px;vertical-align:top;">
                <div style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;">Service Details</div>
                <div style="margin-top:8px;font-size:13px;line-height:1.8;">
                    Type: {{ ucfirst($service->service_type) }}<br>
                    Priority: {{ ucfirst($service->priority) }}<br>
                    Status: {{ ucfirst($service->status) }}<br>
                    @if($service->scheduled_date)
                    Scheduled: {{ $service->scheduled_date->format('d M Y') }}<br>
                    @endif
                    @if($service->assigned_technician)
                    Technician: {{ $service->assigned_technician }}<br>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    @if($service->issue_description)
    <div style="margin-bottom:20px;">
        <div style="font-size:13px;font-weight:700;color:#374151;margin-bottom:8px;">Issue Description:</div>
        <div style="padding:12px;background:#f9fafb;border-left:4px solid #8b5cf6;font-size:13px;line-height:1.7;color:#4b5563;">
            {{ $service->issue_description }}
        </div>
    </div>
    @endif

    @if($service->resolution_notes)
    <div style="margin-bottom:20px;">
        <div style="font-size:13px;font-weight:700;color:#374151;margin-bottom:8px;">Resolution Notes:</div>
        <div style="padding:12px;background:#f0fdf4;border-left:4px solid #10b981;font-size:13px;line-height:1.7;color:#4b5563;">
            {{ $service->resolution_notes }}
        </div>
    </div>
    @endif

    @if($service->parts_used)
    <div style="margin-bottom:20px;">
        <div style="font-size:13px;font-weight:700;color:#374151;margin-bottom:8px;">Parts Used:</div>
        <div style="padding:12px;background:#fffbeb;border-left:4px solid #f59e0b;font-size:13px;line-height:1.7;color:#4b5563;">
            {{ $service->parts_used }}
        </div>
    </div>
    @endif

    <div style="margin-top:32px;padding-top:16px;border-top:2px solid #e5e7eb;">
        <div style="font-size:11px;color:#9ca3af;text-align:center;">
            This service report was generated on {{ now()->format('d M Y H:i') }}
        </div>
    </div>
</div>
BLADE,
        ];
    }

    public static function siteVisitReportStandard(): array
    {
        return [
            'label' => 'Site Visit Report',
            'name' => 'Site Visit Report',
            'document_type' => 'site_visit_report',
            'paper_size' => 'A4',
            'orientation' => 'portrait',
            'header_html' => '',
            'footer_html' => '',
            'body_template' => <<<'BLADE'
<div style="padding:16px 8px;">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;border-bottom:3px solid #06b6d4;padding-bottom:16px;margin-bottom:24px;">
        <div>
            <div style="font-size:24px;font-weight:800;">{{ $settings['company_name'] ?? 'Solar ERP' }}</div>
            <div style="font-size:12px;color:#6b7280;">Site Survey & Assessment</div>
            <div style="margin-top:8px;font-size:12px;color:#4b5563;line-height:1.7;">
                {{ $settings['company_phone'] ?? '' }}<br>
                {{ $settings['company_email'] ?? '' }}
            </div>
        </div>
        <div style="text-align:right;">
            <div style="font-size:28px;font-weight:900;color:#06b6d4;">SITE VISIT REPORT</div>
            <div style="font-size:13px;font-weight:700;">{{ $siteVisit->visit_number }}</div>
            <div style="font-size:11px;color:#6b7280;">{{ optional($siteVisit->visit_date)->format('d M Y') }}</div>
        </div>
    </div>

    <table style="width:100%;margin-bottom:20px;">
        <tr>
            <td style="width:50%;padding-right:12px;vertical-align:top;">
                <div style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;">Customer Information</div>
                <div style="margin-top:8px;font-size:13px;line-height:1.8;">
                    <strong>{{ $siteVisit->customer_name }}</strong><br>
                    {{ $siteVisit->customer_phone }}<br>
                    {{ $siteVisit->customer_email }}<br>
                    {{ $siteVisit->site_address }}
                </div>
            </td>
            <td style="width:50%;padding-left:12px;vertical-align:top;">
                <div style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;">Visit Details</div>
                <div style="margin-top:8px;font-size:13px;line-height:1.8;">
                    Status: {{ ucfirst($siteVisit->status) }}<br>
                    Purpose: {{ ucfirst($siteVisit->visit_purpose) }}<br>
                    @if($siteVisit->assigned_technician)
                    Technician: {{ $siteVisit->assigned_technician }}<br>
                    @endif
                    @if($siteVisit->visit_date)
                    Date: {{ $siteVisit->visit_date->format('d M Y') }}<br>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    @if($siteVisit->roof_type || $siteVisit->roof_area || $siteVisit->roof_condition)
    <div style="margin-bottom:20px;">
        <div style="font-size:14px;font-weight:700;color:#374151;margin-bottom:12px;padding-bottom:6px;border-bottom:2px solid #e5e7eb;">Site Assessment</div>
        <table style="width:100%;border-collapse:collapse;">
            @if($siteVisit->roof_type)
            <tr>
                <td style="width:35%;padding:8px;background:#f9fafb;font-weight:600;border:1px solid #e5e7eb;">Roof Type</td>
                <td style="padding:8px;border:1px solid #e5e7eb;">{{ $siteVisit->roof_type }}</td>
            </tr>
            @endif
            @if($siteVisit->roof_area)
            <tr>
                <td style="padding:8px;background:#f9fafb;font-weight:600;border:1px solid #e5e7eb;">Roof Area</td>
                <td style="padding:8px;border:1px solid #e5e7eb;">{{ $siteVisit->roof_area }} sq.ft</td>
            </tr>
            @endif
            @if($siteVisit->roof_condition)
            <tr>
                <td style="padding:8px;background:#f9fafb;font-weight:600;border:1px solid #e5e7eb;">Roof Condition</td>
                <td style="padding:8px;border:1px solid #e5e7eb;">{{ ucfirst($siteVisit->roof_condition) }}</td>
            </tr>
            @endif
            @if($siteVisit->shading_analysis)
            <tr>
                <td style="padding:8px;background:#f9fafb;font-weight:600;border:1px solid #e5e7eb;">Shading Analysis</td>
                <td style="padding:8px;border:1px solid #e5e7eb;">{{ $siteVisit->shading_analysis }}</td>
            </tr>
            @endif
            @if($siteVisit->recommended_capacity)
            <tr>
                <td style="padding:8px;background:#f9fafb;font-weight:600;border:1px solid #e5e7eb;">Recommended Capacity</td>
                <td style="padding:8px;border:1px solid #e5e7eb;">{{ $siteVisit->recommended_capacity }} kW</td>
            </tr>
            @endif
        </table>
    </div>
    @endif

    @if($siteVisit->notes)
    <div style="margin-bottom:20px;">
        <div style="font-size:13px;font-weight:700;color:#374151;margin-bottom:8px;">Visit Notes:</div>
        <div style="padding:12px;background:#f0f9ff;border-left:4px solid #06b6d4;font-size:13px;line-height:1.7;color:#4b5563;">
            {{ $siteVisit->notes }}
        </div>
    </div>
    @endif

    @if($siteVisit->obstacles)
    <div style="margin-bottom:20px;">
        <div style="font-size:13px;font-weight:700;color:#374151;margin-bottom:8px;">Obstacles/Challenges:</div>
        <div style="padding:12px;background:#fef2f2;border-left:4px solid #ef4444;font-size:13px;line-height:1.7;color:#4b5563;">
            {{ $siteVisit->obstacles }}
        </div>
    </div>
    @endif

    <div style="margin-top:32px;padding-top:16px;border-top:2px solid #e5e7eb;">
        <div style="font-size:11px;color:#9ca3af;text-align:center;">
            Report generated on {{ now()->format('d M Y H:i') }}
        </div>
    </div>
</div>
BLADE,
        ];
    }

    public static function quotationPdfReplica(): array
    {
        return [
            'label' => 'Custom Quotation',
            'name' => 'Custom Quotation',
            'document_type' => 'quotation',
            'paper_size' => 'A4',
            'orientation' => 'portrait',
            'header_html' => '',
            'footer_html' => '',
            'body_template' => <<<'BLADE'
<style>
    .quotation-replica {
        color: #122844;
        font-family: "Arial", sans-serif;
    }

    .quotation-replica * {
        box-sizing: border-box;
    }

    .quotation-replica .page {
        position: relative;
        page-break-after: always;
        padding: 18px 22px 22px;
        background: #ffffff;
        overflow: hidden;
    }

    .quotation-replica .page:last-child {
        page-break-after: auto;
    }

    .quotation-replica .cover-page {
        padding-top: 34px;
    }

    .quotation-replica .meta-top {
        display: table;
        width: 100%;
        margin-bottom: 14px;
    }

    .quotation-replica .meta-top .meta-left,
    .quotation-replica .meta-top .meta-right {
        display: table-cell;
        vertical-align: top;
    }

    .quotation-replica .meta-top .meta-right {
        text-align: right;
        font-size: 14px;
        font-weight: 700;
        line-height: 1.65;
        color: #4b5563;
    }

    .quotation-replica .logo {
        width: 138px;
        max-height: 92px;
        object-fit: contain;
    }

    .quotation-replica .watermark {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 300px;
        max-width: 56%;
        transform: translate(-50%, -50%);
        opacity: 0.2;
        z-index: 0;
        pointer-events: none;
    }

    .quotation-replica .cover-title {
        position: relative;
        z-index: 1;
        margin-top: 2px;
        text-align: center;
        font-size: 46px;
        line-height: 1;
        font-weight: 800;
        letter-spacing: -1px;
    }

    .quotation-replica .rule {
        height: 2px;
        position: relative;
        z-index: 1;
        margin: 10px 0 14px;
        background: #cedded;
    }

    .quotation-replica .cover-name,
    .quotation-replica .cover-location,
    .quotation-replica .cover-system {
        text-align: center;
        text-transform: uppercase;
        font-weight: 800;
    }

    .quotation-replica .cover-name {
        position: relative;
        z-index: 1;
        font-size: 25px;
        margin-bottom: 6px;
    }

    .quotation-replica .cover-location {
        position: relative;
        z-index: 1;
        font-size: 21px;
        margin-bottom: 14px;
    }

    .quotation-replica .cover-system {
        position: relative;
        z-index: 1;
        font-size: 20px;
        margin-bottom: 10px;
    }

    .quotation-replica .company-strip {
        position: relative;
        z-index: 1;
        text-align: center;
        margin-bottom: 12px;
    }

    .quotation-replica .company-strip .name {
        font-size: 16px;
        font-weight: 800;
    }

    .quotation-replica .company-strip .tagline {
        margin-top: 5px;
        font-size: 13px;
        font-weight: 600;
        font-style: italic;
        color: #4b5563;
    }

    .quotation-replica .hero-image,
    .quotation-replica .banner-image,
    .quotation-replica .handshake-image {
        position: relative;
        z-index: 1;
        width: 100%;
        display: block;
    }

    .quotation-replica .hero-image {
        margin: 0 auto;
        max-width: 90%;
    }

    .quotation-replica .banner-image {
        margin-top: 16px;
    }

    .quotation-replica .handshake-image {
        margin-bottom: 14px;
    }

    .quotation-replica .table-title {
        position: relative;
        z-index: 1;
        background: #17283c;
        color: #ffffff;
        text-transform: uppercase;
        text-align: center;
        font-weight: 800;
        font-size: 16px;
        padding: 10px 14px;
        letter-spacing: .3px;
    }

    .quotation-replica table {
        position: relative;
        z-index: 1;
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .quotation-replica th,
    .quotation-replica td {
        border: 1px solid #6d88aa;
        padding: 7px 7px;
        vertical-align: middle;
        word-wrap: break-word;
    }

    .quotation-replica .terms-table th,
    .quotation-replica .terms-table td {
        border-color: #6dd640;
    }

    .quotation-replica thead th {
        background: #f9fbff;
        font-size: 12px;
        font-weight: 800;
        text-align: center;
        color: #1f2937;
    }

    .quotation-replica tbody td {
        font-size: 12px;
        color: #2d3748;
    }

    .quotation-replica .center {
        text-align: center;
    }

    .quotation-replica .right {
        text-align: right;
    }

    .quotation-replica .small-note {
        font-size: 11px;
        line-height: 1.45;
        color: #4b5563;
    }

    .quotation-replica .price-table td {
        font-size: 14px;
        font-weight: 700;
        padding: 10px 10px;
    }

    .quotation-replica .price-table .amount {
        width: 34%;
        text-align: right;
    }

    .quotation-replica .special-note-title,
    .quotation-replica .bank-title,
    .quotation-replica .company-detail-title {
        text-align: center;
        font-weight: 800;
        text-transform: uppercase;
    }

    .quotation-replica .special-note-title {
        position: relative;
        z-index: 1;
        margin: 18px 0 8px;
        font-size: 17px;
    }

    .quotation-replica .special-notes {
        position: relative;
        z-index: 1;
        margin: 0;
        padding-left: 20px;
    }

    .quotation-replica .special-notes li {
        margin-bottom: 8px;
        font-size: 12px;
        line-height: 1.4;
    }

    .quotation-replica .terms-table td {
        font-size: 12px;
        line-height: 1.45;
        padding: 12px 10px;
    }

    .quotation-replica .bank-title,
    .quotation-replica .company-detail-title {
        position: relative;
        z-index: 1;
        color: #19356f;
        text-decoration: underline;
        font-size: 17px;
    }

    .quotation-replica .bank-panel {
        position: relative;
        z-index: 1;
        margin-top: 20px;
        display: table;
        width: 100%;
    }

    .quotation-replica .bank-left,
    .quotation-replica .bank-right {
        display: table-cell;
        vertical-align: top;
    }

    .quotation-replica .bank-left {
        width: 68%;
        padding-right: 16px;
    }

    .quotation-replica .bank-right {
        width: 32%;
        text-align: center;
    }

    .quotation-replica .bank-name {
        font-size: 24px;
        line-height: 1.25;
        font-weight: 800;
        color: #71337a;
        text-transform: uppercase;
        margin-bottom: 14px;
    }

    .quotation-replica .bank-detail {
        font-size: 18px;
        line-height: 1.5;
        font-weight: 800;
        color: #1f3a79;
        text-transform: uppercase;
    }

    .quotation-replica .bank-logo {
        width: 100%;
        max-width: 180px;
        margin: 0 auto 12px;
    }

    .quotation-replica .bank-qr {
        width: 100%;
        max-width: 180px;
        margin: 10px auto 0;
    }

    .quotation-replica .contact-rule {
        height: 2px;
        background: #7d3b88;
        margin: 18px 0 12px;
    }

    .quotation-replica .gst-line {
        position: relative;
        z-index: 1;
        text-align: center;
        margin-top: 6px;
        font-size: 13px;
        font-weight: 700;
        color: #344c83;
        text-transform: uppercase;
    }

    .quotation-replica .contact-line {
        position: relative;
        z-index: 1;
        margin-top: 12px;
        font-size: 15px;
        line-height: 1.6;
        color: #28467a;
        font-weight: 700;
    }

    .quotation-replica .address-line,
    .quotation-replica .system-note {
        position: relative;
        z-index: 1;
        margin-top: 12px;
        text-align: center;
        font-size: 12px;
        line-height: 1.5;
        font-weight: 700;
        color: #344c83;
        text-transform: uppercase;
    }

    .quotation-replica .system-note {
        font-size: 11px;
        margin-top: 14px;
        text-transform: none;
    }
</style>

@php
    $logoPath = !empty($settings['company_logo'])
        ? asset('storage/' . $settings['company_logo'])
        : asset('images/print-format-presets/quotation-logo.png');
    $quotationDate = optional($quotation->created_at)->format('d-m-Y') ?? now()->format('d-m-Y');
    $quotationNumber = $quotation->quotation_number ?? 'Q-000';
    $customerName = strtoupper($quotation->customer_name ?? 'CUSTOMER NAME');
    $customerLocation = strtoupper($quotation->customer_address ?: 'PROJECT LOCATION');
    $systemSummary = strtoupper(trim(($quotation->notes ?: ($settings['quotation_system_type'] ?? 'ON GRID - DOMESTIC')) . ' - ' . ($settings['quotation_system_capacity'] ?? '5 KW')));
    $companyName = $settings['company_name'] ?? 'Rajasthan Green Energy Solar Power Pvt. Ltd.';
    $companyTagline = $settings['company_tagline'] ?? 'Domestic Rooftop Solar Solutions For Smart & Sustainable Homes';
    $bomItems = collect($quotation->bom_items ?? []);
    $priceRows = [
        ['label' => 'COMPLETE COST OF SOLAR SYSTEM', 'value' => number_format((float) ($quotation->total_amount ?? 0), 2)],
        ['label' => 'COST OF STRUCTURE', 'value' => 'INCLUDING'],
        ['label' => 'GST', 'value' => (float) ($quotation->tax_amount ?? 0) > 0 ? number_format((float) $quotation->tax_amount, 2) : 'INCLUDING'],
        ['label' => 'TOTAL SYSTEM COST WITH GST', 'value' => number_format((float) ($quotation->final_amount ?? 0), 2)],
        ['label' => 'SUBSIDY', 'value' => !empty($settings['quotation_subsidy_amount']) ? number_format((float) $settings['quotation_subsidy_amount'], 2) : 'AS APPLICABLE'],
    ];
    $specialNotes = array_values(array_filter([
        $settings['quotation_special_note_1'] ?? 'Actual capacity of the system may vary during detailed engineering, in which case the project cost will be computed on a per watt basis based on this proposal.',
        $settings['quotation_special_note_2'] ?? 'As per applicable guidelines, solar PV system capacity may depend on site transformer and connection conditions. Any issue arising from site availability remains in client scope.',
        $settings['quotation_special_note_3'] ?? 'Cleaning of the solar plant remains in the scope of the client.',
        $settings['quotation_special_note_4'] ?? 'The quoted amount includes survey, layout planning, load analysis, design, development, supply, erection and commissioning of the solar PV power system.',
        $settings['quotation_special_note_5'] ?? 'Depreciation benefits, if any, shall be applicable as per actual policy and tax treatment.',
    ]));
    $termsRows = [
        ['title' => 'Payment Terms', 'content' => $settings['quotation_payment_terms'] ?? "30% Advance.\n65% after structure delivered at site and before module & inverter dispatch.\n5% after solar and net meter installation."],
        ['title' => 'Client Scope', 'content' => $settings['quotation_client_scope'] ?? "Site-specific considerations will require assistance and cooperation from the client.\nProvide access to work site before delivery of equipment and materials.\nFacilitate access of work crew to the site for project execution.\nAll civil materials will be provided by client scope."],
        ['title' => 'Vendor Scope', 'content' => $settings['quotation_vendor_scope'] ?? "Prepare full system design including civil, structural, electrical and mechanical components with construction drawings.\nProcure equipment and materials and deliver to site.\nPerform complete system installation and commissioning.\nAssist for coordination with nodal agencies where applicable."],
        ['title' => 'Project Completion', 'content' => $settings['quotation_project_completion'] ?? '30-45 days from the date of receipt of solar NOC or commercial clearance order and advance payment.'],
        ['title' => 'Net-Metering', 'content' => $settings['quotation_net_metering'] ?? 'Customer shall bear utility or government charges for increasing load and related approvals required for net metering.'],
        ['title' => 'Solar System Warranty', 'content' => $settings['quotation_warranty'] ?? 'Solar module performance warranty: 25-30 years. Solar grid tie inverter: 7-10 years (as per manufacturer norms).'],
        ['title' => 'Validity of Offer', 'content' => $settings['quotation_offer_validity'] ?? '15 days from the date of offer. After this period, confirmation from the vendor is required before proceeding.'],
    ];
@endphp

<div class="quotation-replica">
    <section class="page cover-page">
        <img src="{{ $logoPath }}" alt="Watermark Logo" class="watermark">
        <div class="meta-top">
            <div class="meta-left">
                <img src="{{ $logoPath }}" alt="Company Logo" class="logo">
            </div>
            <div class="meta-right">
                <div>DATE: {{ $quotationDate }}</div>
                <div>Q. No. - {{ $quotationNumber }}</div>
            </div>
        </div>

        <div class="cover-title">QUOTATION</div>
        <div class="rule"></div>

        <div class="cover-name">{{ $customerName }}</div>
        <div class="cover-location">{{ $customerLocation }}</div>
        <div class="cover-system">{{ $systemSummary }}</div>

        <div class="rule"></div>

        <div class="company-strip">
            <div class="name">{{ $companyName }}</div>
            <div class="tagline">{{ $companyTagline }}</div>
        </div>

        <img src="{{ asset('images/print-format-presets/quotation-cover-house.png') }}" alt="Solar House" class="hero-image">
    </section>

    <section class="page">
        <img src="{{ $logoPath }}" alt="Watermark Logo" class="watermark">
        <div class="table-title">BILL OF MATERIAL - ON GRID SOLAR SYSTEM</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 8%;">S. No.</th>
                    <th style="width: 40%;">Description of the Item</th>
                    <th style="width: 22%;">Make</th>
                    <th style="width: 14%;">Unit</th>
                    <th style="width: 16%;">Quantity</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bomItems as $index => $bom)
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>
                        <td>
                            {{ $bom['description'] ?? 'Solar Component' }}
                            @if(!empty($bom['details']))
                                <div class="small-note">{{ $bom['details'] }}</div>
                            @endif
                        </td>
                        <td class="center">{{ $bom['make'] ?? 'As Per Standard' }}</td>
                        <td class="center">{{ $bom['unit'] ?? 'Nos.' }}</td>
                        <td class="center">{{ $bom['quantity'] ?? '1' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td class="center">1</td>
                        <td>Solar PV Module<div class="small-note">(25-30 years linear power warranty)</div></td>
                        <td class="center">As Per Standard</td>
                        <td class="center">Wp</td>
                        <td class="center">As per site request</td>
                    </tr>
                    <tr>
                        <td class="center">2</td>
                        <td>On Grid Inverter<div class="small-note">(Warranty as per manufacturer)</div></td>
                        <td class="center">As Per Standard</td>
                        <td class="center">KW</td>
                        <td class="center">1 Nos.</td>
                    </tr>
                    <tr>
                        <td class="center">3</td>
                        <td>Mounting Structure</td>
                        <td class="center">GI Standard</td>
                        <td class="center">Set</td>
                        <td class="center">As per site request</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <section class="page">
        <img src="{{ $logoPath }}" alt="Watermark Logo" class="watermark">
        <div class="table-title">DOMESTIC OFFER PRICE</div>
        <table class="price-table">
            <tbody>
                @foreach($priceRows as $index => $row)
                    <tr>
                        <td class="center" style="width: 10%;">{{ $index + 1 }}</td>
                        <td>{{ $row['label'] }}</td>
                        <td class="amount">{{ $row['value'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="special-note-title">SPECIAL NOTE</div>
        <ul class="special-notes">
            @foreach($specialNotes as $note)
                <li>{{ $note }}</li>
            @endforeach
        </ul>

        <img src="{{ asset('images/print-format-presets/quotation-roof-banner.png') }}" alt="Solar Roof" class="banner-image">
    </section>

    <section class="page">
        <img src="{{ $logoPath }}" alt="Watermark Logo" class="watermark">
        <div class="table-title">TERMS &amp; CONDITIONS</div>
        <table class="terms-table">
            <tbody>
                @foreach($termsRows as $index => $term)
                    <tr>
                        <td class="center" style="width: 8%;">{{ $index + 1 }}</td>
                        <td class="center" style="width: 27%; font-weight: 700;">{{ $term['title'] }}</td>
                        <td style="white-space: pre-line;">{{ $term['content'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>

    <section class="page">
        <img src="{{ $logoPath }}" alt="Watermark Logo" class="watermark">
        <img src="{{ asset('images/print-format-presets/quotation-handshake.png') }}" alt="Handshake" class="handshake-image">

        <div class="bank-title">OUR COMPANY BANKING DETAIL</div>

        <div class="bank-panel">
            <div class="bank-left">
                <div class="bank-name">BANK NAME: {{ strtoupper($settings['bank_name'] ?? 'AUSMALL FINANCE BANK') }}</div>
                <div class="bank-detail">ACCOUNT NO : {{ $settings['bank_account_number'] ?? '4444414141414141' }}</div>
                <div class="bank-detail">IFSC CODE : {{ strtoupper($settings['bank_ifsc'] ?? 'AUBL0002444') }}</div>
            </div>
        </div>

        <div class="contact-rule"></div>
        <div class="company-detail-title">OUR COMPANY DETAIL</div>
        <div class="gst-line">GSTIN NO. {{ strtoupper($settings['gst_number'] ?? 'GST NUMBER HERE') }}</div>

        <div class="contact-line">
            {{ $settings['company_phone'] ?? '+91-9785277913' }}<br>
            {{ $settings['company_email'] ?? 'headoffice@rajasthangreensolar.com' }}<br>
            {{ $settings['company_website'] ?? 'www.rajasthanenergy.com' }}
        </div>

        <div class="address-line">{{ $settings['company_address'] ?? 'Second Floor, BL Tower, Arihant Nagar, Kalwar Road, Hatoj, Jaipur (Rajasthan) 302012' }}</div>
        <div class="system-note">This is a system-generated quotation and does not require any signature or stamp.</div>
    </section>
</div>
BLADE,
        ];
    }

    public static function salarySlipStandard(): array
    {
        return [
            'label' => 'Standard Salary Slip',
            'name' => 'Standard Salary Slip',
            'document_type' => 'salary_slip',
            'paper_size' => 'A4',
            'orientation' => 'portrait',
            'header_html' => '',
            'footer_html' => '<div style="text-align: center; font-size: 10px; color: #666; margin-top: 20px; padding-top: 10px; border-top: 1px solid #ddd;">
                <p>This is a computer-generated salary slip and does not require a signature.</p>
                <p>For any queries, please contact HR Department</p>
            </div>',
            'body_template' => <<<'BLADE'
<style>
    .salary-slip {
        font-family: Arial, sans-serif;
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        color: #333;
    }
    
    .salary-slip .header {
        text-align: center;
        margin-bottom: 30px;
        border-bottom: 3px solid #4F46E5;
        padding-bottom: 20px;
    }
    
    .salary-slip .company-name {
        font-size: 24px;
        font-weight: bold;
        color: #4F46E5;
        margin-bottom: 5px;
    }
    
    .salary-slip .document-title {
        font-size: 18px;
        font-weight: bold;
        color: #666;
        margin-top: 10px;
    }
    
    .salary-slip .info-section {
        display: table;
        width: 100%;
        margin-bottom: 20px;
    }
    
    .salary-slip .info-row {
        display: table-row;
    }
    
    .salary-slip .info-label {
        display: table-cell;
        padding: 8px;
        font-weight: bold;
        width: 40%;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
    }
    
    .salary-slip .info-value {
        display: table-cell;
        padding: 8px;
        border: 1px solid #dee2e6;
    }
    
    .salary-slip .earnings-deductions {
        display: flex;
        gap: 20px;
        margin: 30px 0;
    }
    
    .salary-slip .earnings, .salary-slip .deductions {
        flex: 1;
    }
    
    .salary-slip .section-title {
        background: #4F46E5;
        color: white;
        padding: 10px;
        font-weight: bold;
        text-align: center;
        margin-bottom: 10px;
    }
    
    .salary-slip .deductions .section-title {
        background: #DC2626;
    }
    
    .salary-slip .line-item {
        display: flex;
        justify-content: space-between;
        padding: 8px;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .salary-slip .line-item:hover {
        background: #f9fafb;
    }
    
    .salary-slip .total-row {
        display: flex;
        justify-content: space-between;
        padding: 12px;
        background: #f3f4f6;
        font-weight: bold;
        font-size: 16px;
        margin-top: 10px;
        border: 2px solid #4F46E5;
    }
    
    .salary-slip .net-salary {
        text-align: center;
        margin-top: 30px;
        padding: 20px;
        background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
        color: white;
        border-radius: 10px;
    }
    
    .salary-slip .net-salary-label {
        font-size: 14px;
        margin-bottom: 5px;
    }
    
    .salary-slip .net-salary-amount {
        font-size: 32px;
        font-weight: bold;
    }
    
    .salary-slip .net-salary-words {
        font-size: 12px;
        margin-top: 10px;
        font-style: italic;
    }
    
    .salary-slip .signature-section {
        margin-top: 50px;
        display: flex;
        justify-content: space-between;
    }
    
    .salary-slip .signature-box {
        text-align: center;
        padding-top: 40px;
        border-top: 1px solid #333;
        width: 200px;
    }
    
    .salary-slip .notes {
        margin-top: 30px;
        padding: 15px;
        background: #FEF3C7;
        border-left: 4px solid #F59E0B;
        font-size: 12px;
    }
</style>

<div class="salary-slip">
    <div class="header">
        <div class="company-name">{{ $settings['company_name'] ?? 'Company Name' }}</div>
        <div>{{ $settings['company_address'] ?? 'Company Address' }}</div>
        <div>{{ $settings['company_phone'] ?? 'Phone' }} | {{ $settings['company_email'] ?? 'Email' }}</div>
        <div class="document-title">SALARY SLIP</div>
    </div>
    
    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Employee Name:</div>
            <div class="info-value">{{ $record->employee->name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Employee Code:</div>
            <div class="info-value">{{ $record->employee->employee_code }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Department:</div>
            <div class="info-value">{{ ucfirst($record->employee->department) }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Designation:</div>
            <div class="info-value">{{ $record->employee->designation }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Pay Period:</div>
            <div class="info-value">{{ date('F Y', mktime(0, 0, 0, $record->month, 1, $record->year)) }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Payment Date:</div>
            <div class="info-value">{{ \Carbon\Carbon::parse($record->payment_date)->format('d M, Y') }}</div>
        </div>
    </div>
    
    <div class="earnings-deductions">
        <div class="earnings">
            <div class="section-title">EARNINGS</div>
            <div class="line-item">
                <span>Basic Salary</span>
                <span>₹{{ number_format($record->basic_salary, 2) }}</span>
            </div>
            @if($record->allowances > 0)
            <div class="line-item">
                <span>Allowances</span>
                <span>₹{{ number_format($record->allowances, 2) }}</span>
            </div>
            @endif
            <div class="total-row">
                <span>Total Earnings</span>
                <span>₹{{ number_format($record->basic_salary + $record->allowances, 2) }}</span>
            </div>
        </div>
        
        <div class="deductions">
            <div class="section-title">DEDUCTIONS</div>
            @if($record->deductions > 0)
            <div class="line-item">
                <span>Deductions</span>
                <span>₹{{ number_format($record->deductions, 2) }}</span>
            </div>
            @else
            <div class="line-item">
                <span>No Deductions</span>
                <span>₹0.00</span>
            </div>
            @endif
            <div class="total-row">
                <span>Total Deductions</span>
                <span>₹{{ number_format($record->deductions, 2) }}</span>
            </div>
        </div>
    </div>
    
    <div class="net-salary">
        <div class="net-salary-label">NET SALARY</div>
        <div class="net-salary-amount">₹{{ number_format($record->net_salary, 2) }}</div>
        <div class="net-salary-words">{{ ucwords(\App\Support\NumberToWords::convert($record->net_salary)) }} Only</div>
    </div>
    
    @if($record->notes)
    <div class="notes">
        <strong>Notes:</strong> {{ $record->notes }}
    </div>
    @endif
    
    <div class="signature-section">
        <div class="signature-box">
            <div>Employee Signature</div>
        </div>
        <div class="signature-box">
            <div>Authorized Signatory</div>
        </div>
    </div>
</div>
BLADE
        ];
    }
}
