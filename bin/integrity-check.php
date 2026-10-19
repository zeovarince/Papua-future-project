<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

set_error_handler(static function (int $severity, string $message, string $file, int $line): bool {
    if ((error_reporting() & $severity) !== 0) {
        throw new ErrorException($message, 0, $severity, $file, $line);
    }

    return false;
});

$appRoot = realpath(dirname(__DIR__));
if ($appRoot === false) {
    fwrite(STDERR, "Akar aplikasi tidak ditemukan.\n");
    exit(1);
}

$localPublicRoot = $appRoot . '/public';
$publicCandidate = is_dir($localPublicRoot)
    ? $localPublicRoot
    : dirname($appRoot) . '/public_html';
$publicRoot = realpath($publicCandidate);
if ($publicRoot === false) {
    fwrite(STDERR, "Folder public/ atau public_html/ tidak ditemukan.\n");
    exit(1);
}

$uploadsDir = $publicRoot . '/uploads';
$uploadsRoot = is_dir($uploadsDir) ? realpath($uploadsDir) : null;
$storageRoot = $appRoot . '/storage';
$baselineFile = $storageRoot . '/integrity-baseline.json';
$scriptPath = realpath(__FILE__) ?: __FILE__;
$extensions = [
    'php', 'phtml', 'phar', 'html', 'htm', 'js', 'css', 'ini', 'htaccess', 'xml', 'svg', 'json',
    'webmanifest', 'yml', 'yaml', 'toml', 'map', 'sql', 'txt',
];
$roots = [
    'app' => $appRoot . '/app',
    'config' => $appRoot . '/config',
    'bin' => $appRoot . '/bin',
    'public' => $publicRoot,
];
$rootFiles = [
    'bootstrap.php', 'composer.json', 'composer.lock', '.htaccess', '.env', '.cpanel.yml', '.user.ini',
    'storage/.htaccess', 'storage/applications/.htaccess',
];

/**
 * @param array<string, string> $roots
 * @param list<string> $extensions
 * @param list<string> $rootFiles
 * @return array<string, array{path: string, hash: string}>
 */
