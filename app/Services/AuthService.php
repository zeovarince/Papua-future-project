<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Session;
use App\Core\Csrf;
use App\Models\AdminUser;

final class AuthService
{
    public function attempt(string $email, string $password): bool
    {
        $admin = AdminUser::findByEmail($email);
        static $dummyHash = null;
        $hash = is_array($admin)
            ? (string) $admin['password_hash']
            : ($dummyHash ??= password_hash('invalid-login-placeholder', PASSWORD_DEFAULT));

        if (!password_verify($password, $hash) || !is_array($admin)) {
            return false;
        }

        if (password_needs_rehash((string) $admin['password_hash'], PASSWORD_DEFAULT)) {
            AdminUser::updatePasswordHash((int) $admin['id'], password_hash($password, PASSWORD_DEFAULT));
        }

        Session::regenerate();
        Csrf::rotate();
        Session::put('admin', [
            'id' => (int) $admin['id'],
            'name' => (string) $admin['name'],
            'email' => (string) $admin['email'],
        ]);

        return true;
    }

    public function logout(): void
    {
        Session::destroy();
    }
}
