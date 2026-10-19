<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;

final class TeamPhotoService
{
    private const MAX_BYTES = 2_097_152;
    private const MAX_PIXELS = 16_000_000;
    private const MAX_SIDE = 8_000;
    private const OUTPUT_SIDE = 1_200;

    public function store(?array $file): ?string
    {
        if ($file === null || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || !isset($file['tmp_name'], $file['size'])) {
            throw new RuntimeException('Foto tidak berhasil diunggah. Coba pilih ulang file.');
        }
        if ((int) $file['size'] < 1 || (int) $file['size'] > self::MAX_BYTES || !is_uploaded_file((string) $file['tmp_name'])) {
            throw new RuntimeException('Ukuran foto maksimal 2 MB.');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file((string) $file['tmp_name']);
        $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $image = @getimagesize((string) $file['tmp_name']);
        if (!is_string($mime) || !isset($extensions[$mime]) || $image === false || $image['mime'] !== $mime) {
            throw new RuntimeException('Foto harus berupa JPG, PNG, atau WebP yang valid.');
        }
        $width = (int) $image[0];
        $height = (int) $image[1];
        if ($width < 1 || $height < 1 || $width > self::MAX_SIDE || $height > self::MAX_SIDE || $width * $height > self::MAX_PIXELS) {
            throw new RuntimeException('Resolusi foto terlalu besar. Unggah foto maksimal 16 megapiksel.');
        }
        if (!function_exists('imagewebp')) {
            throw new RuntimeException('Ekstensi GD dengan dukungan WebP perlu diaktifkan di PHP hosting.');
        }

        $source = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg((string) $file['tmp_name']),
            'image/png' => @imagecreatefrompng((string) $file['tmp_name']),
            'image/webp' => @imagecreatefromwebp((string) $file['tmp_name']),
        };
        if (!$source) {
            throw new RuntimeException('Foto tidak dapat diproses.');
        }
        if ($mime === 'image/jpeg' && function_exists('exif_read_data')) {
            $exif = @exif_read_data((string) $file['tmp_name']);
            $angle = match ((int) ($exif['Orientation'] ?? 1)) { 3 => 180, 6 => -90, 8 => 90, default => 0 };
            if ($angle !== 0) {
                $rotated = imagerotate($source, $angle, 0);
                if ($rotated !== false) {
                    imagedestroy($source);
                    $source = $rotated;
                }
            }
        }

        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);
        $scale = min(1, self::OUTPUT_SIDE / max($sourceWidth, $sourceHeight));
        $outputWidth = max(1, (int) round($sourceWidth * $scale));
        $outputHeight = max(1, (int) round($sourceHeight * $scale));
        $output = imagecreatetruecolor($outputWidth, $outputHeight);
        imagealphablending($output, false);
        imagesavealpha($output, true);
        imagecopyresampled($output, $source, 0, 0, 0, 0, $outputWidth, $outputHeight, $sourceWidth, $sourceHeight);
        imagedestroy($source);

        $directory = PUBLIC_ROOT . '/uploads/team';
        if (!is_dir($directory) && !mkdir($directory, 0750, true) && !is_dir($directory)) {
            imagedestroy($output);
            throw new RuntimeException('Folder foto tim tidak dapat disiapkan.');
        }
        $filename = bin2hex(random_bytes(16)) . '.webp';
        $saved = imagewebp($output, $directory . '/' . $filename, 82);
        imagedestroy($output);
        if (!$saved) {
            throw new RuntimeException('Foto tidak dapat disimpan di server.');
        }

        return 'team/' . $filename;
    }

    public function delete(?string $path): void
    {
        if ($path === null || !preg_match('~^team/[a-f0-9]{32}\.(?:jpg|png|webp)$~', $path)) {
            return;
        }
        $target = PUBLIC_ROOT . '/uploads/' . $path;
        if (is_file($target)) {
            unlink($target);
        }
    }
}
