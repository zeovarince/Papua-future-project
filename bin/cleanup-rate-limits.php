<?php

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

use App\Core\Database;
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

try {
    $statement = Database::connection()->prepare('DELETE FROM rate_limits WHERE created_at < :cutoff');
    $statement->execute(['cutoff' => (new DateTimeImmutable('-1 day'))->format('Y-m-d H:i:s')]);
    fwrite(STDOUT, sprintf("%d catatan batas laju kedaluwarsa dihapus.\n", $statement->rowCount()));
} catch (Throwable $exception) {
    fwrite(STDERR, "Pembersihan gagal: {$exception->getMessage()}\n");
    exit(1);
}
