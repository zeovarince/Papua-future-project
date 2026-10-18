<?php

declare(strict_types=1);

namespace App\Validators;

final class AboutContentValidator
{
    public static function validate(array $input): array
    {
        $data = [
            'story' => self::text($input['story'] ?? ''),
            'vision' => self::text($input['vision'] ?? ''),
            'mission' => self::text($input['mission'] ?? ''),
        ];
        $errors = [];
        if ($data['story'] === '' || mb_strlen($data['story']) > 60000) {
            $errors['story'] = 'Cerita wajib diisi dan maksimal 60.000 karakter.';
        }
        if ($data['vision'] === '' || mb_strlen($data['vision']) > 5000) {
            $errors['vision'] = 'Visi wajib diisi dan maksimal 5.000 karakter.';
        }
        if ($data['mission'] === '' || mb_strlen($data['mission']) > 10000) {
            $errors['mission'] = 'Misi wajib diisi, satu butir per baris, maksimal 10.000 karakter.';
        }
        return [$data, $errors];
    }

    private static function text(mixed $value): string
    {
        return is_scalar($value) ? trim((string) $value) : '';
    }
}
