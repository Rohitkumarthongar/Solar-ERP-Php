<?php
require 'vendor/autoload.php';
use Spatie\Browsershot\Browsershot;

try {
    Browsershot::html('<h1>Hello World</h1>')->savePdf('test.pdf');
    echo "PDF generated successfully\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
