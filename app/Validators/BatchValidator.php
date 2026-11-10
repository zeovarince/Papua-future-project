<?php

declare(strict_types=1);

namespace App\Validators;

final class BatchValidator
{
    public static function validate(array $input): array
    {
        $typeValue = $input['batch_type'] ?? '';
        $nameValue = $input['name'] ?? '';
        $formUrlValue = $input['google_form_url'] ?? '';
        $type = strtoupper(is_scalar($typeValue) ? trim((string) $typeValue) : '');
        $name = is_scalar($nameValue) ? trim((string) $nameValue) : '';
        $formUrl = is_scalar($formUrlValue) ? trim((string) $formUrlValue) : '';
        $errors = [];
        if (!in_array($type, ['INTERN', 'VOLUNTEER'], true)) {
            $errors['batch_type'] = 'Pilih jenis batch yang valid.';
        }
        if ($name === '' || mb_strlen($name) > 60) {
            $errors['name'] = 'Nama batch wajib diisi, maksimal 60 karakter.';
        }
        if ($formUrl !== '' && !self::isRegistrationUrl($formUrl)) {
            $errors['google_form_url'] = 'Masukkan URL pendaftaran yang valid dan diawali http:// atau https://.';
        }
        return [['batch_type' => $type, 'name' => $name, 'google_form_url' => $formUrl], $errors];
    }

    public static function isRegistrationUrl(string $url): bool
    {
        if (mb_strlen($url) > 500 || filter_var($url, FILTER_VALIDATE_URL) === false) {
            return false;
        }
        try {
            $parts = parse_url($url);
        } catch (\ValueError) {
            return false;
        }
        if (!is_array($parts)) {
            return false;
        }
        $scheme = strtolower((string) ($parts['scheme'] ?? ''));
        $port = $parts['port'] ?? null;
        if (!in_array($scheme, ['http', 'https'], true)
            || trim((string) ($parts['host'] ?? '')) === ''
            || isset($parts['user'])
            || isset($parts['pass'])
            || ($port !== null && ((int) $port < 1 || (int) $port > 65535))) {
            return false;
        }

        return true;
    }
}
