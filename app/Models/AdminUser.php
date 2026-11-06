<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class AdminUser
{
    public static function all(): array
    {
        return Database::connection()->query(
            'SELECT id, email, name, created_at FROM admin_users ORDER BY name ASC, id ASC'
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findById(int $id): ?array
    {
        $statement = Database::connection()->prepare(
            'SELECT id, email, name, password_hash FROM admin_users WHERE id = :id LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        $admin = $statement->fetch(PDO::FETCH_ASSOC);
        return is_array($admin) ? $admin : null;
    }

    public static function findByEmail(string $email): ?array
    {
        $statement = Database::connection()->prepare(
            'SELECT id, email, name, password_hash FROM admin_users WHERE email = :email LIMIT 1'
        );
        $statement->execute(['email' => $email]);
        $admin = $statement->fetch(PDO::FETCH_ASSOC);
        return is_array($admin) ? $admin : null;
    }

    public static function updatePasswordHash(int $id, string $passwordHash): void
    {
        $statement = Database::connection()->prepare(
            'UPDATE admin_users SET password_hash = :password_hash WHERE id = :id'
        );
        $statement->execute(['password_hash' => $passwordHash, 'id' => $id]);
    }

    public static function create(string $name, string $email, string $passwordHash): int
    {
        $statement = Database::connection()->prepare(
            'INSERT INTO admin_users (email, name, password_hash) VALUES (:email, :name, :password_hash)'
        );
        $statement->execute([
            'email' => $email,
            'name' => $name,
            'password_hash' => $passwordHash,
        ]);
        return (int) Database::connection()->lastInsertId();
    }
}
