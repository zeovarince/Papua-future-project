<?php

declare(strict_types=1);

namespace App\Core;

final class Request
{
    public function __construct(
        private readonly string $method,
        private readonly string $path,
        private readonly array $query,
        private readonly array $post,
        private readonly array $files,
        private readonly string $ipAddress,
        private readonly bool $secureConnection,
    ) {
    }

    public static function capture(): self
    {
        $uri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $path = '/' . trim(rawurldecode($path), '/');
        $path = $path === '/' ? '/' : rtrim($path, '/');

        $scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
        $basePath = str_replace('\\', '/', dirname($scriptName));
        $basePath = ($basePath === '/' || $basePath === '.') ? '' : rtrim($basePath, '/');
        if ($basePath !== '' && ($path === $basePath || str_starts_with($path, $basePath . '/'))) {
            $path = substr($path, strlen($basePath)) ?: '/';
        }

        if (getenv('APP_ENV') === 'production') {
            $secretAdminPath = \admin_path();
            if ($secretAdminPath !== null && ($path === $secretAdminPath || str_starts_with($path, $secretAdminPath . '/'))) {
                $path = '/admin' . substr($path, strlen($secretAdminPath));
            } elseif ($path === '/admin' || str_starts_with($path, '/admin/')) {
                $path = '/_invalid-admin-path' . substr($path, strlen('/admin'));
            }
        }

        return new self(
            strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')),
            $path,
            $_GET,
            $_POST,
            $_FILES,
            (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'),
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        );
    }

    public function method(): string
    {
        return $this->method;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function query(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->query;
        }
        return $this->query[$key] ?? $default;
    }

    public function post(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->post;
        }
        return $this->post[$key] ?? $default;
    }

    public function file(string $key): ?array
    {
        $file = $this->files[$key] ?? null;
        return is_array($file) ? $file : null;
    }

    public function ipAddress(): string
    {
        return $this->ipAddress;
    }

    public function isSecure(): bool
    {
        return $this->secureConnection;
    }
}
