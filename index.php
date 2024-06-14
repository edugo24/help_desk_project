<?php
require __DIR__ . '/vendor/autoload.php';

// Ejemplo de uso de vlucas/phpdotenv
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$url = 'login.php';

// Redirigir usando la función header()
header("Location: $url");