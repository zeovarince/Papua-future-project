<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\RateLimit;
use DateTimeImmutable;

final class RateLimiter
{
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
