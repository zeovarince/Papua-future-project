<?php

declare(strict_types=1);

namespace App\Core;

use Throwable;
use ErrorException;

final class App
{
    public function run(): void
    {
        try {
            if (getenv('APP_ENV') === 'production') {
                ini_set('display_errors', '0');
                ini_set('log_errors', '1');
            }
            $settings = require APP_ROOT . '/config/app.php';
            date_default_timezone_set($settings['timezone']);
            $request = Request::capture();
            Session::start($request->isSecure());
            $this->setSecurityHeaders($request);
            set_error_handler(static function (int $severity, string $message, string $file, int $line): bool {
                if (!(error_reporting() & $severity)) {
                    return false;
                }
                throw new ErrorException($message, 0, $severity, $file, $line);
            });
            $router = require APP_ROOT . '/config/routes.php';
            $router->dispatch($request)->send();
        } catch (Throwable $exception) {
            Logger::error($exception);
            View::render('public/errors/500', [
                'title' => 'Terjadi kesalahan',
                'page' => 'error',
                'detail' => (getenv('APP_ENV') === 'local') ? $exception->getMessage() : null,
            ], 'public', 500)->send();
        }
    }

    private function setSecurityHeaders(Request $request): void
    {
        header_remove('X-Powered-By');
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: camera=(), microphone=(), geolocation=()');

        if (getenv('APP_ENV') === 'production' && $request->isSecure()) {
            header('Strict-Transport-Security: max-age=31536000');
        }

        if ($request->path() === '/admin' || str_starts_with($request->path(), '/admin/')) {
            header('X-Robots-Tag: noindex, nofollow');
            header('Cache-Control: no-store, private, max-age=0');
            header('Pragma: no-cache');
        }
    }
}
