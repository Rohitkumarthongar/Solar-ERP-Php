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

        // Build $images array from format's uploaded images so templates can use $images['key']
        $images = [];
        foreach ($format->images ?? [] as $img) {
            $images[$img['key']] = $img['url'];
        }
        $data['images'] = $images;

        $header = $format->header_html
            ? Blade::render($format->header_html, $data)
            : '';

        $body = Blade::render($format->body_template, $data);

        $footer = $format->footer_html
            ? Blade::render($format->footer_html, $data)
            : '';

        return Blade::render(<<<'BLADE'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        @page { size: {{ $paperSize }} {{ $orientation }}; margin: 16mm; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            color: #111827;
            background: #ffffff;
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 13px;
            line-height: 1.5;
        }
        .print-shell {
            max-width: {{ $orientation === 'landscape' ? '1120px' : '840px' }};
            margin: 0 auto;
            padding: 24px;
        }
        .print-header,
        .print-footer {
            width: 100%;
        }
        .print-header {
            margin-bottom: 20px;
        }
        .print-body {
            width: 100%;
        }
        .print-footer {
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        img {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="print-shell">
        @if($header)
            <div class="print-header">{!! $header !!}</div>
        @endif

        <div class="print-body">{!! $body !!}</div>

        @if($footer)
            <div class="print-footer">{!! $footer !!}</div>
        @endif
    </div>
</body>
</html>
BLADE, [
            'title' => $data['title'] ?? 'Document',
            'paperSize' => $format->paper_size ?: 'A4',
            'orientation' => $format->orientation ?: 'portrait',
            'header' => $header,
            'body' => $body,
            'footer' => $footer,
        ]);
    }
}
