<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>DISCOM Application - {{ $discom->customer->name }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.5; margin: 0; padding: 40px; }
        .header { text-align: center; border-bottom: 2px solid #4f46e5; pb-20px; margin-bottom: 30px; }
        .header h1 { margin: 0; color: #1e1b4b; font-size: 24px; text-transform: uppercase; }
        .header p { margin: 5px 0; color: #6366f1; font-weight: bold; }
        
        .section { margin-bottom: 25px; }
        .section-title { background: #f8fafc; padding: 8px 15px; border-left: 4px solid #4f46e5; font-weight: bold; color: #1e1b4b; text-transform: uppercase; font-size: 14px; margin-bottom: 15px; }
        
        table { width: 100%; border-collapse: collapse; }
        table td { padding: 8px 0; vertical-align: top; font-size: 13px; }
        .label { color: #64748b; width: 40%; font-weight: 500; }
        .value { color: #1e293b; font-weight: bold; width: 60%; }
        
        .grid { display: block; }
        .col { width: 50%; float: left; }
        .clearfix::after { content: ""; clear: both; display: table; }
        
        .dynamic-table { margin-top: 10px; }
        .dynamic-table th { text-align: left; background: #f1f5f9; padding: 8px; font-size: 12px; color: #475569; }
        .dynamic-table td { border-bottom: 1px solid #f1f5f9; padding: 8px; font-size: 12px; }
        
        .footer { margin-top: 50px; border-top: 1px solid #e2e8f0; padding-top: 20px; font-size: 10px; color: #94a3b8; text-align: center; }
        
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="clearfix">
        <div style="float: left; width: 60%;">
            <p><strong>To:</strong><br>
            The Assistant Engineer,<br>
            {{ $discom->discom_name }},<br>
            {{ $discom->customer->city ?? 'Local Office' }}</p>
        </div>
        <div style="float: right; width: 30%; text-align: right;">
            <p><strong>Date:</strong> {{ date('F d, Y') }}</p>
        </div>
    </div>

    <div style="margin-top: 30px; border-top: 2px solid #1e1b4b; padding-top: 20px;">
        <p><strong>Subject: Application for Meter Testing and Integration of Solar Rooftop System ({{ $discom->required_load_kw }} kW)</strong></p>
    </div>

    <div class="letter-content" style="margin-top: 30px; font-size: 14px; text-align: justify;">
        <p>Respected Sir/Madam,</p>

        <p>I am a registered consumer of <strong>{{ $discom->discom_name }}</strong> under the account/service number <strong>{{ $discom->k_number }}</strong>. My premises are located at <strong>{{ $discom->customer->address }}</strong>.
        @if($discom->meter_number)
            Current Meter No: <strong>{{ $discom->meter_number }}</strong>.
        @endif
        @if($discom->application_number)
            Application Ref No: <strong>{{ $discom->application_number }}</strong>.
        @endif
        </p>

        <p>I have recently installed / intend to install a Grid-Tied Solar Rooftop System with a capacity of <strong>{{ $discom->required_load_kw }} kW</strong> under the net metering scheme. To proceed with the official commissioning and synchronization of the solar plant with your distribution grid, I request the following:</p>

        <ul style="margin-left: 20px;">
            <li style="margin-bottom: 15px;"><strong>Meter Testing:</strong> I request the technical team to conduct a standard accuracy and functional test of my existing electricity meter to ensure it is compatible with a solar setup.</li>
            <li style="margin-bottom: 15px;"><strong>Net-Meter Installation:</strong> If the current meter is not bi-directional, I request the installation of a certified Net-Meter to record both import and export of power.</li>
        </ul>

        <p>I have attached the technical specifications of the solar inverter and panels, along with the solar installation certificate from the empaneled vendor ({{ $settings['company_name'] ?? 'Kodaic.cloud' }}). I am ready to pay the prescribed testing fees as per the board's current tariff schedule.</p>

        <p>Kindly depute an official to inspect the site and perform the necessary testing at your earliest convenience.</p>

        <div style="margin-top: 60px;">
            <p>Yours faithfully,</p>
            <div style="margin-top: 40px; border-bottom: 1px solid #333; width: 200px;"></div>
            <p>(Signature)</p>
            <p><strong>{{ $discom->customer->name }}</strong><br>
            Phone: {{ $discom->customer->phone }}<br>
            Email: {{ $discom->customer->email ?: 'N/A' }}</p>
        </div>
    </div>

    @if($discom->application_data && count($discom->application_data) > 0)
    <div style="page-break-before: always;"></div>
    <div class="section">
        <div class="section-title">Technical Specifications & Field Data</div>
        <table class="dynamic-table">
            <thead>
                <tr>
                    <th>Parameter / Field Name</th>
                    <th>Recorded Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach($discom->application_data as $key => $value)
                <tr>
                    <td>{{ str_replace('_', ' ', ucwords($key, '_')) }}</td>
                    <td><strong>{{ $value }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        Generated for {{ $discom->customer->name }} | {{ $settings['company_name'] ?? 'Kodaic.cloud' }}
    </div>
</body>
</html>
