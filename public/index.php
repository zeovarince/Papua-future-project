<?php

declare(strict_types=1);

use App\Core\App;

define('APP_ROOT', dirname(__DIR__));
define('PUBLIC_ROOT', __DIR__);

if (PHP_SAPI === 'cli-server') {
    $requestPath = parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?: '/';
    if ($requestPath !== '/' && is_file(__DIR__ . $requestPath)) {
        return false;
    }
}

$autoload = APP_ROOT . '/vendor/autoload.php';
if (is_file($autoload)) {
    require $autoload;
} else {
    require APP_ROOT . '/app/Core/helpers.php';
    spl_autoload_register(static function (string $class): void {
        $prefix = 'App\\';
        if (!str_starts_with($class, $prefix)) {
            return;
        }

        $path = APP_ROOT . '/app/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
        if (is_file($path)) {
            require $path;
        }
    });
}

(new App())->run();
