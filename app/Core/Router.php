<?php

declare(strict_types=1);

namespace App\Core;

use Closure;
use App\Middleware\Middleware;

final class Router
{
    /** @var list<array{method: string, pattern: string, handler: Closure, middleware: list<Middleware>}> */
    private array $routes = [];

    public function get(string $path, Closure $handler, array $middleware = []): void
    {
        $this->add('GET', $path, $handler, $middleware);
    }

    public function post(string $path, Closure $handler, array $middleware = []): void
    {
        $this->add('POST', $path, $handler, $middleware);
    }

    public function dispatch(Request $request): Response
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $request->method()) {
                continue;
            }

            if (preg_match($route['pattern'], $request->path(), $matches) !== 1) {
                continue;
            }

            $parameters = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            $arguments = array_values($parameters);
            $next = static fn (Request $currentRequest, mixed ...$args): Response => ($route['handler'])($currentRequest, ...$args);
            foreach (array_reverse($route['middleware']) as $middleware) {
                $downstream = $next;
                $next = static fn (Request $currentRequest, mixed ...$args): Response => $middleware->handle(
                    $currentRequest,
                    static fn (): Response => $downstream($currentRequest, ...$args),
                );
            }
            return $next($request, ...$arguments);
        }

        return View::render('public/errors/404', [
            'title' => 'Halaman tidak ditemukan',
            'page' => 'not-found',
        ], 'public', 404);
    }

    private function add(string $method, string $path, Closure $handler, array $middleware): void
    {
        $quoted = preg_quote($path, '~');
        $pattern = preg_replace('/\\\{([a-zA-Z][a-zA-Z0-9_]*)\\\}/', '(?P<$1>[^/]+)', $quoted);
        $this->routes[] = [
            'method' => $method,
            'pattern' => '~^' . $pattern . '$~',
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }
}
