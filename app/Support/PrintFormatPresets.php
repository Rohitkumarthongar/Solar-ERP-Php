<?php

namespace App\Support;

class PrintFormatPresets
{
    public static function all(): array
    {
        return [
            'quotation_pdf_replica' => self::quotationPdfReplica(),
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
            <div class="bank-right">
                <img src="{{ asset('images/print-format-presets/quotation-bank-qr.png') }}" alt="Bank QR" class="bank-qr">
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
}
