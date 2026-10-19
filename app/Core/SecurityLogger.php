<?php

declare(strict_types=1);

namespace App\Core;

use App\Services\RateLimiter;
use Throwable;

final class SecurityLogger
{
    public static function record(string $event, Request $request, ?int $objectId = null): void
    {
        if (preg_match('/^[a-z][a-z0-9_]{2,63}$/', $event) !== 1) {
            return;
        }

        try {
            $directory = STORAGE_DIR . '/logs';
            if (!is_dir($directory) && !mkdir($directory, 0750, true) && !is_dir($directory)) {
                return;
            }

            $admin = Session::get('admin');
            $entry = [
                'time' => date(DATE_ATOM),
                'event' => $event,
                'admin_id' => is_array($admin) && isset($admin['id']) ? (int) $admin['id'] : null,
                'object_id' => $objectId,
                'ip_hash' => RateLimiter::identifier('security-ip|' . $request->ipAddress()),
            ];
            $line = json_encode($entry, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . "\n";
            $path = $directory . '/security.log';
            if (file_put_contents($path, $line, FILE_APPEND | LOCK_EX) !== strlen($line)) {
                return;
            }
            if (DIRECTORY_SEPARATOR !== '\\') {
                @chmod($path, 0600);
            }
        } catch (Throwable) {
            // Logging must never turn an otherwise safe request into a failure.
        }
    }
}
