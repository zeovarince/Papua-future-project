<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Core\Response;
use App\Core\View;
use App\Models\TeamMember;
use App\Models\Program;
use App\Models\Post;

final class HomeController
{
    public function index(): Response
    {
        return View::render('public/home/index', [
            'title' => 'Papua Future Project — Every Child Matters',
            'page' => 'home',
            'stylesheet' => 'css/pages/home.css',
            'script' => 'js/pages/home.js',
            'founder' => TeamMember::founder(),
            'programs' => Program::featured(3),
            'recentPosts' => Post::featured(3),
        ]);
    }
}
