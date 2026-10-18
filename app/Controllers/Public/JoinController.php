<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Core\Response;
use App\Core\View;

final class JoinController
{
    public function index(): Response
    {
        return View::render('public/join/index', [
            'title' => 'Bergabung — Papua Future Project',
            'page' => 'join',
            'stylesheet' => 'css/pages/join.css',
        ]);
    }
}
