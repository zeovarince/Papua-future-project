<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;

final class ProgramImageService
{
    public const MAX_PHOTOS = 30;
    public const MAX_FILES_PER_UPLOAD = 20;
    private const MAX_BYTES = 8_388_608;

    public function store(?array $file, int $programId, string $area = 'programs'): ?string
    {
        if (!in_array($area, ['programs', 'posts'], true)) throw new RuntimeException('Lokasi gambar tidak valid.');
        if ($file === null || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || !isset($file['tmp_name'], $file['size']) || !is_uploaded_file((string) $file['tmp_name'])) {
            throw new RuntimeException('Gambar gagal diunggah. Periksa file dan coba kembali.');
        }
        if ((int) $file['size'] < 1 || (int) $file['size'] > self::MAX_BYTES) throw new RuntimeException('Ukuran setiap gambar maksimal 8 MB.');
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file((string) $file['tmp_name']);
        $imageInfo = @getimagesize((string) $file['tmp_name']);
        if (!in_array($mime, ['image/jpeg','image/png','image/webp'], true) || !$imageInfo || ($imageInfo['mime'] ?? '') !== $mime) {
            throw new RuntimeException('Gambar harus berupa JPEG, PNG, atau WebP yang valid.');
        }
        if ($imageInfo[0] > 12000 || $imageInfo[1] > 12000) throw new RuntimeException('Dimensi gambar maksimal 12.000 × 12.000 piksel.');
        if (!function_exists('imagewebp')) throw new RuntimeException('Ekstensi GD dengan dukungan WebP perlu diaktifkan di PHP hosting.');
        $source = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg((string) $file['tmp_name']),
            'image/png' => @imagecreatefrompng((string) $file['tmp_name']),
            'image/webp' => @imagecreatefromwebp((string) $file['tmp_name']),
        };
        if (!$source) throw new RuntimeException('Gambar tidak dapat diproses.');
        if ($mime === 'image/jpeg' && function_exists('exif_read_data')) {
            $exif = @exif_read_data((string) $file['tmp_name']);
            $orientation = (int) ($exif['Orientation'] ?? 1);
            $angle = match ($orientation) { 3 => 180, 6 => -90, 8 => 90, default => 0 };
            if ($angle !== 0) {
                $rotated = imagerotate($source, $angle, 0);
                if ($rotated !== false) { imagedestroy($source); $source = $rotated; }
            }
        }
        $width = imagesx($source); $height = imagesy($source);
        $scale = min(1, 1600 / max($width, $height));
        $outWidth = max(1, (int) round($width * $scale)); $outHeight = max(1, (int) round($height * $scale));
        $optimized = imagecreatetruecolor($outWidth, $outHeight);
        imagealphablending($optimized, false); imagesavealpha($optimized, true);
        imagecopyresampled($optimized, $source, 0, 0, 0, 0, $outWidth, $outHeight, $width, $height);
        $directory = PUBLIC_ROOT . '/uploads/' . $area . '/' . $programId;
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            imagedestroy($source); imagedestroy($optimized); throw new RuntimeException('Folder gambar program tidak dapat disiapkan.');
        }
        $filename = bin2hex(random_bytes(16)) . '.webp';
        $fullPath = $directory . '/' . $filename;
        $thumbPath = $directory . '/' . basename($filename, '.webp') . '-thumb.webp';
        $saved = imagewebp($optimized, $fullPath, 82);
        if ($saved) {
            $thumbScale = min(1, 480 / max(imagesx($optimized), imagesy($optimized)));
            $thumbWidth = max(1, (int) round(imagesx($optimized) * $thumbScale));
            $thumbHeight = max(1, (int) round(imagesy($optimized) * $thumbScale));
            $thumb = imagecreatetruecolor($thumbWidth, $thumbHeight);
            imagealphablending($thumb, false); imagesavealpha($thumb, true);
            imagecopyresampled($thumb, $optimized, 0, 0, 0, 0, $thumbWidth, $thumbHeight, imagesx($optimized), imagesy($optimized));
            $saved = imagewebp($thumb, $thumbPath, 78);
            imagedestroy($thumb);
        }
        imagedestroy($source); imagedestroy($optimized);
        if (!$saved) { @unlink($fullPath); @unlink($thumbPath); throw new RuntimeException('Gambar hasil optimasi tidak dapat disimpan.'); }
        return $area . '/' . $programId . '/' . $filename;
    }

    public function storeMultiple(?array $files, int $programId, int $available, string $area = 'programs'): array
    {
        if ($files === null || !isset($files['name']) || !is_array($files['name'])) return [];
        $count = count(array_filter($files['name'], static fn ($name) => (string) $name !== ''));
        if ($count > self::MAX_FILES_PER_UPLOAD) throw new RuntimeException('Maksimal 20 foto dalam satu kali unggah.');
        if ($count > $available) throw new RuntimeException('Jumlah foto melebihi batas 30 foto per program.');
        $stored = [];
        try {
            foreach ($files['name'] as $index => $name) {
                if ((string) $name === '') continue;
                $file = ['name'=>$name, 'tmp_name'=>$files['tmp_name'][$index] ?? '', 'error'=>$files['error'][$index] ?? UPLOAD_ERR_NO_FILE, 'size'=>$files['size'][$index] ?? 0, 'type'=>$files['type'][$index] ?? ''];
                $stored[] = $this->store($file, $programId, $area);
            }
        } catch (\Throwable $exception) {
            foreach ($stored as $path) $this->delete($path);
            throw $exception;
        }
        return $stored;
    }

    public function delete(?string $path): void
    {
        if ($path === null || !preg_match('~^(?:programs|posts)/[1-9][0-9]*/[a-f0-9]{32}\.webp$~', $path)) return;
        $target = PUBLIC_ROOT . '/uploads/' . $path;
        if (is_file($target)) @unlink($target);
        $thumb = preg_replace('/\.webp$/', '-thumb.webp', $target);
        if (is_string($thumb) && is_file($thumb)) @unlink($thumb);
    }

    public function deleteProgramFiles(int $programId, array $program, array $photos): void
    {
        $this->deleteItemFiles('programs', $programId, $program['cover_image'] ?? null, $photos);
    }

    public function deleteItemFiles(string $area, int $itemId, ?string $cover, array $photos): void
    {
        if (!in_array($area, ['programs', 'posts'], true)) return;
        $this->delete($cover);
        foreach ($photos as $photo) $this->delete($photo['file_path'] ?? null);
        $directory = PUBLIC_ROOT . '/uploads/' . $area . '/' . $itemId;
        if (is_dir($directory) && count(scandir($directory) ?: []) <= 2) @rmdir($directory);
    }
}
