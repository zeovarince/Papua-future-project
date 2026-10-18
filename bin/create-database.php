<?php

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

if (PHP_SAPI !== 'cli' || getenv('APP_ENV') !== 'local') {
    fwrite(STDERR, "Perintah ini hanya tersedia lewat CLI pada APP_ENV=local.\n");
    exit(2);
}

$name = getenv('DB_NAME') ?: '';
$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '3306';
$user = getenv('DB_USER') ?: '';
$password = getenv('DB_PASS') ?: '';
if (!preg_match('/^[A-Za-z0-9_]+$/', $name) || $user === '') {
    fwrite(STDERR, "DB_NAME atau DB_USER di .env tidak valid.\n");
    exit(2);
}

try {
    $pdo = new PDO(
        sprintf('mysql:host=%s;port=%s;charset=utf8mb4', $host, $port),
        $user,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
    );
    $pdo->exec(sprintf(
        'CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci',
        $name,
    ));
    fwrite(STDOUT, "Database lokal `{$name}` siap.\n");
} catch (\Throwable $exception) {
    fwrite(STDERR, "Database tidak dapat dibuat. Pastikan MySQL aktif dan akun lokal punya izin CREATE DATABASE.\n");
    exit(1);
}
