<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit;
}

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$q = App\Models\Quotation::first();
if ($q) {
    try {
        // The diagnostic runs only from the CLI and calls the controller directly.
        session(['admin_logged_in' => true]);
        $html = app(App\Http\Controllers\Admin\QuotationController::class)
            ->downloadPdf(Illuminate\Http\Request::create('/admin/quotations/'.$q->id.'/pdf', 'GET'), $q->id);
        if ($html->getStatusCode() !== 200) {
            throw new RuntimeException('Quotation rendering returned HTTP '.$html->getStatusCode());
        }
        file_put_contents('/tmp/quotation_test.html', $html->content());
        echo "OK rendered\n";
    } catch (\Throwable $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "No quotation found\n";
}
