<?php

declare(strict_types=1);

if (!function_exists('e')) {
    function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('url')) {
    function url(string $path = '/'): string
    {
        $base = getenv('APP_ENV') === 'production'
            ? rtrim((string) (getenv('APP_URL') ?: ''), '/')
            : '';
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
