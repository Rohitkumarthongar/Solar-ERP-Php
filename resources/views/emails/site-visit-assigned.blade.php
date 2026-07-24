<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Visit Assigned</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .badge {
            display: inline-block;
            background-color: #fef3c7;
            color: #92400e;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            margin-top: 10px;
        }
        .content {
            margin-bottom: 30px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 15px;
        }
        .info-box {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .info-row {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #6b7280;
            width: 150px;
            flex-shrink: 0;
        }
        .info-value {
            color: #1f2937;
            font-weight: 500;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
            box-shadow: 0 4px 6px rgba(249, 115, 22, 0.3);
        }
        .button:hover {
            background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%);
        }
        .footer {
            text-align: center;
            color: #6b7280;
            font-size: 12px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
        .note {
            background-color: #dbeafe;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🗓️ New Site Visit Assigned</h1>
            <span class="badge">{{ $siteVisit->visit_number }}</span>
        </div>

        <div class="content">
            <p class="greeting">Hello {{ $employee->name }},</p>
            
            <p>You have been assigned a new site visit. Please review the details below and complete the visit as scheduled.</p>

            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">Visit Number:</span>
                    <span class="info-value">{{ $siteVisit->visit_number }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Customer:</span>
                    <span class="info-value">{{ $siteVisit->customer->name ?? ($siteVisit->lead->name ?? 'N/A') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Scheduled Date:</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($siteVisit->scheduled_at)->format('d M Y, h:i A') }}</span>
                </div>
                @if($siteVisit->lead)
                <div class="info-row">
                    <span class="info-label">Lead Number:</span>
                    <span class="info-value">{{ $siteVisit->lead->lead_number }}</span>
                </div>
                @endif
                @if($siteVisit->system_size_kw)
                <div class="info-row">
                    <span class="info-label">System Size:</span>
                    <span class="info-value">{{ $siteVisit->system_size_kw }} kW</span>
                </div>
                @endif
                @if($assignedBy)
                <div class="info-row">
                    <span class="info-label">Assigned By:</span>
                    <span class="info-value">{{ $assignedBy }}</span>
                </div>
                @endif
            </div>

            <div style="text-align: center;">
                <a href="{{ url('/admin/site-visits/' . $siteVisit->id) }}" class="button">
                    📋 View Site Visit Details
                </a>
            </div>

            <div class="note">
                <strong>📝 Important:</strong> Please complete the technical details and upload site photos after your visit. This information is crucial for preparing accurate quotations and sales orders.
            </div>

            @if($siteVisit->technical_notes)
            <div style="margin-top: 20px;">
                <strong>Additional Notes:</strong>
                <p style="background-color: #f9fafb; padding: 15px; border-radius: 5px; margin-top: 10px;">
                    {{ $siteVisit->technical_notes }}
                </p>
            </div>
            @endif
        </div>

        <div class="footer">
            <p>This is an automated notification from your Solar ERP System.</p>
            <p>If you have any questions, please contact your administrator.</p>
        </div>
    </div>
</body>
</html>

// Made with Bob
