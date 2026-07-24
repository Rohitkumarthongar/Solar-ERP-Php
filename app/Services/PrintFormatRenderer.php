<?php

namespace App\Services;

use App\Models\PrintFormat;
use Illuminate\Support\Facades\Blade;

class PrintFormatRenderer
{
    public function render(?PrintFormat $format, array $data): ?string
    {
        if (!$format || !$format->is_active || !$format->body_template) {
            return null;
        }

        // Build $images array from format's uploaded images
        $images = [];
        foreach ($format->images ?? [] as $img) {
            $images[$img['key']] = $img['url'];
        }
        $data['images'] = $images;

        $header = $format->header_html ? Blade::render($format->header_html, $data) : '';
        $body   = Blade::render($format->body_template, $data);
        $footer = $format->footer_html ? Blade::render($format->footer_html, $data) : '';

        $title       = $data['title'] ?? 'Document';
        $paperSize   = $format->paper_size ?: 'A4';
        $orientation = $format->orientation ?: 'portrait';
        $maxWidth    = $orientation === 'landscape' ? '1120px' : '840px';

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title}</title>
    <style>
        @page { size: {$paperSize} {$orientation}; margin: 12mm; }
        @page :first { margin-top: 12mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #111827; background: #fff; font-family: "Segoe UI", Arial, sans-serif; font-size: 13px; line-height: 1.5; }
        .print-shell { max-width: {$maxWidth}; margin: 0 auto; padding: 20px; }
        .print-header { width: 100%; margin-bottom: 20px; }
        .print-body   { width: 100%; }
        .print-footer { width: 100%; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; }
        img { max-width: 100%; height: auto; }
        .no-print { display: block; }
        @media print {
            .no-print { display: none !important; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            /* Hide Chrome's URL header/footer */
            @page { margin: 12mm; }
        }
    </style>
</head>
<body>
    <!-- Print toolbar -->
    <div class="no-print" style="position:fixed;top:0;left:0;right:0;z-index:9999;background:#1e293b;color:#fff;padding:10px 20px;display:flex;align-items:center;gap:12px;box-shadow:0 2px 8px rgba(0,0,0,0.3);">
        <span style="flex:1;font-size:13px;font-weight:600;">{$title}</span>
        <span style="font-size:11px;color:#94a3b8;">In print dialog: uncheck "Headers and footers"</span>
        <button onclick="window.print()" style="background:#f59e0b;color:#fff;border:none;padding:8px 20px;border-radius:8px;font-weight:700;font-size:13px;cursor:pointer;">⬇ Save as PDF</button>
        <button onclick="window.close()" style="background:rgba(255,255,255,0.1);color:#fff;border:none;padding:8px 14px;border-radius:8px;font-size:13px;cursor:pointer;">✕ Close</button>
    </div>
    <div style="height:48px;" class="no-print"></div>

    <div class="print-shell">
        {$header}
        <div class="print-body">{$body}</div>
        {$footer}
    </div>
    <script>
        // Auto-open print dialog after images load
        window.addEventListener('load', function() {
            setTimeout(function() { window.print(); }, 800);
        });
    </script>
</body>
</html>
HTML;
    }
}
