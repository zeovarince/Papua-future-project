<?php

declare(strict_types=1);

namespace App\Validators;

use App\Models\Batch;
use App\Models\ApplicationFormSetting;
use App\Models\ApplicationQuestion;
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
            'full_name' => self::text($input, 'full_name'),
            'birth_date' => self::text($input, 'birth_date'),
            'email' => mb_strtolower(self::text($input, 'email')),
            'whatsapp' => self::text($input, 'whatsapp'),
            'domicile' => self::text($input, 'domicile'),
            'occupation_status' => self::text($input, 'occupation_status'),
            'university' => self::text($input, 'university'),
            'duration' => self::text($input, 'duration'),
            'interest_areas' => self::interests($input['interest_areas'] ?? null),
            'referral_source' => self::text($input, 'referral_source'),
            'motivation' => self::text($input, 'motivation'),
            'special_skills' => self::text($input, 'special_skills'),
            'custom_answers' => [],
        ];
        $errors = [];
        $applicationType = null;

        $batchId = filter_var($values['batch_id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($batchId === false) {
            $errors['batch_id'] = 'Pilih batch yang pendaftarannya masih dibuka.';
        } else {
            $batch = Batch::find((int) $batchId);
            if ($batch === null) {
                $errors['batch_id'] = 'Batch ini tidak ditemukan atau pendaftarannya sudah ditutup. Pilih batch yang masih dibuka.';
            } else {
                $applicationType = (string) $batch['batch_type'];
                if ((int) $batch['is_open'] !== 1) {
                    $errors['batch_id'] = 'Batch ini tidak ditemukan atau pendaftarannya sudah ditutup. Pilih batch yang masih dibuka.';
                } elseif (!ApplicationFormSetting::isActive($applicationType)) {
                    $errors['batch_id'] = 'Pendaftaran jenis ini sedang ditutup. Pilih jalur yang masih dibuka.';
                } else {
                    $values['batch_id'] = (int) $batchId;
                }
            }
        }

        if ($applicationType !== null) {
            $submittedAnswers = is_array($input['custom_answers'] ?? null) ? $input['custom_answers'] : [];
            foreach (ApplicationQuestion::activeByType($applicationType) as $question) {
                $questionId = (int) $question['id'];
                $rawAnswer = $submittedAnswers[$questionId] ?? '';
                $answer = is_scalar($rawAnswer) ? trim((string) $rawAnswer) : '';
                $values['custom_answers'][$questionId] = $answer;
                $maxLength = $question['field_type'] === 'TEXT' ? 200 : 2000;
                if ((int) $question['is_required'] === 1 && $answer === '') {
                    $errors['custom_answers_' . $questionId] = 'Jawaban ini wajib diisi.';
                } elseif (mb_strlen($answer) > $maxLength) {
                    $errors['custom_answers_' . $questionId] = 'Jawaban maksimal ' . number_format($maxLength, 0, ',', '.') . ' karakter.';
                }
            }
        }

        if ($values['full_name'] === '' || mb_strlen($values['full_name']) > 100) {
            $errors['full_name'] = 'Nama wajib diisi, maksimal 100 karakter.';
        }

        $birthDate = self::birthDate($values['birth_date']);
        if ($birthDate === null) {
            $errors['birth_date'] = 'Masukkan tanggal lahir yang valid.';
        } elseif ($birthDate > new DateTimeImmutable('today')) {
            $errors['birth_date'] = 'Tanggal lahir tidak boleh di masa depan.';
        } elseif ($birthDate->diff(new DateTimeImmutable('today'))->y < 17) {
            $errors['birth_date'] = 'Pendaftar harus berusia minimal 17 tahun.';
        }

        if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($values['email']) > 190) {
            $errors['email'] = 'Masukkan alamat email yang valid.';
        }
        if ($values['whatsapp'] === '' || mb_strlen($values['whatsapp']) > 20 || !preg_match('/^(?:\+62|62|0)8[0-9 .()\-]{7,16}$/', $values['whatsapp'])) {
            $errors['whatsapp'] = 'Masukkan nomor WhatsApp Indonesia yang diawali 08 atau +62.';
        }
        if ($values['domicile'] === '' || mb_strlen($values['domicile']) > 120) {
            $errors['domicile'] = 'Domisili wajib diisi, maksimal 120 karakter.';
        }
        if (!array_key_exists($values['occupation_status'], Labels::occupations())) {
            $errors['occupation_status'] = 'Pilih status pekerjaan atau pendidikan.';
        }
        if (in_array($values['occupation_status'], ['STUDENT', 'FRESH_GRADUATE'], true)
            && ($values['university'] === '' || mb_strlen($values['university']) > 120)) {
            $errors['university'] = 'Asal universitas atau instansi wajib diisi, maksimal 120 karakter.';
        } elseif (mb_strlen($values['university']) > 120) {
            $errors['university'] = 'Asal universitas atau instansi maksimal 120 karakter.';
        }
        if (!array_key_exists($values['duration'], Labels::durations())) {
            $errors['duration'] = 'Pilih durasi kontribusi.';
        }
        if ($values['interest_areas'] === []) {
            $errors['interest_areas'] = 'Pilih minimal satu bidang kontribusi.';
        }
        if (!array_key_exists($values['referral_source'], Labels::referralSources())) {
            $errors['referral_source'] = 'Pilih sumber informasi tentang PFP.';
        }
        if (mb_strlen($values['motivation']) < 50 || mb_strlen($values['motivation']) > 1000) {
            $errors['motivation'] = 'Motivasi wajib diisi, 50 sampai 1.000 karakter.';
        }
        if (mb_strlen($values['special_skills']) > 500) {
            $errors['special_skills'] = 'Keahlian khusus maksimal 500 karakter.';
        }

        $photoError = self::validatePhoto($photo);
        if ($photoError !== null) {
            $errors['photo'] = $photoError;
        }
        $cvError = self::validateCv($cv);
        if ($cvError !== null) {
            $errors['cv'] = $cvError;
        }

        $values['university'] = $values['university'] === '' ? null : $values['university'];
        $values['special_skills'] = $values['special_skills'] === '' ? null : $values['special_skills'];
        return [$values, $errors];
    }

    private static function text(array $input, string $key): string
    {
        $value = $input[$key] ?? '';
        return is_scalar($value) ? trim((string) $value) : '';
    }

    private static function interests(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }
        $allowed = Labels::interests();
        $selected = [];
        foreach ($value as $interest) {
            if (is_string($interest) && array_key_exists($interest, $allowed)) {
                $selected[] = $interest;
            }
        }
        return array_values(array_unique($selected));
    }

    private static function birthDate(string $value): ?DateTimeImmutable
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

    private static function validatePhoto(?array $file): ?string
    {
        if ($file === null || (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK
            || empty($file['tmp_name']) || !is_uploaded_file((string) $file['tmp_name'])) {
            return 'Unggah foto pendaftar.';
        }
        if ((int) ($file['size'] ?? 0) < 1 || (int) $file['size'] > self::MAX_PHOTO_BYTES) {
            return 'Ukuran foto maksimal 2 MB.';
        }
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file((string) $file['tmp_name']);
        $image = @getimagesize((string) $file['tmp_name']);
        if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)
            || !$image || ($image['mime'] ?? '') !== $mime) {
            return 'Foto harus berupa JPG, PNG, atau WebP yang valid.';
        }
        if ((int) ($image[0] ?? 0) > 8000 || (int) ($image[1] ?? 0) > 8000
            || (int) ($image[0] ?? 0) * (int) ($image[1] ?? 0) > 16_000_000) {
            return 'Resolusi foto terlalu besar. Unggah foto maksimal 16 megapiksel.';
        }
        return null;
    }

    private static function validateCv(?array $file): ?string
    {
        if ($file === null || (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK
            || empty($file['tmp_name']) || !is_uploaded_file((string) $file['tmp_name'])) {
            return 'Unggah CV dalam format PDF.';
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
