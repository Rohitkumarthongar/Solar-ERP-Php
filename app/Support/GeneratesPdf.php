<?php

namespace App\Support;

use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Response;

trait GeneratesPdf
{
    protected function pdfResponse(string $html, string $filename): \Symfony\Component\HttpFoundation\Response
    {
        try {
            $pdfContent = Browsershot::html($html)
                ->format('A4')
                ->margins(8, 8, 8, 8)
                ->showBackground()
                ->noSandbox()
                ->waitUntilNetworkIdle()
                ->pdf();

            return Response::make($pdfContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        } catch (\Throwable $e) {
            return response($html)->header('Content-Type', 'text/html');
        }
    }
}
