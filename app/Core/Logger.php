<?php

declare(strict_types=1);

namespace App\Core;

use Throwable;

final class Logger
{
    public static function error(Throwable $exception): void
    {
        $directory = APP_ROOT . '/storage/logs';
        if (!is_dir($directory) && !mkdir($directory, 0750, true) && !is_dir($directory)) {
            error_log($exception->__toString());
            return;
        }

        $line = sprintf("[%s] %s\n", date(DATE_ATOM), $exception->__toString());
        if (file_put_contents($directory . '/app.log', $line, FILE_APPEND | LOCK_EX) === false) {
            error_log($exception->__toString());
        }
    }
}
