<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Core\Response;
use App\Core\View;
use App\Models\Post;

final class PostController
{
    public function index(): Response
    {
        $allPosts = Post::publicList(null, 200);
        $stories = array_values(array_filter(
            $allPosts,
            static fn (array $post): bool => in_array($post['category'], ['ACTIVITY', 'UPDATE'], true)
        ));
        $featured = $stories[0] ?? null;

        return View::render('public/post/index', [
            'title' => 'Berita & Kegiatan — Papua Future Project',
            'page' => 'news',
            'stylesheet' => 'css/pages/news.css',
            'posts' => array_slice($stories, $featured === null ? 0 : 1),
            'featured' => $featured,
            'awards' => array_values(array_filter($allPosts, static fn (array $post): bool => $post['category'] === 'AWARD')),
            'media' => array_values(array_filter($allPosts, static fn (array $post): bool => $post['category'] === 'MEDIA')),
        ]);
    }

    public function show(string $slug): Response
    {
        $post = Post::findPublicBySlug($slug);
        if ($post === null) {
            return View::render('public/errors/404', [
                'title' => 'Berita tidak ditemukan',
                'page' => 'not-found',
            ], 'public', 404);
        }

        return View::render('public/post/detail', [
            'title' => $post['title'] . ' — Berita Papua Future Project',
            'page' => 'news-detail',
            'stylesheet' => 'css/pages/news-detail.css',
            'script' => 'js/pages/news-detail.js',
            'post' => $post,
            'photos' => Post::photos((int) $post['id']),
            'related' => Post::related((int) $post['id'], $post['category']),
        ]);
    }
}
