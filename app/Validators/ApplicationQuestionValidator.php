<?php

declare(strict_types=1);

namespace App\Validators;

final class ApplicationQuestionValidator
{
    public static function validate(array $input): array
    {
        $question = isset($input['question']) && is_scalar($input['question'])
            ? trim((string) $input['question'])
            : '';
        $fieldType = isset($input['field_type']) && is_scalar($input['field_type'])
            ? (string) $input['field_type']
            : '';
        $sortOrder = filter_var($input['sort_order'] ?? 0, FILTER_VALIDATE_INT);
        $values = [
            'question' => $question,
            'field_type' => $fieldType,
            'is_required' => ($input['is_required'] ?? '') === '1' ? 1 : 0,
            'is_active' => ($input['is_active'] ?? '') === '1' ? 1 : 0,
            'sort_order' => $sortOrder === false ? 0 : max(-10000, min(10000, $sortOrder)),
        ];
        $errors = [];

        if ($question === '' || mb_strlen($question) > 250) {
            $errors['question'] = 'Pertanyaan wajib diisi, maksimal 250 karakter.';
        }
        if (!in_array($fieldType, ['TEXT', 'TEXTAREA'], true)) {
            $errors['field_type'] = 'Pilih jenis jawaban yang valid.';
        }

        return [$values, $errors];
    }
}
