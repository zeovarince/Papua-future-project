<?php

declare(strict_types=1);

use App\Core\Router;
use App\Controllers\Admin\AuthController;
use App\Controllers\Admin\AboutController as AdminAboutController;
use App\Controllers\Admin\BatchController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\TeamController;
use App\Controllers\Public\AboutController;
use App\Controllers\Public\ContactController;
use App\Controllers\Public\HomeController;
use App\Controllers\Public\JoinController;
use App\Controllers\Public\PostController;
use App\Controllers\Public\ProgramController;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;

$router = new Router();
$auth = new AuthMiddleware();
$csrf = new CsrfMiddleware();

$router->get('/', static fn () => (new HomeController())->index());
$router->get('/tentang', static fn () => (new AboutController())->index());
$router->get('/program', static fn () => (new ProgramController())->index());
$router->get('/program/{slug}', static fn ($request, string $slug) => (new ProgramController())->show($slug));
$router->get('/berita', static fn () => (new PostController())->index());
$router->get('/berita/{slug}', static fn ($request, string $slug) => (new PostController())->show($slug));
$router->get('/bergabung', static fn () => (new JoinController())->index());
$router->get('/kontak', static fn () => (new ContactController())->index());
$router->get('/admin/login', static fn () => (new AuthController())->showLogin());
$router->post('/admin/login', static fn ($request) => (new AuthController())->login($request), [$csrf]);
$router->post('/admin/logout', static fn () => (new AuthController())->logout(), [$auth, $csrf]);
$router->get('/admin', static fn ($request) => (new DashboardController())->index($request), [$auth]);
$router->get('/admin/tentang', static fn () => (new AdminAboutController())->edit(), [$auth]);
$router->post('/admin/tentang', static fn ($request) => (new AdminAboutController())->update($request), [$auth, $csrf]);
$router->get('/admin/tim/{group}', static fn ($request, string $group) => (new TeamController())->index($group), [$auth]);
$router->get('/admin/tim/{group}/tambah', static fn ($request, string $group) => (new TeamController())->create($group), [$auth]);
$router->post('/admin/tim/{group}', static fn ($request, string $group) => (new TeamController())->store($request, $group), [$auth, $csrf]);
$router->get('/admin/tim/{group}/{id}/ubah', static fn ($request, string $group, string $id) => (new TeamController())->edit($group, (int) $id), [$auth]);
$router->post('/admin/tim/{group}/{id}', static fn ($request, string $group, string $id) => (new TeamController())->update($request, $group, (int) $id), [$auth, $csrf]);
$router->post('/admin/tim/{group}/{id}/hapus', static fn ($request, string $group, string $id) => (new TeamController())->destroy($request, $group, (int) $id), [$auth, $csrf]);
$router->get('/admin/batch', static fn () => (new BatchController())->index(), [$auth]);
$router->get('/admin/batch/tambah', static fn () => (new BatchController())->create(), [$auth]);
$router->post('/admin/batch', static fn ($request) => (new BatchController())->store($request), [$auth, $csrf]);
$router->get('/admin/batch/{id}/ubah', static fn ($request, string $id) => (new BatchController())->edit((int) $id), [$auth]);
$router->post('/admin/batch/{id}', static fn ($request, string $id) => (new BatchController())->update($request, (int) $id), [$auth, $csrf]);
$router->post('/admin/batch/{id}/toggle', static fn ($request, string $id) => (new BatchController())->toggle($request, (int) $id), [$auth, $csrf]);
$router->post('/admin/batch/{id}/hapus', static fn ($request, string $id) => (new BatchController())->destroy($request, (int) $id), [$auth, $csrf]);

return $router;
