<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;
use Throwable;

final class View
{
    public static function render(string $view, array $data = [], string $layout = 'public', int $status = 200): Response
    {
        $viewFile = APP_ROOT . '/app/Views/' . $view . '.php';
        $layoutFile = APP_ROOT . '/app/Views/layouts/' . $layout . '.php';

        if (!is_file($viewFile) || !is_file($layoutFile)) {
            throw new RuntimeException('View tidak ditemukan.');
        }

        if ($layout === 'admin') {
            $data['admin'] ??= Session::get('admin', []);
        }
        extract($data, EXTR_SKIP);
        ob_start();
        require $viewFile;
        $content = (string) ob_get_clean();

        ob_start();
        require $layoutFile;
        $html = (string) ob_get_clean();

        return new Response($html, $status);
    }
}
