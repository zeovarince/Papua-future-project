<?php

declare(strict_types=1);

namespace App\Core;

final class Flash
{
    public static function set(string $key, mixed $value): void
    {
        $messages = Session::get('_flash', []);
        $messages[$key] = $value;
        Session::put('_flash', $messages);
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $messages = Session::get('_flash', []);
        $value = $messages[$key] ?? $default;
        unset($messages[$key]);
        Session::put('_flash', $messages);
        return $value;
    }
}
