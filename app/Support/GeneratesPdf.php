<?php

namespace App\Support;

use Barryvdh\Snappy\Facades\SnappyPdf;

trait GeneratesPdf
{
    protected function pdfResponse(string $html, string $filename): \Symfony\Component\HttpFoundation\Response
    {
        try {
            $pdf = SnappyPdf::loadHTML($html)
                ->setOption('enable-local-file-access', true)
                ->setOption('images', true)
                ->setOption('no-outline', true)
                ->setOption('margin-top', '8mm')
                ->setOption('margin-bottom', '8mm')
                ->setOption('margin-left', '8mm')
                ->setOption('margin-right', '8mm')
                ->setOption('print-media-type', true)
                ->setOption('disable-smart-shrinking', true)
                ->setOption('zoom', '1')
                ->setOption('dpi', '150')
                ->setOption('load-media-error-handling', 'ignore')
                ->setOption('load-error-handling', 'ignore');

            return $pdf->download($filename);
        } catch (\Throwable $e) {
            return response($html)->header('Content-Type', 'text/html');
        }
    }
}
