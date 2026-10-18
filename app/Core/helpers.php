<?php

declare(strict_types=1);

if (!function_exists('e')) {
    function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('url')) {
    function app_base_path(): string
    {
        $scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
        $basePath = str_replace('\\', '/', dirname($scriptName));

        return ($basePath === '/' || $basePath === '.') ? '' : rtrim($basePath, '/');
    }

    function url(string $path = '/'): string
    {
        $base = getenv('APP_ENV') === 'production'
            ? rtrim((string) (getenv('APP_URL') ?: ''), '/')
            : app_base_path();
        return $base . '/' . ltrim($path, '/');
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . e(\App\Core\Csrf::token()) . '">';
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        $relativePath = ltrim($path, '/');
        $publicPath = PUBLIC_ROOT . '/assets/' . $relativePath;
        $version = is_file($publicPath) ? (string) filemtime($publicPath) : null;
        $assetUrl = url('/assets/' . $relativePath);

        return $version === null ? $assetUrl : $assetUrl . '?v=' . $version;
    }
}
