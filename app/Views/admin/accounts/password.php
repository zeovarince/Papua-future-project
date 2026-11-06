<?php

declare(strict_types=1);

$errors = is_array($errors ?? null) ? $errors : [];
?>
<div class="form-container-narrow">
    <section class="card">
        <div class="card-header">
            <div>
                <h2 class="card-title">Perbarui kata sandi</h2>
                <p class="card-subtitle">Konfirmasi kata sandi saat ini sebelum menyimpan yang baru.</p>
            </div>
        </div>
        <div class="card-body">
            <form action="<?= url('/admin/akun/password') ?>" method="post" class="admin-account-form">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label class="form-label" for="current-password">Kata sandi saat ini</label>
                    <input class="form-control" id="current-password" name="current_password" type="password" maxlength="4096" autocomplete="current-password" required>
                    <?php if (isset($errors['current_password'])): ?><small class="field-error"><?= e($errors['current_password']) ?></small><?php endif; ?>
                </div>
                <div class="form-group">
                    <label class="form-label" for="new-password">Kata sandi baru</label>
                    <input class="form-control" id="new-password" name="new_password" type="password" minlength="12" maxlength="4096" autocomplete="new-password" required>
                    <small class="field-hint">Minimal 12 karakter dan berbeda dari kata sandi saat ini.</small>
                    <?php if (isset($errors['new_password'])): ?><small class="field-error"><?= e($errors['new_password']) ?></small><?php endif; ?>
                </div>
                <div class="form-group">
                    <label class="form-label" for="password-confirmation">Ulangi kata sandi baru</label>
                    <input class="form-control" id="password-confirmation" name="password_confirmation" type="password" minlength="12" maxlength="4096" autocomplete="new-password" required>
                    <?php if (isset($errors['password_confirmation'])): ?><small class="field-error"><?= e($errors['password_confirmation']) ?></small><?php endif; ?>
                </div>
                <div class="form-actions">
                    <a class="btn btn-secondary" href="<?= url('/admin') ?>">Batal</a>
                    <button class="btn btn-primary" type="submit"><?= icon('check', '', 16) ?><span>Simpan kata sandi</span></button>
                </div>
            </form>
        </div>
    </section>
</div>
