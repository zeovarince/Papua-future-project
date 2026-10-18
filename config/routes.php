<?php

declare(strict_types=1);

use App\Core\Router;
use App\Controllers\Public\AboutController;
use App\Controllers\Public\ContactController;
use App\Controllers\Public\HomeController;
use App\Controllers\Public\JoinController;
use App\Controllers\Public\PostController;
use App\Controllers\Public\ProgramController;

$router = new Router();

$router->get('/', static fn () => (new HomeController())->index());
$router->get('/tentang', static fn () => (new AboutController())->index());
$router->get('/program', static fn () => (new ProgramController())->index());
$router->get('/program/{slug}', static fn ($request, string $slug) => (new ProgramController())->show($slug));
$router->get('/berita', static fn () => (new PostController())->index());
$router->get('/berita/{slug}', static fn ($request, string $slug) => (new PostController())->show($slug));
$router->get('/bergabung', static fn () => (new JoinController())->index());
$router->get('/kontak', static fn () => (new ContactController())->index());

return $router;
