<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;

final class TeamPhotoService
{
    private const MAX_BYTES = 2_097_152;

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

        $directory = PUBLIC_ROOT . '/uploads/team';
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            throw new RuntimeException('Folder foto tim tidak dapat disiapkan.');
        }
        $filename = bin2hex(random_bytes(16)) . '.' . $extensions[$mime];
        if (!move_uploaded_file((string) $file['tmp_name'], $directory . '/' . $filename)) {
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
