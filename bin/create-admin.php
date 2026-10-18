<?php

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

use App\Core\Database;
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$email = strtolower(trim((string) ($argv[1] ?? '')));
$name = trim((string) ($argv[2] ?? ''));
$password = getenv('PFP_ADMIN_PASSWORD') ?: '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $name === '' || mb_strlen($name) > 100) {
    fwrite(STDERR, "Pemakaian: PFP_ADMIN_PASSWORD=<rahasia> php bin/create-admin.php email nama-admin\n");
    exit(2);
}
if (strlen($password) < 12) {
    fwrite(STDERR, "PFP_ADMIN_PASSWORD wajib berisi minimal 12 karakter.\n");
    exit(2);
}

try {
    $pdo = Database::connection();
    $statement = $pdo->prepare('INSERT INTO admin_users (email, name, password_hash) VALUES (:email, :name, :password_hash)');
    $statement->execute([
        'email' => $email,
        'name' => $name,
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
    ]);
    fwrite(STDOUT, "Akun admin {$email} berhasil dibuat. Password hanya disimpan sebagai hash.\n");
} catch (PDOException $exception) {
    if ((string) $exception->getCode() === '23000') {
        fwrite(STDERR, "Email tersebut sudah terdaftar sebagai admin.\n");
        exit(1);
    }
    fwrite(STDERR, "Pembuatan akun admin gagal. Periksa koneksi database.\n");
    exit(1);
} catch (Throwable $exception) {
    fwrite(STDERR, "Pembuatan akun admin gagal: {$exception->getMessage()}\n");
    exit(1);
}