function collect_files(array $roots, array $extensions, array $rootFiles, string $appRoot, ?string $uploadsRoot): array
{
    $records = [];

    foreach ($rootFiles as $filename) {
        $path = $appRoot . DIRECTORY_SEPARATOR . $filename;
        if (is_link($path)) {
            $records['root/' . $filename] = [
                'path' => $path,
                'hash' => 'SYMLINK',
            ];
        } elseif (is_file($path)) {
            $records['root/' . $filename] = [
                'path' => $path,
                'hash' => hash_file_or_fail($path),
            ];
        }
    }

    foreach ($roots as $label => $root) {
        $realRoot = realpath($root);
        if ($realRoot === false || !is_dir($realRoot)) {
            throw new RuntimeException('Folder pemantauan tidak ditemukan: ' . $label);
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($realRoot, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if (!($file instanceof SplFileInfo)) {
                continue;
            }

            $path = $file->getPathname();
            if ($file->isLink()) {
                $relativePath = relative_path($realRoot, $path);
                $records[$label . '/' . $relativePath] = [
                    'path' => $path,
                    'hash' => 'SYMLINK',
                ];
                continue;
            }
            if (!$file->isFile()) {
                continue;
            }

            $realPath = realpath($path);
            if ($realPath === false) {
                throw new RuntimeException('Jalur file tidak dapat dibaca: ' . $path);
            }

            if ($label === 'public' && $uploadsRoot !== null && is_path_inside($realPath, $uploadsRoot)) {
                continue;
            }

            if (!in_array(strtolower($file->getExtension()), $extensions, true)) {
                continue;
            }

            $relativePath = relative_path($realRoot, $path);
            $key = $label . '/' . $relativePath;
            $records[$key] = [
                'path' => $path,
                'hash' => hash_file_or_fail($path),
            ];
        }
    }

    ksort($records, SORT_STRING);
    return $records;
}

function hash_file_or_fail(string $path): string
{
    $hash = hash_file('sha256', $path);
    if (!is_string($hash)) {
        throw new RuntimeException('Hash file gagal dibuat: ' . $path);
    }

    return $hash;
}

function relative_path(string $root, string $path): string
{
    $root = rtrim(str_replace('\\', '/', $root), '/') . '/';
    $path = str_replace('\\', '/', $path);
    $comparisonRoot = DIRECTORY_SEPARATOR === '\\' ? strtolower($root) : $root;
    $comparisonPath = DIRECTORY_SEPARATOR === '\\' ? strtolower($path) : $path;

    if (!str_starts_with($comparisonPath, $comparisonRoot)) {
        throw new RuntimeException('File keluar dari folder pemantauan: ' . $path);
    }

    return substr($path, strlen($root));
}

function is_path_inside(string $path, string $directory): bool
{
    $path = rtrim(str_replace('\\', '/', $path), '/') . '/';
    $directory = rtrim(str_replace('\\', '/', $directory), '/') . '/';
    if (DIRECTORY_SEPARATOR === '\\') {
        $path = strtolower($path);
        $directory = strtolower($directory);
    }

    return str_starts_with($path, $directory);
}

/**
 * @param array<string, array{path: string, hash: string}> $records
 * @return list<string>
 */
function inspect_current_files(array $records, string $scriptPath, string $uploadsDir, ?string $uploadsRoot): array
{
    $problems = [];
    $dangerPatterns = [
        '/\beval\s*\(\s*(?:base64_decode|gzinflate|gzuncompress|str_rot13)/i',
        '/\b(?:base64_decode|gzinflate)\s*\(\s*[\'\"][A-Za-z0-9+\/=]{200,}/i',
        '/\b(?:shell_exec|passthru|proc_open|popen)\s*\(/i',
        '/\bassert\s*\(\s*\$_(?:GET|POST|REQUEST|COOKIE)/i',
    ];
    $judolPattern = '/\b(slot\s*gacor|gacor|togel|judi\s*online|judol|maxwin|situs\s*slot|slot88|rtp\s*live|sbobet|bandar\s*togel|toto\s*macau)\b/i';

    foreach ($records as $record) {
        $path = $record['path'];
        if ($record['hash'] === 'SYMLINK') {
            $problems[] = 'SYMLINK: tautan simbolik ditemukan: ' . $path;
            continue;
        }
        $realPath = realpath($path) ?: $path;
        if ($realPath === $scriptPath) {
            continue;
        }

        $size = filesize($path);
        if (!is_int($size) || $size > 20 * 1024 * 1024) {
            $problems[] = 'PEMINDAIAN: ukuran file tidak dapat dipindai: ' . $path;
            continue;
        }

        $content = file_get_contents($path);
        if (!is_string($content)) {
            $problems[] = 'PEMINDAIAN: file tidak dapat dibaca: ' . $path;
            continue;
        }

        foreach ($dangerPatterns as $pattern) {
            if (preg_match($pattern, $content) === 1) {
                $problems[] = 'POLA: kode mencurigakan di ' . $path;
                break;
            }
        }

        if (preg_match($judolPattern, $content) === 1) {
            $problems[] = 'JUDOL: kata kunci mencurigakan di ' . $path;
        }
    }

    if (is_link($uploadsDir)) {
        $problems[] = 'UPLOADS: folder uploads/ berupa symbolic link.';
        return $problems;
    }

    if ($uploadsRoot !== null) {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($uploadsRoot, RecursiveDirectoryIterator::SKIP_DOTS)
        );
        $allowedMimeByExtension = [
            'gif' => ['image/gif'],
            'jpeg' => ['image/jpeg'],
            'jpg' => ['image/jpeg'],
            'png' => ['image/png'],
            'webp' => ['image/webp'],
        ];
        $fileInfo = new finfo(FILEINFO_MIME_TYPE);

        foreach ($iterator as $file) {
            if (!($file instanceof SplFileInfo) || $file->getFilename() === '.gitkeep') {
                continue;
            }
            if ($file->isLink()) {
                $problems[] = 'UPLOADS: symbolic link ditemukan: ' . $file->getPathname();
                continue;
            }
            if (!$file->isFile() || $file->getFilename() === '.htaccess') {
                continue;
            }
            if (!is_readable($file->getPathname())) {
                $problems[] = 'UPLOADS: file tidak dapat dibaca: ' . $file->getPathname();
                continue;
            }

            $extension = strtolower($file->getExtension());
            $mime = @$fileInfo->file($file->getPathname());
            $image = @getimagesize($file->getPathname());
            if (!isset($allowedMimeByExtension[$extension])
                || !in_array($mime, $allowedMimeByExtension[$extension], true)
                || $image === false) {
                $problems[] = 'UPLOADS: file bukan gambar valid: ' . $file->getPathname();
            }
        }
    }

    return $problems;
}

/**
 * @param array<string, array{path: string, hash: string}> $records
 * @return array<string, string>
 */
function hashes_from_records(array $records): array
{
    $hashes = [];
    foreach ($records as $key => $record) {
        $hashes[$key] = $record['hash'];
    }
    return $hashes;
}

