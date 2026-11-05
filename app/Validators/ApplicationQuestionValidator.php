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
        $optionsRaw = isset($input['options']) && is_scalar($input['options'])
            ? trim((string) $input['options'])
            : '';
        $sortOrder = filter_var($input['sort_order'] ?? 0, FILTER_VALIDATE_INT);

        $optionList = array_values(array_filter(array_map('trim', explode("\n", $optionsRaw)), static fn($o) => $o !== ''));

        $values = [
            'question' => $question,
            'field_type' => $fieldType,
            'options' => implode("\n", $optionList),
            'is_required' => ($input['is_required'] ?? '') === '1' ? 1 : 0,
            'is_active' => ($input['is_active'] ?? '') === '1' ? 1 : 0,
            'sort_order' => $sortOrder === false ? 0 : max(-10000, min(10000, $sortOrder)),
        ];
        $errors = [];

        if ($question === '' || mb_strlen($question) > 250) {
            $errors['question'] = 'Pertanyaan wajib diisi, maksimal 250 karakter.';
        }
        $validTypes = ['TEXT', 'TEXTAREA', 'RADIO', 'CHECKBOX', 'SELECT', 'FILE'];
        if (!in_array($fieldType, $validTypes, true)) {
            $errors['field_type'] = 'Pilih jenis jawaban yang valid.';
        }

        if (in_array($fieldType, ['RADIO', 'CHECKBOX', 'SELECT'], true) && count($optionList) < 2) {
            $errors['options'] = 'Masukkan minimal 2 pilihan jawaban (satu pilihan per baris).';
        }

        return [$values, $errors];
    }
}
