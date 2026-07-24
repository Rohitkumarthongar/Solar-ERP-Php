<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$p = App\Models\PrintFormat::find(4);
if ($p) {
    $p->body_template = App\Support\PrintFormatPresets::quotationPdfReplica()['body_template'];
    $p->save();
    echo "OK DB UPDATED\n";
} else {
    echo "NOT FOUND\n";
}
