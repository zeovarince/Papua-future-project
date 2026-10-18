<form action="<?= url($batch === null ? '/admin/batch' : '/admin/batch/' . (int) $batch['id']) ?>" method="post" class="admin-form card-surface">
    <?= csrf_field() ?>
    <label class="form-field">
        <span>Jenis batch</span>
        <select name="batch_type" <?= $batch !== null && ((int) $batch['member_count'] > 0 || (int) $batch['application_count'] > 0) ? 'disabled' : '' ?>>
            <option value="INTERN" <?= ($values['batch_type'] ?? '') === 'INTERN' ? 'selected' : '' ?>>Internship</option>
            <option value="VOLUNTEER" <?= ($values['batch_type'] ?? '') === 'VOLUNTEER' ? 'selected' : '' ?>>Volunteer / Relawan</option>
        </select>
        <?php if ($batch !== null && ((int) $batch['member_count'] > 0 || (int) $batch['application_count'] > 0)): ?>
            <input type="hidden" name="batch_type" value="<?= e($batch['batch_type']) ?>">
            <small>Jenis batch dikunci karena sudah memiliki anggota atau pendaftar.</small>
        <?php endif; ?>
        <?php if (isset($errors['batch_type'])): ?><small class="field-error"><?= e($errors['batch_type']) ?></small><?php endif; ?>
    </label>
    <label class="form-field">
        <span>Nama batch</span>
        <input name="name" maxlength="60" required value="<?= e($values['name'] ?? '') ?>" placeholder="Contoh: Batch 1 2026">
        <?php if (isset($errors['name'])): ?><small class="field-error"><?= e($errors['name']) ?></small><?php endif; ?>
    </label>
    <div class="form-actions form-field-wide"><button class="button-primary" type="submit">Simpan batch</button><a class="button-secondary" href="<?= url('/admin/batch') ?>">Batal</a></div>
</form>
