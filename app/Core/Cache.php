<?php

declare(strict_types=1);

namespace App\Core;

final class Cache
{
    private const APCU_PREFIX = 'pfp.public.';
    private const FILE_PREFIX = 'pfp-public-';

    public static function remember(string $key, int $ttlSeconds, callable $resolver): mixed
    {
        if ($ttlSeconds < 1) {
            return $resolver();
        }

        $hash = hash('sha256', $key);
        $apcuKey = self::apcuPrefix() . $hash;

        if (self::apcuEnabled()) {
            try {
                $success = false;
                $entry = apcu_fetch($apcuKey, $success);
                if ($success && self::isFresh($entry)) {
                    return $entry['value'];
                }
            } catch (\Throwable) {
                // APCu is optional; fall back to the private file cache.
            }
        }

        $filePath = self::filePath($hash);
        $entry = self::readFile($filePath);
        if (self::isFresh($entry)) {
            if (self::apcuEnabled()) {
                try {
                    apcu_store($apcuKey, $entry, max(1, (int) $entry['expires_at'] - time()));
                } catch (\Throwable) {
                    // The file cache remains available if APCu is unavailable.
                }
            }
            return $entry['value'];
        }

        $value = $resolver();
        $entry = ['expires_at' => time() + $ttlSeconds, 'value' => $value];

        if (self::apcuEnabled()) {
            try {
                apcu_store($apcuKey, $entry, $ttlSeconds);
            } catch (\Throwable) {
                // Continue with the file cache.
            }
        }

        self::writeFile($filePath, $entry);
        return $value;
    }

    public static function clearPublic(): void
    {
        if (self::apcuEnabled() && function_exists('apcu_cache_info')) {
            try {
                $info = apcu_cache_info(true);
                foreach (($info['cache_list'] ?? []) as $item) {
                    $key = (string) ($item['info'] ?? '');
                    if (str_starts_with($key, self::apcuPrefix())) {
                        apcu_delete($key);
                    }
                }
            } catch (\Throwable) {
                // File cache invalidation below still applies.
            }
        }

        foreach (glob(STORAGE_DIR . '/cache/' . self::FILE_PREFIX . '*.cache') ?: [] as $filePath) {
            @unlink($filePath);
        }
    }

    private static function apcuEnabled(): bool
    {
        return function_exists('apcu_fetch')
            && (function_exists('apcu_enabled') ? apcu_enabled() : filter_var(ini_get('apc.enabled'), FILTER_VALIDATE_BOOL));
    }

    private static function apcuPrefix(): string
    {
        return self::APCU_PREFIX . substr(hash('sha256', APP_ROOT), 0, 12) . '.';
    }

    private static function filePath(string $hash): string
    {
        return STORAGE_DIR . '/cache/' . self::FILE_PREFIX . $hash . '.cache';
    }

    private static function readFile(string $filePath): mixed
    {
        if (!is_file($filePath)) {
            return null;
        }

        $contents = @file_get_contents($filePath);
        if (!is_string($contents) || $contents === '') {
            return null;
        }

        $entry = @unserialize($contents, ['allowed_classes' => false]);
        return is_array($entry) ? $entry : null;
    }

    private static function writeFile(string $filePath, array $entry): void
    {
        $directory = dirname($filePath);
        if (!is_dir($directory) && !@mkdir($directory, 0775, true) && !is_dir($directory)) {
            return;
        }

        try {
            $temporaryPath = $filePath . '.' . bin2hex(random_bytes(6)) . '.tmp';
            $contents = serialize($entry);
            if (@file_put_contents($temporaryPath, $contents, LOCK_EX) === false) {
                return;
            }
            if (!@rename($temporaryPath, $filePath)) {
                @unlink($temporaryPath);
            }
        } catch (\Throwable) {
            // A cache write failure must never make a public page unavailable.
        }
    }

    private static function isFresh(mixed $entry): bool
    {
        return is_array($entry)
            && array_key_exists('value', $entry)
            && (int) ($entry['expires_at'] ?? 0) > time();
    }
}
