<?php

declare(strict_types=1);

namespace App\Validators;

use App\Models\ApplicationQuestion;
use App\Models\Batch;
use App\Support\Labels;
use DateTimeImmutable;

final class JoinApplicationValidator
{
    private const MAX_PHOTO_BYTES = 2_097_152;
    private const MAX_CV_BYTES = 5_242_880;

    public static function validate(array $input, ?array $photo, ?array $cv): array
    {
        $values = [
            'batch_id' => self::text($input, 'batch_id'),
            'full_name' => null,
            'birth_date' => null,
            'email' => null,
            'whatsapp' => null,
            'domicile' => null,
            'occupation_status' => null,
            'university' => null,
            'duration' => null,
            'interest_areas' => null,
            'referral_source' => null,
            'motivation' => null,
            'special_skills' => null,
            'portfolio_url' => null,
            // Preserve submitted answers if batch validation returns early.
            'question_answers' => is_array($input['question_answers'] ?? null) ? $input['question_answers'] : [],
            'custom_answers' => is_array($input['custom_answers'] ?? null) ? $input['custom_answers'] : [],
            'photo_enabled' => false,
            'cv_enabled' => false,
        ];
        $errors = [];
        $batchId = filter_var($values['batch_id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($batchId === false) {
            $errors['batch_id'] = 'Pilih batch yang pendaftarannya masih dibuka.';
            return [$values, $errors];
        }

        $batch = Batch::find((int) $batchId);
        if ($batch === null || (int) $batch['is_open'] !== 1) {
            $errors['batch_id'] = 'Batch ini tidak ditemukan atau pendaftarannya sudah ditutup. Pilih batch yang masih dibuka.';
            return [$values, $errors];
        }
        $applicationType = (string) $batch['batch_type'];
        $values['batch_id'] = (int) $batchId;
        $systemInput = is_array($input['question_answers'] ?? null) ? $input['question_answers'] : [];
        $customInput = is_array($input['custom_answers'] ?? null) ? $input['custom_answers'] : [];
        // On a valid batch, retain only values for questions that are active now.
        $values['question_answers'] = [];
        $values['custom_answers'] = [];
        $questions = ApplicationQuestion::activeByType($applicationType);
        $activeUploadFields = [];

        foreach ($questions as $question) {
            $questionId = (int) $question['id'];
            $fieldKey = (string) ($question['field_key'] ?? '');
            $fieldType = (string) $question['field_type'];
            $isRequired = (int) $question['is_required'] === 1;

            if (in_array($fieldKey, ['photo', 'cv'], true)) {
                $activeUploadFields[$fieldKey] = ['required' => $isRequired, 'type' => $fieldType];
                continue;
            }

            $rawAnswer = $fieldKey !== '' ? ($systemInput[$questionId] ?? '') : ($customInput[$questionId] ?? '');
            $options = self::options($question['options'] ?? null);
            $answerErrorKey = $fieldKey !== '' ? 'question_answers_' . $questionId : 'custom_answers_' . $questionId;
            $answer = self::normalizeAnswer($rawAnswer, $fieldType, $options, $answerErrorKey, $errors, $isRequired);
            if ($answer === null) {
                continue;
            }

            if ($fieldKey !== '') {
                $values['question_answers'][$questionId] = $answer['stored'];
                if ($answer['display'] !== '') {
                    self::assignSystemField($values, $fieldKey, $answer['display'], $answer['items']);
                }
            } else {
                $values['custom_answers'][$questionId] = $answer['stored'];
            }
        }

        foreach (['photo', 'cv'] as $uploadKey) {
            $config = $activeUploadFields[$uploadKey] ?? null;
            $values[$uploadKey . '_enabled'] = $config !== null;
            if ($config === null) {
                continue;
            }
            $file = $uploadKey === 'photo' ? $photo : $cv;
            $uploadError = $uploadKey === 'photo'
                ? self::validatePhoto($file, $config['required'])
                : self::validateCv($file, $config['required']);
            if ($uploadError !== null) {
                $errors[$uploadKey] = $uploadError;
            }
        }

        self::validateSystemValues($values, $errors, $questions);
        return [$values, $errors];
    }

    private static function normalizeAnswer(
        mixed $raw,
        string $fieldType,
        array $options,
        string $errorKey,
        array &$errors,
        bool $required
    ): ?array {
        $items = [];
        if (in_array($fieldType, ['CHECKBOX'], true)) {
            $items = is_array($raw) ? array_values(array_unique(array_filter(array_map(
                static fn (mixed $item): string => is_scalar($item) ? trim((string) $item) : '',
                $raw
            ), static fn (string $item): bool => $item !== ''))) : [];
            if ($options !== [] && array_diff($items, $options) !== []) {
                $errors[$errorKey] = 'Pilih jawaban dari opsi yang tersedia.';
            }
            $display = implode(', ', $items);
            $stored = $items;
        } else {
            $display = is_scalar($raw) ? trim((string) $raw) : '';
            $stored = $display;
            if (in_array($fieldType, ['RADIO', 'SELECT'], true) && $display !== '' && $options !== [] && !in_array($display, $options, true)) {
                $errors[$errorKey] = 'Pilih jawaban dari opsi yang tersedia.';
            }
        }

        if ($required && $display === '') {
            $errors[$errorKey] = 'Pertanyaan ini wajib diisi.';
        }
        if ($display === '') {
            return ['stored' => $stored, 'display' => '', 'items' => []];
        }

        $maxLength = $fieldType === 'TEXT' ? 200 : 2000;
        if (in_array($fieldType, ['TEXT', 'TEXTAREA', 'EMAIL', 'TEL', 'DATE', 'URL', 'FILE', 'RADIO', 'CHECKBOX', 'SELECT'], true)
            && mb_strlen($display) > $maxLength) {
            $errors[$errorKey] = 'Jawaban terlalu panjang.';
        }
        if ($fieldType === 'EMAIL' && !filter_var($display, FILTER_VALIDATE_EMAIL)) {
            $errors[$errorKey] = 'Masukkan alamat email yang valid.';
        }
        if ($fieldType === 'TEL' && !preg_match('/^\+?[0-9][0-9 .()\-]{6,23}$/', $display)) {
            $errors[$errorKey] = 'Masukkan nomor telepon yang valid.';
        }
        if ($fieldType === 'DATE' && self::validDate($display) === null) {
            $errors[$errorKey] = 'Masukkan tanggal yang valid.';
        }
        if (in_array($fieldType, ['URL', 'FILE'], true)
            && (!filter_var($display, FILTER_VALIDATE_URL) || !preg_match('#^https?://#i', $display))) {
            $errors[$errorKey] = 'Masukkan tautan website yang diawali http:// atau https://.';
        }
        if ($fieldType === 'TEXTAREA' && mb_strlen($display) > 2000) {
            $errors[$errorKey] = 'Jawaban maksimal 2.000 karakter.';
        }

        return ['stored' => $stored, 'display' => $display, 'items' => $items];
    }

    private static function assignSystemField(array &$values, string $fieldKey, string $display, array $items): void
    {
        $values[$fieldKey] = match ($fieldKey) {
            'full_name' => $display,
            'birth_date' => self::validDate($display)?->format('Y-m-d'),
            'email' => mb_strtolower($display),
            'whatsapp' => $display,
            'domicile' => $display,
            'occupation_status' => self::mapOptionToEnum($display, Labels::occupations()),
            'university' => $display,
            'duration' => self::mapOptionToEnum($display, Labels::durations()),
            'interest_areas' => array_map(static function (string $item): string {
                $key = array_search($item, Labels::interests(), true);
                return is_string($key) ? $key : $item;
            }, $items),
            'referral_source' => self::mapOptionToEnum($display, Labels::referralSources()),
            'motivation' => $display,
            'special_skills' => $display,
            'portfolio_url' => $display,
            default => $values[$fieldKey] ?? null,
        };
    }

    private static function validateSystemValues(array &$values, array &$errors, array $questions): void
    {
        $keys = [];
        foreach ($questions as $question) {
            $key = (string) ($question['field_key'] ?? '');
            if ($key !== '') {
                $keys[$key] = $question;
            }
        }

        if (isset($keys['full_name']) && (int) $keys['full_name']['is_required'] === 1) {
            if (!is_string($values['full_name']) || trim($values['full_name']) === '') {
                $errors['question_answers_' . (int) $keys['full_name']['id']] = 'Nama wajib diisi.';
            } elseif (mb_strlen($values['full_name']) > 100) {
                $errors['question_answers_' . (int) $keys['full_name']['id']] = 'Nama maksimal 100 karakter.';
            }
        } elseif (is_string($values['full_name']) && mb_strlen($values['full_name']) > 100) {
            $errors['question_answers_' . (int) ($keys['full_name']['id'] ?? 0)] = 'Nama maksimal 100 karakter.';
        }

        if (isset($keys['email']) && (int) $keys['email']['is_required'] === 1
            && (!is_string($values['email']) || !filter_var($values['email'], FILTER_VALIDATE_EMAIL))) {
            $errors['question_answers_' . (int) $keys['email']['id']] = 'Masukkan alamat email yang valid.';
        }
        if (is_string($values['email']) && $values['email'] !== '' && mb_strlen($values['email']) > 190) {
            $errors['question_answers_' . (int) ($keys['email']['id'] ?? 0)] = 'Email maksimal 190 karakter.';
        }
        if (isset($keys['whatsapp']) && (int) $keys['whatsapp']['is_required'] === 1
            && (!is_string($values['whatsapp']) || !preg_match('/^(?:\+62|62|0)8[0-9 .()\-]{7,16}$/', $values['whatsapp']))) {
            $errors['question_answers_' . (int) $keys['whatsapp']['id']] = 'Masukkan nomor WhatsApp Indonesia yang diawali 08 atau +62.';
        }
        if (is_string($values['whatsapp']) && $values['whatsapp'] !== '' && mb_strlen($values['whatsapp']) > 20) {
            $errors['question_answers_' . (int) ($keys['whatsapp']['id'] ?? 0)] = 'Nomor WhatsApp maksimal 20 karakter.';
        }

        $birthDateKey = (int) ($keys['birth_date']['id'] ?? 0);
        if (is_string($values['birth_date']) && $values['birth_date'] !== '') {
            $birthDate = self::validDate($values['birth_date']);
            if ($birthDate === null || $birthDate > new DateTimeImmutable('today')) {
                $errors['question_answers_' . $birthDateKey] = 'Masukkan tanggal lahir yang valid dan tidak di masa depan.';
            } elseif ($birthDate->diff(new DateTimeImmutable('today'))->y < 17) {
                $errors['question_answers_' . $birthDateKey] = 'Pendaftar harus berusia minimal 17 tahun.';
            }
        }
        if (is_string($values['domicile']) && mb_strlen($values['domicile']) > 120) {
            $errors['question_answers_' . (int) ($keys['domicile']['id'] ?? 0)] = 'Domisili maksimal 120 karakter.';
        }
        if (is_string($values['university']) && mb_strlen($values['university']) > 120) {
            $errors['question_answers_' . (int) ($keys['university']['id'] ?? 0)] = 'Universitas atau instansi maksimal 120 karakter.';
        }
        if (is_string($values['motivation']) && mb_strlen($values['motivation']) > 1000) {
            $errors['question_answers_' . (int) ($keys['motivation']['id'] ?? 0)] = 'Motivasi maksimal 1.000 karakter.';
        }
        if (is_string($values['motivation']) && $values['motivation'] !== ''
            && isset($keys['motivation']) && (int) $keys['motivation']['is_required'] === 1
            && mb_strlen($values['motivation']) < 50) {
            $errors['question_answers_' . (int) $keys['motivation']['id']] = 'Motivasi minimal 50 karakter.';
        }
        if (is_string($values['special_skills']) && mb_strlen($values['special_skills']) > 500) {
            $errors['question_answers_' . (int) ($keys['special_skills']['id'] ?? 0)] = 'Keahlian khusus maksimal 500 karakter.';
        }

        foreach (['full_name', 'birth_date', 'email', 'whatsapp', 'domicile', 'occupation_status', 'university', 'duration', 'referral_source', 'motivation', 'special_skills', 'portfolio_url'] as $key) {
            if (is_string($values[$key] ?? null) && $values[$key] === '') {
                $values[$key] = null;
            }
        }
        if (($values['interest_areas'] ?? null) === []) {
            $values['interest_areas'] = null;
        }
    }

    private static function mapOptionToEnum(string $value, array $labels): ?string
    {
        if (array_key_exists($value, $labels)) {
            return $value;
        }
        $key = array_search($value, $labels, true);
        return is_string($key) ? $key : null;
    }

    /** @return list<string> */
    private static function options(mixed $options): array
    {
        if (!is_string($options) || trim($options) === '') {
            return [];
        }
        return array_values(array_unique(array_filter(array_map('trim', preg_split('/\R/', $options) ?: []), static fn (string $option): bool => $option !== '')));
    }

    private static function validDate(string $value): ?DateTimeImmutable
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return null;
        }
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        $errors = DateTimeImmutable::getLastErrors();
        if ($date === false || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))
            || $date->format('Y-m-d') !== $value) {
            return null;
        }
        return $date;
    }

    private static function text(array $input, string $key): string
    {
        $value = $input[$key] ?? '';
        return is_scalar($value) ? trim((string) $value) : '';
    }

    private static function validatePhoto(?array $file, bool $required): ?string
    {
        return self::validateImageUpload($file, $required);
    }

    private static function validateImageUpload(?array $file, bool $required): ?string
    {
        $error = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($error === UPLOAD_ERR_NO_FILE) {
            return $required ? 'Unggah foto pendaftar.' : null;
        }
        if ($error !== UPLOAD_ERR_OK || empty($file['tmp_name']) || !is_uploaded_file((string) $file['tmp_name'])) {
            return 'Foto gagal diunggah. Pilih kembali berkasnya.';
        }
        if ((int) ($file['size'] ?? 0) < 1 || (int) $file['size'] > self::MAX_PHOTO_BYTES) {
            return 'Ukuran foto maksimal 2 MB.';
        }
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file((string) $file['tmp_name']);
        $image = @getimagesize((string) $file['tmp_name']);
        if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true) || !$image || ($image['mime'] ?? '') !== $mime) {
            return 'Foto harus berupa JPG, PNG, atau WebP yang valid.';
        }
        if ((int) ($image[0] ?? 0) > 8000 || (int) ($image[1] ?? 0) > 8000 || (int) ($image[0] ?? 0) * (int) ($image[1] ?? 0) > 16_000_000) {
            return 'Resolusi foto terlalu besar. Unggah foto maksimal 16 megapiksel.';
        }
        return null;
    }

    private static function validateCv(?array $file, bool $required): ?string
    {
        $error = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($error === UPLOAD_ERR_NO_FILE) {
            return $required ? 'Unggah CV dalam format PDF.' : null;
        }
        if ($error !== UPLOAD_ERR_OK || empty($file['tmp_name']) || !is_uploaded_file((string) $file['tmp_name'])) {
            return 'CV gagal diunggah. Pilih kembali berkasnya.';
        }
        if ((int) ($file['size'] ?? 0) < 1 || (int) $file['size'] > self::MAX_CV_BYTES) {
            return 'Ukuran CV maksimal 5 MB.';
        }
        $handle = @fopen((string) $file['tmp_name'], 'rb');
        $signature = $handle === false ? false : fread($handle, 5);
        if (is_resource($handle)) {
            fclose($handle);
        }
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file((string) $file['tmp_name']);
        if ($signature !== '%PDF-' || $mime !== 'application/pdf') {
            return 'CV harus berupa file PDF yang valid.';
        }
        return null;
    }
}
