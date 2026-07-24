<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Work Completion Application - {{ $installation->customer->name }}</title>
    <style>
        @page { margin: 15mm; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1e293b; line-height: 1.5; margin: 0; padding: 0; font-size: 12px; }
        .toolbar { position: sticky; top: 0; z-index: 20; background: rgba(15, 23, 42, 0.96); color: #fff; padding: 14px 18px; display: flex; align-items: center; justify-content: space-between; gap: 16px; }
        .toolbar-title { font-size: 13px; font-weight: 700; letter-spacing: 0.04em; }
        .toolbar-actions { display: flex; gap: 10px; flex-wrap: wrap; }
        .toolbar-actions a,
        .toolbar-actions button { border: 0; border-radius: 999px; padding: 10px 16px; font-size: 12px; font-weight: 700; cursor: pointer; text-decoration: none; }
        .btn-download { background: #f59e0b; color: #1f2937; }
        .btn-print { background: #e2e8f0; color: #0f172a; }
        .document-shell { padding: 24px; }
        .header { text-align: center; border-bottom: 3px solid #f59e0b; padding-bottom: 15px; margin-bottom: 25px; }
        .header h1 { margin: 0; color: #78350f; font-size: 22px; text-transform: uppercase; letter-spacing: 1px; }
        .header p { margin: 5px 0; color: #b45309; font-weight: bold; font-size: 14px; }
        
        .section { margin-bottom: 20px; }
        .section-title { background: #fffbeb; padding: 6px 12px; border-left: 4px solid #f59e0b; font-weight: bold; color: #92400e; text-transform: uppercase; font-size: 11px; margin-bottom: 12px; letter-spacing: 0.5px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table td { padding: 6px 4px; vertical-align: top; }
        .label { color: #64748b; width: 30%; font-weight: 500; font-size: 11px; }
        .value { color: #1e293b; font-weight: bold; width: 70%; border-bottom: 1px solid #f1f5f9; }
        
        .grid { display: block; width: 100%; }
        .col { width: 48%; display: inline-block; vertical-align: top; }
        .col-spacer { width: 4%; display: inline-block; }
        
        .data-table { width: 100%; border: 1px solid #e2e8f0; margin-top: 5px; }
        .data-table th { background: #f8fafc; border: 1px solid #e2e8f0; padding: 8px; text-align: left; font-size: 10px; color: #475569; text-transform: uppercase; }
        .data-table td { border: 1px solid #e2e8f0; padding: 8px; font-size: 11px; }
        
        .stamps { margin-top: 50px; }
        .stamp-box { width: 200px; height: 80px; border: 1px dashed #cbd5e1; display: inline-block; text-align: center; padding-top: 60px; color: #94a3b8; font-size: 10px; font-weight: bold; }
        
        .footer { position: fixed; bottom: 0; width: 100%; border-top: 1px solid #e2e8f0; padding-top: 10px; font-size: 9px; color: #94a3b8; text-align: center; }

        @media print {
            .toolbar { display: none !important; }
            .document-shell { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <div class="toolbar-title">Work Application Preview: {{ $installation->installation_number }}</div>
        <div class="toolbar-actions">
            <a href="{{ route('admin.installations.work-application', ['id' => $installation->id, 'download' => 1]) }}" class="btn-download">Download</a>
            <button type="button" class="btn-print" onclick="window.print()">Print</button>
        </div>
    </div>

    <div class="document-shell">
    <div class="header">
        <h1>Work Completion Application</h1>
        <p>{{ $settings['company_name'] ?? 'KODAIC SOLAR SOLUTIONS' }}</p>
    </div>

    <div class="section">
        <div class="section-title">1. Customer & DISCOM Info</div>
        <div class="grid">
            <div class="col">
                <table>
                    <tr><td class="label">Customer:</td><td class="value">{{ $installation->customer->name }}</td></tr>
                    <tr><td class="label">Mobile:</td><td class="value">{{ $installation->customer->phone }}</td></tr>
                    <tr><td class="label">K-Number:</td><td class="value">{{ $installation->customer->discom->k_number ?? 'N/A' }}</td></tr>
                </table>
            </div>
            <div class="col-spacer"></div>
            <div class="col">
                <table>
                    <tr><td class="label">Application No:</td><td class="value">{{ $installation->customer->discom->application_number ?? 'N/A' }}</td></tr>
                    <tr><td class="label">Sanctioned Load:</td><td class="value">{{ $installation->customer->discom->sanctioned_load ?? '-' }} kW</td></tr>
                    <tr><td class="label">DISCOM:</td><td class="value">{{ $installation->customer->discom->discom_name ?? 'N/A' }}</td></tr>
                </table>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">2. Installation Technical Details</div>
        <div class="grid">
            <div class="col">
                <table>
                    <tr><td class="label">System Size:</td><td class="value">{{ $installation->system_size_kw }} kW</td></tr>
                    <tr><td class="label">Install Date:</td><td class="value">{{ $installation->scheduled_date }}</td></tr>
                    <tr><td class="label">Roof Type:</td><td class="value">{{ $installation->roof_type }}</td></tr>
                </table>
            </div>
            <div class="col-spacer"></div>
            <div class="col">
                <table>
                    <tr><td class="label">Structure:</td><td class="value">Standard Hot-Dip Galvanized</td></tr>
                    <tr><td class="label">Wiring Info:</td><td class="value">DC: 4sqmm | AC: 6sqmm</td></tr>
                    <tr><td class="label">LA/Earthing:</td><td class="value">Completed & Tested</td></tr>
                </table>
            </div>
        </div>
    </div>

    @if($installation->panel_serial_details && count($installation->panel_serial_details) > 0)
    <div class="section">
        <div class="section-title">3. Solar Module Details</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Serial Number</th>
                    <th>Make / Brand</th>
                    <th>Wattage</th>
                    <th>String No.</th>
                </tr>
            </thead>
            <tbody>
                @foreach($installation->panel_serial_details as $panel)
                <tr>
                    <td>{{ $panel['serial_number'] ?? '-' }}</td>
                    <td>{{ $panel['module_make'] ?? '-' }}</td>
                    <td>{{ $panel['wattage'] ?? '-' }} Wp</td>
                    <td>String - {{ $panel['string_number'] ?? '1' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($installation->inverter_serial_details && count($installation->inverter_serial_details) > 0)
    <div class="section">
        <div class="section-title">4. Inverter Details</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Inverter Serial</th>
                    <th>Make / Brand</th>
                    <th>Capacity</th>
                    <th>Phase</th>
                </tr>
            </thead>
            <tbody>
                @foreach($installation->inverter_serial_details as $inverter)
                <tr>
                    <td>{{ $inverter['serial_number'] ?? '-' }}</td>
                    <td>{{ $inverter['make'] ?? '-' }}</td>
                    <td>{{ $inverter['capacity'] ?? '-' }}</td>
                    <td>{{ $inverter['phase'] ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="section" style="margin-top: 30px;">
        <div class="section-title">5. Declaration</div>
        <p style="font-size: 10px; color: #475569; text-align: justify;">
            Certified that the installation of Rooftop Solar Photo-Voltaic Power Plant has been completed in accordance with the standards and specifications approved by MNRE/DISCOM. The system has been tested for safety and performance under local conditions and is ready for Net Metering synchronization.
        </p>
    </div>

    <div class="stamps">
        <div class="stamp-box" style="margin-right: 150px;">
            CONSUMER SIGNATURE
        </div>
        <div class="stamp-box">
            AUTHORIZED ENGINEER STAMP
        </div>
    </div>

    <div class="footer">
        Generated for {{ $installation->installation_number }} | {{ date('d M Y') }} | Built by Kodaic.cloud
    </div>
    </div>
</body>
</html>