function print_problems(array $problems): never
{
    echo 'PERINGATAN integritas ' . date('Y-m-d H:i:s') . "\n";
    echo implode("\n", array_values(array_unique($problems))) . "\n";
    exit(2);
}

function write_baseline(string $baselineFile, array $hashes): void
{
    $directory = dirname($baselineFile);
    if (!is_dir($directory) && !mkdir($directory, 0750, true) && !is_dir($directory)) {
        throw new RuntimeException('Folder storage/ tidak dapat disiapkan.');
    }

    $json = json_encode($hashes, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . "\n";
    $temporaryFile = $baselineFile . '.' . bin2hex(random_bytes(8)) . '.tmp';
    if (file_put_contents($temporaryFile, $json, LOCK_EX) !== strlen($json)) {
        throw new RuntimeException('File acuan sementara gagal ditulis.');
    }
    if (DIRECTORY_SEPARATOR !== '\\' && !chmod($temporaryFile, 0600)) {
        unlink($temporaryFile);
        throw new RuntimeException('Izin file acuan tidak dapat diatur ke 0600.');
    }

    if (DIRECTORY_SEPARATOR === '\\' && is_file($baselineFile)) {
        $backupFile = $baselineFile . '.' . bin2hex(random_bytes(8)) . '.bak';
        if (!rename($baselineFile, $backupFile)) {
            unlink($temporaryFile);
            throw new RuntimeException('Acuan lama tidak dapat diganti.');
        }
        if (!rename($temporaryFile, $baselineFile)) {
            rename($backupFile, $baselineFile);
            unlink($temporaryFile);
            throw new RuntimeException('Acuan baru tidak dapat dipasang.');
        }
        unlink($backupFile);
        return;
    }

    if (!rename($temporaryFile, $baselineFile)) {
        unlink($temporaryFile);
        throw new RuntimeException('Acuan baru tidak dapat dipasang.');
    }
}

try {
    $arguments = array_slice($argv, 1);
    if (count($arguments) > 1 || ($arguments !== [] && !in_array($arguments[0], ['--init', '--scan'], true))) {
        fwrite(STDERR, "Pemakaian: php bin/integrity-check.php [--scan|--init]\n");
        exit(64);
    }

    $mode = $arguments[0] ?? '--check';
    $records = collect_files($roots, $extensions, $rootFiles, $appRoot, $uploadsRoot);
    $scanProblems = inspect_current_files($records, $scriptPath, $uploadsDir, $uploadsRoot);

    if ($mode === '--scan') {
        if ($scanProblems !== []) {
            print_problems($scanProblems);
        }
        exit(0);
    }

    $hashes = hashes_from_records($records);
    if ($mode === '--init') {
        if ($scanProblems !== []) {
            print_problems($scanProblems);
        }
        write_baseline($baselineFile, $hashes);
        echo 'Acuan integritas dibuat untuk ' . count($hashes) . " berkas.\n";
        exit(0);
    }

    if (!is_file($baselineFile)) {
        fwrite(STDERR, "Acuan belum ada. Audit kondisi bersih, lalu jalankan --init.\n");
        exit(1);
    }

    $baselineContent = file_get_contents($baselineFile);
    $baseline = is_string($baselineContent) ? json_decode($baselineContent, true) : null;
    if (!is_array($baseline)) {
        fwrite(STDERR, "Berkas acuan rusak. Jangan timpa sebelum kondisi situs diperiksa.\n");
        exit(1);
    }

    foreach ($baseline as $path => $hash) {
        if (!is_string($path) || !is_string($hash) || preg_match('/^[a-f0-9]{64}$/', $hash) !== 1
            || str_starts_with($path, '/') || str_contains($path, '../') || str_contains($path, "\\")) {
            fwrite(STDERR, "Format berkas acuan tidak valid. Jangan timpa sebelum kondisi situs diperiksa.\n");
            exit(1);
        }
    }

    $problems = $scanProblems;
    foreach ($hashes as $path => $hash) {
        if (!array_key_exists($path, $baseline)) {
            $problems[] = 'BARU: ' . $path;
        } elseif (!hash_equals((string) $baseline[$path], $hash)) {
            $problems[] = 'BERUBAH: ' . $path;
        }
    }
    foreach ($baseline as $path => $_hash) {
        if (!array_key_exists($path, $hashes)) {
            $problems[] = 'HILANG: ' . $path;
        }
    }

    if ($problems !== []) {
        print_problems($problems);
    }
} catch (Throwable $exception) {
    fwrite(STDERR, 'Pemeriksaan integritas gagal: ' . $exception->getMessage() . "\n");
    exit(1);
}
