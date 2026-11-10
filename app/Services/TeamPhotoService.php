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
        $extensions = ['image/png' => 'png', 'image/webp' => 'webp'];
        $image = @getimagesize((string) $file['tmp_name']);
        if (!is_string($mime) || !isset($extensions[$mime]) || $image === false || $image['mime'] !== $mime) {
            throw new RuntimeException('Unggah PNG atau WebP yang valid dan sudah memiliki latar transparan.');
        }
        $width = (int) $image[0];
        $height = (int) $image[1];
        if ($width < 1 || $height < 1 || $width > self::MAX_SIDE || $height > self::MAX_SIDE || $width * $height > self::MAX_PIXELS) {
            throw new RuntimeException('Resolusi foto terlalu besar. Unggah foto maksimal 16 megapiksel.');
        }
        if (!function_exists('imagewebp')) {
            throw new RuntimeException('Ekstensi GD dengan dukungan WebP perlu diaktifkan di PHP hosting.');
        }

        $sourcePath = (string) $file['tmp_name'];
        if (!$this->hasTransparency($sourcePath, $mime)) {
            throw new RuntimeException('Latar foto belum transparan. Hapus latar secara manual, lalu unggah ulang sebagai PNG atau WebP.');
        }

        $source = @imagecreatefromstring((string) file_get_contents($sourcePath));
        if (!$source) {
            throw new RuntimeException('Foto tidak dapat diproses.');
        }

        $source = $this->cropTransparentPadding($source);
        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);
        $scale = min(1, self::OUTPUT_SIDE / max($sourceWidth, $sourceHeight));
        $outputWidth = max(1, (int) round($sourceWidth * $scale));
        $outputHeight = max(1, (int) round($sourceHeight * $scale));
        $output = imagecreatetruecolor($outputWidth, $outputHeight);
        imagealphablending($output, false);
        imagesavealpha($output, true);
        $transparent = imagecolorallocatealpha($output, 0, 0, 0, 127);
        imagefilledrectangle($output, 0, 0, $outputWidth, $outputHeight, $transparent);
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

    /** Remove excess transparent canvas while keeping a small clear margin around the cutout. */
    private function cropTransparentPadding(\GdImage $image): \GdImage
    {
        $width = imagesx($image);
        $height = imagesy($image);
        $step = max(1, (int) ceil(sqrt(($width * $height) / 300_000)));
        $minX = $width;
        $minY = $height;
        $maxX = -1;
        $maxY = -1;

        for ($y = 0; $y < $height; $y += $step) {
            for ($x = 0; $x < $width; $x += $step) {
                $pixel = imagecolorat($image, $x, $y);
                $alpha = imageistruecolor($image)
                    ? (($pixel >> 24) & 0x7F)
                    : (imagecolorsforindex($image, $pixel)['alpha'] ?? 0);

                if ($alpha >= 127) {
                    continue;
                }

                $minX = min($minX, $x);
                $minY = min($minY, $y);
                $maxX = max($maxX, $x);
                $maxY = max($maxY, $y);
            }
        }

        if ($maxX < $minX || $maxY < $minY) {
            return $image;
        }

        $subjectWidth = $maxX - $minX + 1;
        $subjectHeight = $maxY - $minY + 1;
        $paddingX = max($step * 2, (int) ceil($subjectWidth * 0.12));
        $paddingY = max($step * 2, (int) ceil($subjectHeight * 0.15));
        $left = max(0, $minX - $paddingX);
        $top = max(0, $minY - $paddingY);
        $right = min($width - 1, $maxX + $paddingX);
        $bottom = min($height - 1, $maxY + $paddingY);
        $cropped = @imagecrop($image, [
            'x' => $left,
            'y' => $top,
            'width' => $right - $left + 1,
            'height' => $bottom - $top + 1,
        ]);

        if (!$cropped) {
            return $image;
        }

        imagedestroy($image);
        return $cropped;
    }

    private function hasTransparency(string $path, string $mime): bool
    {
        if (!in_array($mime, ['image/png', 'image/webp'], true)) {
            return false;
        }

        $image = @imagecreatefromstring((string) file_get_contents($path));
        if (!$image) {
            return false;
        }

        $width = imagesx($image);
        $height = imagesy($image);
        $step = max(1, (int) ceil(sqrt(($width * $height) / 100_000)));
        $transparentFound = false;

        for ($y = 0; $y < $height && !$transparentFound; $y += $step) {
            for ($x = 0; $x < $width; $x += $step) {
                $pixel = imagecolorat($image, $x, $y);
                $alpha = imageistruecolor($image)
                    ? (($pixel >> 24) & 0x7F)
                    : (imagecolorsforindex($image, $pixel)['alpha'] ?? 0);
                if ($alpha > 0) {
                    $transparentFound = true;
                    break;
                }
            }
        }

        imagedestroy($image);
        return $transparentFound;
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
