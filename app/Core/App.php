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
            $settings = require APP_ROOT . '/config/app.php';
            date_default_timezone_set($settings['timezone']);
            $request = Request::capture();
            Session::start($request->isSecure());
            $this->setSecurityHeaders();
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

    private function setSecurityHeaders(): void
    {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('Referrer-Policy: strict-origin-when-cross-origin');
    }
}
