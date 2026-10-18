<?php

declare(strict_types=1);

namespace App\Core;

use Closure;

final class Response
{
    public function __construct(
        private readonly string|Closure $body = '',
        private readonly int $status = 200,
        private readonly array $headers = ['Content-Type' => 'text/html; charset=UTF-8'],
    ) {
    }

    public static function redirect(string $location, int $status = 303): self
    {
        return new self('', $status, ['Location' => $location, 'Cache-Control' => 'no-store']);
    }

    public static function stream(Closure $callback, array $headers, int $status = 200): self
    {
        return new self($callback, $status, $headers);
    }

    public function send(): void
    {
        http_response_code($this->status);
        foreach ($this->headers as $name => $value) {
            header($name . ': ' . $value);
        }

        if ($this->body instanceof Closure) {
            ($this->body)();
            return;
        }
        echo $this->body;
    }
}
