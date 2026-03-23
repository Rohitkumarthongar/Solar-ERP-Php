<!DOCTYPE html>
<html>
<head>
    <title>Profit & Loss Statement</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 14px; color: #333; margin: 0; padding: 0; }
        .header { text-align: center; border-bottom: 2px solid #ea580c; padding-bottom: 20px; margin-bottom: 30px; }
        .company-name { font-size: 28px; font-weight: bold; color: #ea580c; margin: 0; }
        .report-title { font-size: 20px; margin: 5px 0; color: #555; text-transform: uppercase; letter-spacing: 1px; }
        .report-date { font-size: 14px; color: #777; margin: 0; }
        .section-title { font-size: 18px; font-weight: bold; margin-bottom: 10px; color: #444; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        th, td { padding: 12px 10px; text-align: left; border-bottom: 1px solid #eee; }
        th { background-color: #f9f9f9; color: #555; text-transform: uppercase; font-size: 12px; font-weight: bold; letter-spacing: 0.5px; }
        .amount { text-align: right; font-weight: bold; }
        .total-row td { border-top: 2px solid #ddd; border-bottom: 2px solid #ddd; font-weight: bold; font-size: 16px; background-color: #fdfdfd; }
        .revenue-color { color: #16a34a; }
        .expense-color { color: #dc2626; }
        
        .summary-box { 
            background-color: {{ $profit >= 0 ? '#f0fdf4' : '#fef2f2' }}; 
            border: 2px solid {{ $profit >= 0 ? '#16a34a' : '#dc2626' }}; 
            padding: 25px; 
            text-align: center; 
            margin-top: 40px; 
            border-radius: 8px;
        }
        .summary-title { font-size: 16px; color: #555; text-transform: uppercase; letter-spacing: 2px; margin: 0; }
        .summary-amount { font-size: 36px; font-weight: bold; color: {{ $profit >= 0 ? '#16a34a' : '#dc2626' }}; margin: 10px 0 0; }
        .footer { margin-top: 50px; font-size: 11px; color: #999; text-align: center; border-top: 1px solid #eee; padding-top: 15px; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="company-name">{{ $settings['company_name'] ?? 'SolarTech Solutions' }}</h1>
        <h2 class="report-title">Profit & Loss Statement</h2>
        <p class="report-date">Target Period: {{ \Carbon\Carbon::parse($from)->format('d M, Y') }} - {{ \Carbon\Carbon::parse($to)->format('d M, Y') }}</p>
    </div>

    <div class="section-title text-green-700">Revenue (Income)</div>
    <table>
        <tbody>
            <tr>
                <td>Completed Sales Orders</td>
                <td class="amount">₹{{ number_format($sales, 2) }}</td>
            </tr>
            <tr class="total-row revenue-color">
                <td>Total Revenue</td>
                <td class="amount">₹{{ number_format($sales, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title text-red-700">Expenses (Outflow)</div>
    <table>
        <tbody>
            <tr>
                <td>Purchases (Stock/Materials)</td>
                <td class="amount">₹{{ number_format($purchases, 2) }}</td>
            </tr>
            <tr>
                <td>Employee Salaries</td>
                <td class="amount">₹{{ number_format($salaries, 2) }}</td>
            </tr>
            <tr>
                <td>Service & Component Costs</td>
                <td class="amount">₹{{ number_format($serviceExpenses, 2) }}</td>
            </tr>
            <tr>
                <td>Direct / Miscellaneous Expenses</td>
                <td class="amount">₹{{ number_format($directExpenses, 2) }}</td>
            </tr>
            <tr class="total-row expense-color">
                <td>Total Expenses</td>
                <td class="amount">₹{{ number_format($totalExpenses, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="summary-box">
        <h3 class="summary-title">Net {{ $profit >= 0 ? 'Profit' : 'Loss' }}</h3>
        <p class="summary-amount">₹{{ number_format(abs($profit), 2) }}</p>
    </div>

    <div class="footer">
        Generated on {{ now()->format('d M, Y h:i A') }} • This is an automatically generated report.
    </div>
</body>
</html>
