<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$q = App\Models\Quotation::first();
if ($q) {
    try {
        $html = app(App\Http\Controllers\Admin\QuotationController::class)->downloadPdf($q->id);
        file_put_contents('/tmp/quotation_test.html', $html->content());
        echo "OK rendered\n";
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "No quotation found\n";
}
