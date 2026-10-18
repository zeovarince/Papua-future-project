<?php

declare(strict_types=1);

namespace App\Core;

use Throwable;

final class App
{
    public function run(): void
    {
        try {
            $settings = require APP_ROOT . '/config/app.php';
            date_default_timezone_set($settings['timezone']);
            $router = require APP_ROOT . '/config/routes.php';
            $router->dispatch(Request::capture())->send();
        } catch (Throwable $exception) {
            error_log($exception->__toString());
            View::render('public/errors/500', [
                'title' => 'Terjadi kesalahan',
                'page' => 'error',
            ], 'public', 500)->send();
        }
    }
}
