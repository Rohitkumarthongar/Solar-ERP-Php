<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>DCR Certificate - {{ $installation->customer->name }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.4; margin: 0; padding: 30px; }
        .header { text-align: center; border-bottom: 3px double #065f46; padding-bottom: 15px; margin-bottom: 25px; }
        .header h1 { margin: 0; color: #065f46; font-size: 22px; text-transform: uppercase; letter-spacing: 1px; }
        .header p { margin: 5px 0; color: #059669; font-weight: bold; font-size: 14px; }
        
        .certificate-title { text-align: center; margin-bottom: 30px; }
        .certificate-title h2 { margin: 0; font-size: 18px; color: #111827; text-decoration: underline; }
        .ref-no { float: right; font-size: 12px; font-weight: bold; }
        
        .section { margin-bottom: 20px; clear: both; }
        .section-title { background: #f0fdf4; padding: 6px 12px; border-left: 4px solid #059669; font-weight: bold; color: #064e3b; text-transform: uppercase; font-size: 12px; margin-bottom: 10px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table td { padding: 5px 0; vertical-align: top; font-size: 12px; }
        .label { color: #6b7280; width: 35%; }
        .value { color: #111827; font-weight: bold; width: 65%; }
        
        .grid { display: block; }
        .col { width: 50%; float: left; }
        .clearfix::after { content: ""; clear: both; display: table; }
        
        .data-table { width: 100%; border: 1px solid #e5e7eb; margin-top: 5px; }
        .data-table th { background: #f9fafb; border: 1px solid #e5e7eb; padding: 8px; text-align: left; font-size: 11px; color: #374151; text-transform: uppercase; }
        .data-table td { border: 1px solid #e5e7eb; padding: 8px; font-size: 11px; }
        
        .declaration { margin-top: 30px; padding: 15px; background: #fafafa; border: 1px solid #f3f4f6; font-size: 11px; font-style: italic; color: #4b5563; text-align: justify; }
        
        .signature-section { margin-top: 60px; }
        .sig-box { width: 200px; float: right; text-align: center; border-top: 1px solid #333; padding-top: 5px; font-size: 12px; font-weight: bold; }
        
        .footer { position: fixed; bottom: 0; left: 0; right: 0; border-top: 1px solid #e5e7eb; padding-top: 10px; font-size: 9px; color: #9ca3af; text-align: center; }
        
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h1>DCR CERTIFICATE</h1>
        <p>{{ $settings['company_name'] ?? 'PALAWAT TRADING COMPANY' }}</p>
        <div style="font-size: 10px; font-weight: normal; color: #6b7280;">{{ $settings['company_address'] ?? '' }}</div>
    </div>

    <div class="certificate-title">
        <div class="ref-no">Ref: DCR/{{ $installation->installation_number }}/{{ date('Y') }}</div>
        <h2>Domestic Content Requirement (DCR) Compliance Certificate</h2>
    </div>

    <div class="section">
        <div class="section-title">Customer & DISCOM Details</div>
        <div class="clearfix">
            <div class="col">
                <table>
                    <tr><td class="label">Customer Name:</td><td class="value">{{ $installation->customer->name }}</td></tr>
                    <tr><td class="label">Customer Phone:</td><td class="value">{{ $installation->customer->phone }}</td></tr>
                    <tr><td class="label">Site Address:</td><td class="value">{{ $installation->installation_address }}</td></tr>
                </table>
            </div>
            <div class="col">
                <table>
                    @if($installation->customer->discom)
                    <tr><td class="label">DISCOM Name:</td><td class="value">{{ $installation->customer->discom->discom_name }}</td></tr>
                    <tr><td class="label">K-Number:</td><td class="value">{{ $installation->customer->discom->k_number }}</td></tr>
                    <tr><td class="label">Sanctioned Load:</td><td class="value">{{ $installation->customer->discom->sanctioned_load }} kW</td></tr>
                    <tr><td class="label">Application No:</td><td class="value">{{ $installation->customer->discom->application_number ?: 'N/A' }}</td></tr>
                    @else
                    <tr><td colspan="2" style="color:red">DISCOM details not found.</td></tr>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">System Configuration</div>
        <table>
            <tr>
                <td class="label">Total System Size:</td><td class="value">{{ $installation->system_size_kw }} kWp</td>
                <td class="label">Installation Date:</td><td class="value">{{ $installation->completion_date ? $installation->completion_date->format('d M Y') : 'Ongoing' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Solar PV Module (Panel) Details</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Serial Number</th>
                    <th>Module Make / Manufacturer</th>
                    <th>Wattage (Wp)</th>
                    <th>Source (DCR)</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($installation->panel_serial_details))
                    @foreach($installation->panel_serial_details as $panel)
                    <tr>
                        <td>{{ $panel['serial_number'] ?? 'N/A' }}</td>
                        <td>{{ $panel['module_make'] ?? 'N/A' }}</td>
                        <td>{{ $panel['wattage'] ?? 'N/A' }}W</td>
                        <td>Domestic / India</td>
                    </tr>
                    @endforeach
                @else
                    <tr><td colspan="4" style="text-align:center">No panel details recorded.</td></tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Solar Inverter Details</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Serial Number</th>
                    <th>Make / Brand</th>
                    <th>Capacity</th>
                    <th>Phase Type</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($installation->inverter_serial_details))
                    @foreach($installation->inverter_serial_details as $inverter)
                    <tr>
                        <td>{{ $inverter['serial_number'] ?? 'N/A' }}</td>
                        <td>{{ $inverter['make'] ?? 'N/A' }}</td>
                        <td>{{ $inverter['capacity'] ?? 'N/A' }}</td>
                        <td>{{ $inverter['phase'] ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                @else
                    <tr><td colspan="4" style="text-align:center">No inverter details recorded.</td></tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="declaration">
        <strong>Declaration:</strong> This is to certify that the Solar PV modules used in the rooftop solar power project of the above-mentioned customer adhere to the Domestic Content Requirement (DCR) as specified by the Ministry of New and Renewable Energy (MNRE). The solar cells and modules used in this project are manufactured in India. All technical specifications mentioned above are verified as per the actual installation at site.
    </div>

    <div class="signature-section clearfix">
        <div class="sig-box" style="float: left;">
            Technician / Engineer Signature
        </div>
        <div class = "sig-box">
            Authorized Signatory<br>
            {{ $settings['company_name'] ?? 'Kodaic.cloud' }}
        </div>
    </div>

    <div class="footer">
        This is a computer-generated DCR Certificate for {{ $installation->installation_number }}. Printed on {{ date('d M Y H:i') }}.
    </div>
</body>
</html>
