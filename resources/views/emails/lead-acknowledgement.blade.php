<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
  body { font-family: Arial, sans-serif; background: #f8f8f8; margin: 0; padding: 20px; }
  .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 20px rgba(0,0,0,.1); }
  .header { background: linear-gradient(135deg, #ea580c, #d97706); padding: 30px; text-align: center; color: white; }
  .header h1 { margin: 0; font-size: 24px; }
  .header p { margin: 8px 0 0; opacity: .9; }
  .body { padding: 32px; }
  .detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
  .detail-row .label { color: #666; }
  .detail-row .value { font-weight: 600; color: #333; }
  .cta { background: #ea580c; color: white; padding: 14px 28px; border-radius: 8px; text-decoration: none; display: inline-block; margin-top: 20px; font-weight: 600; }
  .footer { background: #f8f8f8; padding: 20px; text-align: center; font-size: 12px; color: #888; }
  .badge { display: inline-block; background: #fef3c7; color: #92400e; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; margin-bottom: 16px; }
</style>
</head>
<body>
<div class="container">
  <div class="header">
    <div style="font-size:40px;margin-bottom:10px;">☀️</div>
    <h1>Thank You, {{ $lead->name }}!</h1>
    <p>We've received your solar enquiry</p>
  </div>
  <div class="body">
    <div class="badge">Reference: {{ $lead->lead_number }}</div>
    <p style="color:#555;line-height:1.7">Thank you for reaching out to Palawat Solar. We're excited to help you switch to clean, affordable solar energy! Our expert team will review your requirements and get back to you within <strong>24 hours</strong>.</p>
    @if($lead->monthly_electricity_bill || $lead->required_load_kw || $lead->k_number)
    <div style="background:#fff7ed;border-radius:10px;padding:18px;margin:20px 0;border:1px solid #fed7aa">
      <p style="font-weight:700;color:#c2410c;margin:0 0 12px">Your Enquiry Details:</p>
      @if($lead->k_number)
      <div class="detail-row"><span class="label">EB K-Number / Consumer No</span><span class="value">{{ $lead->k_number }}</span></div>
      @endif
      @if($lead->monthly_electricity_bill)
      <div class="detail-row"><span class="label">Monthly Electricity Bill</span><span class="value">₹{{ number_format($lead->monthly_electricity_bill, 2) }}</span></div>
      @endif
      @if($lead->required_load_kw)
      <div class="detail-row"><span class="label">Required System Size</span><span class="value">{{ $lead->required_load_kw }} kW</span></div>
      @endif
      @if($lead->roof_type)
      <div class="detail-row"><span class="label">Roof Type</span><span class="value">{{ $lead->roof_type }}</span></div>
      @endif
    </div>
    @endif
    <div style="background:#f0fdf4;border-radius:10px;padding:18px;margin:20px 0;border:1px solid #bbf7d0">
      <p style="font-weight:700;color:#166534;margin:0 0 10px">⚡ What Happens Next:</p>
      <ol style="color:#555;font-size:14px;line-height:2;margin:0;padding-left:20px">
        <li>Our solar expert will call you within 24 hours</li>
        <li>We will do a free site assessment</li>
        <li>You'll receive a detailed customized quotation</li>
        <li>Installation by our certified team</li>
      </ol>
    </div>
    <div style="text-align:center;margin-top:24px">
      <p style="color:#888;font-size:13px">Questions? Call us:</p>
      <p style="font-size:20px;font-weight:700;color:#ea580c">+91 98765 43210</p>
    </div>
  </div>
  <div class="footer">
    <p>© {{ date('Y') }} Palawat Solar | 123 Solar Park, Gujarat - 380001</p>
    <p>Made with ❤️ by <a href="https://kodaic.cloud/" style="color:#ea580c">Kodaic</a></p>
  </div>
</div>
</body>
</html>
