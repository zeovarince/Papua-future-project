<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class RateLimit
{
    public static function countSince(string $bucket, string $identifier, string $since): int
    {
        $statement = Database::connection()->prepare(
            'SELECT COUNT(*) FROM rate_limits WHERE bucket = :bucket AND identifier = :identifier AND created_at >= :since'
        );
        $statement->execute(['bucket' => $bucket, 'identifier' => $identifier, 'since' => $since]);
        return (int) $statement->fetchColumn();
    }

    public static function record(string $bucket, string $identifier): void
    {
        $statement = Database::connection()->prepare(
            'INSERT INTO rate_limits (bucket, identifier) VALUES (:bucket, :identifier)'
        );
        $statement->execute(['bucket' => $bucket, 'identifier' => $identifier]);
    }

    public static function clear(string $bucket, string $identifier): void
    {
        $statement = Database::connection()->prepare(
            'DELETE FROM rate_limits WHERE bucket = :bucket AND identifier = :identifier'
        );
        $statement->execute(['bucket' => $bucket, 'identifier' => $identifier]);
    }
}
