<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;

final class ApplicationFileService
{
    private const MAX_PHOTO_BYTES = 2_097_152;
    private const MAX_CV_BYTES = 5_242_880;

    public function store(string $token, ?array $photo, ?array $cv): array
    {
        if (!preg_match('/^[a-f0-9]{32}$/', $token)) {
            throw new RuntimeException('Token penyimpanan berkas tidak valid.');
        }
        $root = STORAGE_DIR . '/applications';
        if (!is_dir($root) && !mkdir($root, 0750, true) && !is_dir($root)) {
            throw new RuntimeException('Penyimpanan berkas pendaftar tidak dapat disiapkan.');
        }
        $directory = $root . '/' . $token;
        if (file_exists($directory) || is_link($directory) || !mkdir($directory, 0750)) {
            throw new RuntimeException('Penyimpanan berkas pendaftar tidak dapat disiapkan.');
        }
        @chmod($directory, 0750);

        try {
            $this->storePhoto($photo, $directory . '/photo.webp');
            $this->storeCv($cv, $directory . '/cv.pdf');
            return [
                'photo_path' => 'applications/' . $token . '/photo.webp',
                'cv_path' => 'applications/' . $token . '/cv.pdf',
            ];
        } catch (\Throwable $exception) {
            $this->deleteToken($token);
            throw $exception;
        }
    }

    public function pathFor(array $application, string $kind): ?string
    {
        if (!in_array($kind, ['photo', 'cv'], true)) {
            return null;
        }
        $token = (string) ($application['file_token'] ?? '');
        if (!preg_match('/^[a-f0-9]{32}$/', $token)) {
            return null;
        }
        $filename = $kind === 'photo' ? 'photo.webp' : 'cv.pdf';
        $root = realpath(STORAGE_DIR . '/applications');
        $path = realpath(STORAGE_DIR . '/applications/' . $token . '/' . $filename);
        if ($root === false || $path === false || !is_file($path)) {
            return null;
        }
        $rootPrefix = rtrim($root, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        if (!str_starts_with(strtolower($path), strtolower($rootPrefix))) {
            return null;
        }
        return $path;
    }

    public function deleteToken(string $token): void
    {
        if (!preg_match('/^[a-f0-9]{32}$/', $token)) {
            return;
        }
        $directory = STORAGE_DIR . '/applications/' . $token;
        foreach (['photo.webp', 'cv.pdf'] as $filename) {
            $path = $directory . '/' . $filename;
            if (is_file($path)) {
                @unlink($path);
            }
        }
        if (is_dir($directory)) {
            @rmdir($directory);
        }
    }

    private function storePhoto(?array $file, string $destination): void
    {
        $temporaryPath = $this->validUpload($file, self::MAX_PHOTO_BYTES, 'Foto');
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($temporaryPath);
        $image = @getimagesize($temporaryPath);
        if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)
            || !$image || ($image['mime'] ?? '') !== $mime) {
            throw new RuntimeException('Foto harus berupa JPG, PNG, atau WebP yang valid.');
        }
        if ((int) ($image[0] ?? 0) > 8000 || (int) ($image[1] ?? 0) > 8000
            || (int) ($image[0] ?? 0) * (int) ($image[1] ?? 0) > 16_000_000) {
            throw new RuntimeException('Resolusi foto terlalu besar. Unggah foto maksimal 16 megapiksel.');
        }
        if (!function_exists('imagewebp')) {
            throw new RuntimeException('GD dengan dukungan WebP perlu diaktifkan untuk menyimpan foto pendaftar.');
        }
        $source = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($temporaryPath),
            'image/png' => @imagecreatefrompng($temporaryPath),
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($temporaryPath) : false,
        };
        if (!$source) {
            throw new RuntimeException('Foto tidak dapat dibaca. Gunakan JPG, PNG, atau WebP yang valid.');
        }

        if ($mime === 'image/jpeg' && function_exists('exif_read_data')) {
            $exif = @exif_read_data($temporaryPath);
            $orientation = (int) ($exif['Orientation'] ?? 1);
            $angle = match ($orientation) { 3 => 180, 6 => -90, 8 => 90, default => 0 };
            if ($angle !== 0) {
                $rotated = imagerotate($source, $angle, 0);
                if ($rotated !== false) {
                    imagedestroy($source);
                    $source = $rotated;
                }
            }
        }

        $width = imagesx($source);
        $height = imagesy($source);
        if ($width < 1 || $height < 1 || $width > 8000 || $height > 8000 || $width * $height > 16_000_000) {
            imagedestroy($source);
            throw new RuntimeException('Dimensi foto tidak valid atau terlalu besar.');
        }
        $scale = min(1, 1200 / max($width, $height));
        $outWidth = max(1, (int) round($width * $scale));
        $outHeight = max(1, (int) round($height * $scale));
        $output = imagecreatetruecolor($outWidth, $outHeight);
        imagealphablending($output, false);
        imagesavealpha($output, true);
        imagecopyresampled($output, $source, 0, 0, 0, 0, $outWidth, $outHeight, $width, $height);
        $saved = imagewebp($output, $destination, 82);
        imagedestroy($source);
        imagedestroy($output);
        if (!$saved) {
            throw new RuntimeException('Foto hasil optimasi tidak dapat disimpan.');
        }
        @chmod($destination, 0640);
    }

    private function storeCv(?array $file, string $destination): void
    {
        $temporaryPath = $this->validUpload($file, self::MAX_CV_BYTES, 'CV');
        $handle = @fopen($temporaryPath, 'rb');
        $signature = $handle === false ? false : fread($handle, 5);
        if (is_resource($handle)) {
            fclose($handle);
        }
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($temporaryPath);
        if ($signature !== '%PDF-' || $mime !== 'application/pdf') {
            throw new RuntimeException('CV harus berupa file PDF yang valid.');
        }
        if (!move_uploaded_file($temporaryPath, $destination)) {
            if (!copy($temporaryPath, $destination)) {
                throw new RuntimeException('CV tidak dapat disimpan. Coba unggah ulang.');
            }
        }
        @chmod($destination, 0640);
    }

    private function validUpload(?array $file, int $maximumBytes, string $label): string
    {
        if ($file === null || (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK
            || empty($file['tmp_name']) || !is_uploaded_file((string) $file['tmp_name'])) {
            throw new RuntimeException($label . ' gagal diunggah. Pilih kembali berkasnya.');
        }
        $size = (int) ($file['size'] ?? 0);
        if ($size < 1 || $size > $maximumBytes) {
            throw new RuntimeException($label . ' melebihi batas ukuran yang diizinkan.');
        }
        return (string) $file['tmp_name'];
    }
}
