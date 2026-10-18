<?php

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

use App\Core\Database;
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

try {
    $pdo = Database::connection();
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS schema_migrations (' .
        'migration VARCHAR(190) NOT NULL PRIMARY KEY, ' .
        'applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP' .
        ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );

    $migrationFiles = glob(APP_ROOT . '/database/migrations/*.sql') ?: [];
    sort($migrationFiles, SORT_STRING);
    $applied = $pdo->prepare('SELECT 1 FROM schema_migrations WHERE migration = :migration');
    $record = $pdo->prepare('INSERT INTO schema_migrations (migration) VALUES (:migration)');

    foreach ($migrationFiles as $file) {
        $name = basename($file);
        $applied->execute(['migration' => $name]);
        if ($applied->fetchColumn()) {
            fwrite(STDOUT, "Lewati {$name} (sudah diterapkan).\n");
            continue;
        }

        $sql = file_get_contents($file);
        if ($sql === false) {
            throw new RuntimeException("Tidak dapat membaca migrasi {$name}.");
        }

        foreach (array_filter(array_map('trim', explode(';', $sql))) as $statement) {
            $pdo->exec($statement);
        }
        $record->execute(['migration' => $name]);
        fwrite(STDOUT, "Terapkan {$name}.\n");
    }

    fwrite(STDOUT, "Migrasi selesai.\n");
} catch (Throwable $exception) {
    fwrite(STDERR, "Migrasi gagal: {$exception->getMessage()}\n");
    exit(1);
}
