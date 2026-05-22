<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$a = collect([]);
try {
    echo "Result: " . ($a->first()->id ?? 'empty');
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage();
}
