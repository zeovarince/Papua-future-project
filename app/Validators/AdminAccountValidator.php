<?php

declare(strict_types=1);

namespace App\Validators;

final class AdminAccountValidator
{
    /** @return array{0: array{name: string, email: string}, 1: array<string, string>, 2: string} */
    public static function newAdmin(array $input): array
    {
        $name = self::text($input['name'] ?? '');
        $email = mb_strtolower(self::text($input['email'] ?? ''));
        $password = self::text($input['password'] ?? '', false);
        $confirmation = self::text($input['password_confirmation'] ?? '', false);
        $values = ['name' => $name, 'email' => $email];
        $errors = [];

        if ($name === '' || mb_strlen($name) > 100) {
            $errors['name'] = 'Nama admin wajib diisi, maksimal 100 karakter.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 190) {
            $errors['email'] = 'Masukkan email yang valid, maksimal 190 karakter.';
        }
        self::validatePassword($password, 'password', $errors);
        if ($confirmation !== $password) {
            $errors['password_confirmation'] = 'Konfirmasi kata sandi tidak sama.';
        }

        return [$values, $errors, $password];
    }

    /** @return array{0: array{current_password: string, new_password: string}, 1: array<string, string>} */
    public static function passwordChange(array $input): array
    {
        $values = [
            'current_password' => self::text($input['current_password'] ?? '', false),
            'new_password' => self::text($input['new_password'] ?? '', false),
        ];
        $confirmation = self::text($input['password_confirmation'] ?? '', false);
        $errors = [];
        if ($values['current_password'] === '' || strlen($values['current_password']) > 4096) {
            $errors['current_password'] = 'Masukkan kata sandi saat ini.';
        }
        self::validatePassword($values['new_password'], 'new_password', $errors);
        if ($confirmation !== $values['new_password']) {
            $errors['password_confirmation'] = 'Konfirmasi kata sandi tidak sama.';
        }

        return [$values, $errors];
    }

    /** @param array<string, string> $errors */
    private static function validatePassword(string $password, string $key, array &$errors): void
    {
        if (mb_strlen($password) < 12) {
            $errors[$key] = 'Kata sandi minimal 12 karakter.';
        } elseif (strlen($password) > 4096) {
            $errors[$key] = 'Kata sandi terlalu panjang.';
        }
    }

    private static function text(mixed $value, bool $trim = true): string
    {
        if (!is_scalar($value)) {
            return '';
        }
        $text = (string) $value;
        return $trim ? trim($text) : $text;
    }
}
