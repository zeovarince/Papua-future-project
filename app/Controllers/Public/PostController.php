<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Core\Response;
use App\Core\View;

final class PostController
{
    public function index(): Response
    {
        return View::render('public/post/index', [
            'title' => 'Berita & Kegiatan — Papua Future Project',
            'page' => 'news',
            'stylesheet' => 'css/pages/news.css',
        ]);
    }

    public function show(string $slug): Response
    {
        if ($slug !== '500-buku-baru') {
            return View::render('public/errors/404', [
                'title' => 'Berita tidak ditemukan',
                'page' => 'not-found',
            ], 'public', 404);
        }

        return View::render('public/post/show', [
            'title' => '500 Buku Baru Tiba di Pulau Mansinam — Papua Future Project',
            'page' => 'news-detail',
            'stylesheet' => 'css/pages/news-detail.css',
            'script' => 'pages/news-detail.js',
            'slug' => $slug,
        ]);
    }
}
