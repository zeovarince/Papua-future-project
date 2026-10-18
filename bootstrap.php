<?php

declare(strict_types=1);

define('APP_ROOT', __DIR__);
define('PUBLIC_ROOT', APP_ROOT . '/public');

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

\App\Core\Environment::load(APP_ROOT . '/.env');

$privateStoragePath = getenv('STORAGE_DIR') ?: APP_ROOT . '/storage';
define('STORAGE_DIR', rtrim($privateStoragePath, '/\\'));
