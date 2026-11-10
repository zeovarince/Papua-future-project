<?php

declare(strict_types=1);

if (!defined('APP_ROOT')) {
    define('APP_ROOT', __DIR__);
}

if (!defined('PUBLIC_ROOT')) {
    $configuredPublicRoot = trim((string) (getenv('PUBLIC_DIR') ?: ''));
    $defaultPublicRoot = $configuredPublicRoot !== '' && is_dir($configuredPublicRoot)
        ? $configuredPublicRoot
        : APP_ROOT . '/public';
    define('PUBLIC_ROOT', $defaultPublicRoot);
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

\App\Core\Environment::load(APP_ROOT . '/.env');

$privateStoragePath = getenv('STORAGE_DIR') ?: APP_ROOT . '/storage';
define('STORAGE_DIR', rtrim($privateStoragePath, '/\\'));
