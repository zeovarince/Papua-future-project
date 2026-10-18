<?php
declare(strict_types=1);
namespace App\Controllers\Public;
use App\Core\Response;use App\Core\View;use App\Models\Post;
final class PostController
{
    public function index(): Response { $posts=Post::publicList();return View::render('public/post/index',['title'=>'Berita & Kegiatan — Papua Future Project','page'=>'news','stylesheet'=>'css/pages/news.css','posts'=>$posts,'featured'=>$posts[0]??null]); }
    public function show(string $slug): Response
    {
        $post=Post::findPublicBySlug($slug);if($post===null)return View::render('public/errors/404',['title'=>'Berita tidak ditemukan','page'=>'not-found'],'public',404);
        return View::render('public/post/detail',['title'=>$post['title'].' — Papua Future Project','page'=>'news-detail','stylesheet'=>'css/pages/news-detail.css','post'=>$post,'photos'=>Post::photos((int)$post['id']),'related'=>Post::related((int)$post['id'],$post['category'])]);
    }
}
