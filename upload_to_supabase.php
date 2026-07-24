<?php
/**
 * Upload all local storage files to Supabase
 * Run: php upload_to_supabase.php
 * DELETE THIS FILE AFTER USE
 */

define('LARAVEL_START', microtime(true));
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Storage;

$localDisk = Storage::disk('public');
$supabase  = Storage::disk('supabase');

$folders = [
    'settings',
    'blogs',
    'categories',
    'products',
    'installation-proofs',
    'discom-reports',
    'purchase-invoices',
    'site-visits',
];

$uploaded = 0;
$failed   = 0;

foreach ($folders as $folder) {
    $files = $localDisk->allFiles($folder);

    if (empty($files)) {
        echo "[SKIP] $folder — no files\n";
        continue;
    }

    echo "\n📁 $folder (" . count($files) . " files)\n";

    foreach ($files as $file) {
        try {
            $content = $localDisk->get($file);
            $mime    = $localDisk->mimeType($file);

            $supabase->put($file, $content, [
                'visibility'  => 'public',
                'ContentType' => $mime,
            ]);

            echo "  [OK] $file\n";
            $uploaded++;
        } catch (Exception $e) {
            echo "  [FAIL] $file — " . $e->getMessage() . "\n";
            $failed++;
        }
    }
}

echo "\n✅ Done — Uploaded: $uploaded | Failed: $failed\n";
