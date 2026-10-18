<?php

declare(strict_types=1);

if (PHP_SAPI === 'cli-server') {
    $requestPath = parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?: '/';
    if ($requestPath !== '/' && is_file(__DIR__ . $requestPath)) {
        return false;
    }
}

require dirname(__DIR__) . '/bootstrap.php';

(new \App\Core\App())->run();
