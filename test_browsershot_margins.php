<?php
require 'vendor/autoload.php';
$reflector = new ReflectionClass(Spatie\Browsershot\Browsershot::class);
foreach ($reflector->getMethods() as $method) {
    if (strpos(strtolower($method->getName()), 'margin') !== false || strpos(strtolower($method->getName()), 'format') !== false) {
        echo $method->getName() . "\n";
    }
}
