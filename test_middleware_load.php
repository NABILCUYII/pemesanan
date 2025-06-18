<?php

// Pastikan path ini benar sesuai struktur proyek Anda
require __DIR__.'/vendor/autoload.php';

use App\Http\Middleware\AdminMiddleware;

try {
    // Coba instansiasi kelas
    $middleware = new AdminMiddleware();
    echo "AdminMiddleware class found and loaded successfully!\n";
} catch (Throwable $e) {
    echo "Error loading AdminMiddleware: " . $e->getMessage() . "\n";
}

?> 