<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Core\Response;
use App\Core\View;

final class AboutController
{
    public function index(): Response
    {
        return View::render('public/about/index', [
            'title' => 'Tentang Kami — Papua Future Project',
            'page' => 'about',
            'stylesheet' => 'css/pages/about.css',
            'script' => 'pages/about.js',
        ]);
    }
}
