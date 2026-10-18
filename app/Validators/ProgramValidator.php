<?php

declare(strict_types=1);

namespace App\Validators;

final class ProgramValidator
{
    public static function validate(array $input, ?int $id = null): array
    {
        $fields = ['slug','category_label','title','summary','tagline','body','activities','partners','stat1_value','stat1_label','stat2_value','stat2_label'];
        $data = [];
        foreach ($fields as $field) $data[$field] = isset($input[$field]) && is_scalar($input[$field]) ? trim((string) $input[$field]) : '';
        $slug = strtolower($data['slug']);
        $slug = trim((string) preg_replace('/[^a-z0-9]+/', '-', $slug), '-');
        $data['slug'] = $slug;
        $sort = filter_var($input['sort_order'] ?? 0, FILTER_VALIDATE_INT);
        $data['sort_order'] = $sort === false ? 0 : max(-10000, min(10000, $sort));
        $data['is_featured'] = !empty($input['is_featured']) ? 1 : 0;
        $data['is_published'] = !empty($input['is_published']) ? 1 : 0;
        $rows = [];
        $rowsText = isset($input['info_rows']) && is_scalar($input['info_rows']) ? trim((string) $input['info_rows']) : '';
        foreach (preg_split('/\R/', $rowsText) ?: [] as $line) {
            if (trim($line) === '') continue;
            [$label, $value] = array_pad(explode('|', $line, 2), 2, '');
            if (trim($label) !== '' && trim($value) !== '') $rows[] = ['label' => trim($label), 'value' => trim($value)];
        }
        $data['info_rows'] = $rows === [] ? null : json_encode(array_slice($rows, 0, 8), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        $errors = [];
        foreach (['category_label'=>40,'title'=>160,'summary'=>400,'tagline'=>220] as $field => $max) {
            if ($data[$field] === '' || mb_strlen($data[$field]) > $max) $errors[$field] = "Wajib diisi, maksimal {$max} karakter.";
        }
        if ($data['body'] === '') $errors['body'] = 'Isi deskripsi program.';
        if ($slug === '' || strlen($slug) > 160) $errors['slug'] = 'Slug wajib diisi dan maksimal 160 karakter.';
        return [$data, $errors];
    }
}
