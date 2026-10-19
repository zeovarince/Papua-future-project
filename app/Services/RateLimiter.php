<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\RateLimit;
use DateTimeImmutable;
use RuntimeException;

final class RateLimiter
{
    public static function identifier(string $value): string
    {
        $key = getenv('APP_KEY') ?: '';
        if (preg_match('/^[a-f0-9]{64}$/i', $key) !== 1) {
            throw new RuntimeException('APP_KEY harus berisi 32 byte acak dalam format heksadesimal.');
        }

        $binaryKey = hex2bin($key);
        if ($binaryKey === false) {
            throw new RuntimeException('APP_KEY tidak valid.');
        }

        return hash_hmac('sha256', $value, $binaryKey);
    }

    public function isLimited(string $bucket, string $identifier, int $limit, int $windowSeconds): bool
    {
        $since = (new DateTimeImmutable())->modify('-' . $windowSeconds . ' seconds')->format('Y-m-d H:i:s');
        return RateLimit::countSince($bucket, $identifier, $since) >= $limit;
    }

    public function hit(string $bucket, string $identifier): void
    {
        RateLimit::record($bucket, $identifier);
    }

    public function clear(string $bucket, string $identifier): void
    {
        RateLimit::clear($bucket, $identifier);
    }
}
