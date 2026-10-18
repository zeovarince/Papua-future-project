<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use Closure;

final class CsrfMiddleware implements Middleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Csrf::valid($request->post('_csrf'))) {
            return new Response('Permintaan tidak valid atau sesi formulir telah berakhir.', 419);
        }

        return $next();
    }
}
