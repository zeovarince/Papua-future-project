<?php

declare(strict_types=1);

namespace App\Validators;

final class BatchValidator
{
    public static function validate(array $input): array
    {
        $typeValue = $input['batch_type'] ?? '';
        $nameValue = $input['name'] ?? '';
        $type = strtoupper(is_scalar($typeValue) ? trim((string) $typeValue) : '');
        $name = is_scalar($nameValue) ? trim((string) $nameValue) : '';
        $errors = [];
        if (!in_array($type, ['INTERN', 'VOLUNTEER'], true)) {
            $errors['batch_type'] = 'Pilih jenis batch yang valid.';
        }
        if ($name === '' || mb_strlen($name) > 60) {
            $errors['name'] = 'Nama batch wajib diisi, maksimal 60 karakter.';
        }
        return [['batch_type' => $type, 'name' => $name], $errors];
    }
}
