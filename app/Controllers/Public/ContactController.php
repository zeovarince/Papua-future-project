<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Core\Response;
use App\Core\View;

final class ContactController
{
    public function index(): Response
    {
        return View::render('public/contact/index', [
            'title' => 'Kontak — Papua Future Project',
            'page' => 'contact',
            'stylesheet' => 'css/pages/contact.css',
            'script' => 'pages/contact.js',
        ]);
    }
}
