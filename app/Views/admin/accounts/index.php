<?php

declare(strict_types=1);

$old = is_array($old ?? null) ? $old : [];
$errors = is_array($errors ?? null) ? $errors : [];
$currentAdminId = (int) ($admin['id'] ?? 0);
?>
<div class="admin-account-grid">
    <section class="card">
        <div class="card-header">
            <div>
                <h2 class="card-title">Buat akun admin</h2>
                <p class="card-subtitle">Admin baru dapat masuk ke panel dan mengelola konten.</p>
            </div>
        </div>
        <div class="card-body">
            <form action="<?= url('/admin/akun') ?>" method="post" class="admin-account-form">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label class="form-label" for="admin-name">Nama</label>
                    <input class="form-control" id="admin-name" name="name" type="text" maxlength="100" autocomplete="name" value="<?= e($old['name'] ?? '') ?>" required>
                    <?php if (isset($errors['name'])): ?><small class="field-error"><?= e($errors['name']) ?></small><?php endif; ?>
                </div>
                <div class="form-group">
                    <label class="form-label" for="admin-email">Email</label>
                    <input class="form-control" id="admin-email" name="email" type="email" maxlength="190" autocomplete="email" value="<?= e($old['email'] ?? '') ?>" required>
                    <?php if (isset($errors['email'])): ?><small class="field-error"><?= e($errors['email']) ?></small><?php endif; ?>
                </div>
                <div class="form-group">
                    <label class="form-label" for="admin-password">Kata sandi awal</label>
                    <input class="form-control" id="admin-password" name="password" type="password" minlength="12" maxlength="4096" autocomplete="new-password" required>
                    <small class="field-hint">Gunakan minimal 12 karakter. Kata sandi tidak disimpan dalam teks biasa.</small>
                    <?php if (isset($errors['password'])): ?><small class="field-error"><?= e($errors['password']) ?></small><?php endif; ?>
                </div>
                <div class="form-group">
                    <label class="form-label" for="admin-password-confirmation">Ulangi kata sandi awal</label>
                    <input class="form-control" id="admin-password-confirmation" name="password_confirmation" type="password" minlength="12" maxlength="4096" autocomplete="new-password" required>
                    <?php if (isset($errors['password_confirmation'])): ?><small class="field-error"><?= e($errors['password_confirmation']) ?></small><?php endif; ?>
                </div>
                <button class="btn btn-primary" type="submit">
                    <?= icon('user-plus', '', 16) ?>
                    <span>Buat akun admin</span>
                </button>
            </form>
        </div>
    </section>

    <section class="card">
        <div class="card-header">
            <div>
                <h2 class="card-title">Akun admin terdaftar</h2>
                <p class="card-subtitle"><?= count($admins) ?> akun memiliki akses ke seluruh panel admin.</p>
            </div>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr><th>Nama</th><th>Email</th><th>Dibuat</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($admins as $account): ?>
                        <tr>
                            <td>
                                <strong><?= e($account['name']) ?></strong>
                                <?php if ((int) $account['id'] === $currentAdminId): ?><small class="admin-account-current">Akunmu</small><?php endif; ?>
                            </td>
                            <td><?= e($account['email']) ?></td>
                            <td><?= e(date('d M Y', strtotime((string) $account['created_at']))) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>
