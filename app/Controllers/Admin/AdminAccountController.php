<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Request;
use App\Core\Response;
use App\Core\SecurityLogger;
use App\Core\Session;
use App\Core\View;
use App\Models\AdminUser;
use App\Validators\AdminAccountValidator;
use PDOException;

final class AdminAccountController
{
    public function index(): Response
    {
        $old = Flash::get('old', []);
        return View::render('admin/accounts/index', [
            'title' => 'Pengelola Admin',
            'subtitle' => 'Buat akun baru untuk tim yang mengelola situs.',
            'page' => 'admin-accounts',
            'breadcrumb' => ['Admin', 'Pengaturan', 'Pengelola Admin'],
            'admins' => AdminUser::all(),
            'old' => is_array($old) ? $old : [],
            'errors' => Flash::get('errors', []),
        ], 'admin');
    }

    public function create(Request $request): Response
    {
        [$values, $errors, $password] = AdminAccountValidator::newAdmin($request->post());
        if ($errors !== []) {
            Flash::set('old', $values);
            Flash::set('errors', $errors);
            Flash::set('error', 'Periksa kembali data akun admin.');
            return Response::redirect('/admin/akun');
        }

        try {
            $id = AdminUser::create($values['name'], $values['email'], password_hash($password, PASSWORD_DEFAULT));
        } catch (PDOException $exception) {
            if ((int) ($exception->errorInfo[1] ?? 0) === 1062 || (string) $exception->getCode() === '23000') {
                Flash::set('old', ['name' => $values['name'], 'email' => $values['email']]);
                Flash::set('errors', ['email' => 'Email tersebut sudah terdaftar sebagai akun admin.']);
                Flash::set('error', 'Akun admin tidak dapat dibuat.');
                return Response::redirect('/admin/akun');
            }
            throw $exception;
        }

        SecurityLogger::record('admin_user_created', $request, $id);
        Flash::set('success', 'Akun admin baru berhasil dibuat. Berikan email dan kata sandi awal kepada pemilik akun dengan aman.');
        return Response::redirect('/admin/akun');
    }

    public function passwordForm(): Response
    {
        return View::render('admin/accounts/password', [
            'title' => 'Ubah Kata Sandi',
            'subtitle' => 'Perbarui kata sandi akun admin yang sedang digunakan.',
            'page' => 'admin-password',
            'breadcrumb' => ['Admin', 'Pengaturan', 'Ubah Kata Sandi'],
            'errors' => Flash::get('errors', []),
        ], 'admin');
    }

    public function updatePassword(Request $request): Response
    {
        $admin = Session::get('admin', []);
        $adminId = (int) (is_array($admin) ? ($admin['id'] ?? 0) : 0);
        $record = $adminId > 0 ? AdminUser::findById($adminId) : null;
        if ($record === null) {
            Session::destroy();
            return Response::redirect('/admin/login');
        }

        [$values, $errors] = AdminAccountValidator::passwordChange($request->post());
        if ($errors === [] && !password_verify($values['current_password'], (string) $record['password_hash'])) {
            $errors['current_password'] = 'Kata sandi saat ini tidak sesuai.';
        }
        if ($errors === [] && password_verify($values['new_password'], (string) $record['password_hash'])) {
            $errors['new_password'] = 'Gunakan kata sandi yang berbeda dari kata sandi saat ini.';
        }
        if ($errors !== []) {
            Flash::set('errors', $errors);
            Flash::set('error', 'Kata sandi belum diubah. Periksa kembali isianmu.');
            return Response::redirect('/admin/akun/password');
        }

        AdminUser::updatePasswordHash($adminId, password_hash($values['new_password'], PASSWORD_DEFAULT));
        Session::regenerate();
        Csrf::rotate();
        SecurityLogger::record('admin_password_changed', $request, $adminId);
        Flash::set('success', 'Kata sandi akunmu berhasil diubah.');
        return Response::redirect('/admin/akun/password');
    }
}
