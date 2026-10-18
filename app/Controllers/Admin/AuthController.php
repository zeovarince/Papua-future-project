<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Flash;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Services\AuthService;
use App\Services\RateLimiter;

final class AuthController
{
    public function showLogin(): Response
    {
        if (Session::get('admin') !== null) {
            return Response::redirect('/admin');
        }

        return View::render('admin/auth/login', [
            'title' => 'Masuk ke Panel Admin',
            'error' => Flash::get('error'),
            'email' => Flash::get('email', ''),
        ], 'admin-auth');
    }

    public function login(Request $request): Response
    {
        if (Session::get('admin') !== null) {
            return Response::redirect('/admin');
        }

        $email = strtolower(trim((string) $request->post('email', '')));
        $password = (string) $request->post('password', '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '' || strlen($password) > 4096) {
            Flash::set('error', 'Email atau kata sandi tidak sesuai.');
            Flash::set('email', $email);
            return Response::redirect('/admin/login');
        }

        $identifier = hash('sha256', $request->ipAddress() . '|' . $email);
        $limiter = new RateLimiter();
        if ($limiter->isLimited('login', $identifier, 5, 900)) {
            Flash::set('error', 'Terlalu banyak percobaan masuk. Coba lagi dalam 15 menit.');
            Flash::set('email', $email);
            return Response::redirect('/admin/login');
        }

        if ((new AuthService())->attempt($email, $password)) {
            $limiter->clear('login', $identifier);
            return Response::redirect('/admin');
        }

        $limiter->hit('login', $identifier);
        Flash::set('error', 'Email atau kata sandi tidak sesuai.');
        Flash::set('email', $email);
        return Response::redirect('/admin/login');
    }

    public function logout(): Response
    {
        (new AuthService())->logout();
        return Response::redirect('/admin/login');
    }
}
