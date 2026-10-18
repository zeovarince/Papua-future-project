<?php

declare(strict_types=1);

namespace App\Validators;

use App\Models\Batch;

final class TeamMemberValidator
{
    public static function validate(array $input, string $group): array
    {
        $validGroups = ['penggerak' => 'CORE', 'internship' => 'INTERN', 'volunteer' => 'VOLUNTEER'];
        $memberGroup = $validGroups[$group] ?? '';
        $name = self::text($input['name'] ?? '');
        $role = self::text($input['role'] ?? '');
        $university = self::text($input['university'] ?? '');
        $bio = self::text($input['bio'] ?? '');
        $longBio = self::text($input['long_bio'] ?? '');
        $batchValue = self::text($input['batch_id'] ?? '');
        $batchId = $batchValue === '' ? null : filter_var($batchValue, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $sortOrder = filter_var($input['sort_order'] ?? '0', FILTER_VALIDATE_INT);
        $errors = [];

        if ($memberGroup === '') {
            $errors['group'] = 'Kelompok tim tidak valid.';
        }
        if ($name === '' || mb_strlen($name) > 100) {
            $errors['name'] = 'Nama wajib diisi, maksimal 100 karakter.';
        }
        if ($role === '' || mb_strlen($role) > 120) {
            $errors['role'] = 'Jabatan atau bidang wajib diisi, maksimal 120 karakter.';
        }
        if (mb_strlen($university) > 120) {
            $errors['university'] = 'Nama universitas maksimal 120 karakter.';
        }
        if (mb_strlen($bio) > 400) {
            $errors['bio'] = 'Deskripsi singkat maksimal 400 karakter.';
        }
        if (mb_strlen($longBio) > 10000) {
            $errors['long_bio'] = 'Profil panjang maksimal 10.000 karakter.';
        }
        if ($sortOrder === false || $sortOrder < 0 || $sortOrder > 100000) {
            $errors['sort_order'] = 'Urutan harus berupa angka dari 0 sampai 100.000.';
        }

        if ($memberGroup === 'CORE') {
            if ($batchValue !== '') {
                $errors['batch_id'] = 'Tim Penggerak tidak menggunakan batch.';
            }
            $batchId = null;
        } else {
            if ($batchId === false || $batchId === null) {
                $errors['batch_id'] = 'Pilih batch untuk anggota ini.';
                $batchId = null;
            } else {
                $batch = Batch::find((int) $batchId);
                $expectedType = $memberGroup === 'INTERN' ? 'INTERN' : 'VOLUNTEER';
                if ($batch === null || $batch['batch_type'] !== $expectedType) {
                    $errors['batch_id'] = 'Batch tidak ditemukan atau jenisnya tidak cocok dengan kelompok anggota.';
                }
            }
        }

        return [[
            'member_group' => $memberGroup,
            'batch_id' => $batchId === null ? null : (int) $batchId,
            'name' => $name,
            'role' => $role,
            'university' => $university !== '' ? $university : null,
            'bio' => $bio !== '' ? $bio : null,
            'long_bio' => $longBio !== '' ? $longBio : null,
            'is_founder' => $memberGroup === 'CORE' && !empty($input['is_founder']),
            'sort_order' => $sortOrder === false ? 0 : (int) $sortOrder,
            'is_published' => !isset($input['is_published']) || !empty($input['is_published']),
        ], $errors];
    }

    private static function text(mixed $value): string
    {
        return is_scalar($value) ? trim((string) $value) : '';
    }
}
