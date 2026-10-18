<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Flash;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use Closure;

final class AuthMiddleware implements Middleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $admin = Session::get('admin');
        if (!is_array($admin) || !isset($admin['id'])) {
            Flash::set('error', 'Silakan masuk sebagai admin terlebih dahulu.');
            return Response::redirect('/admin/login');
        }

        return $next();
    }
}
