<?php $isCore = $group === 'penggerak'; $action = $member === null ? url('/admin/tim/' . $group) : url('/admin/tim/' . $group . '/' . (int) $member['id']); ?>
<form action="<?= $action ?>" method="post" enctype="multipart/form-data" class="admin-form card-surface">
    <?= csrf_field() ?>
    <label class="form-field">
        <span>Nama lengkap</span>
        <input name="name" maxlength="100" required value="<?= e($values['name'] ?? '') ?>">
        <?php if (isset($errors['name'])): ?><small class="field-error"><?= e($errors['name']) ?></small><?php endif; ?>
    </label>
    <label class="form-field">
        <span><?= $isCore ? 'Jabatan' : 'Peran / bidang' ?></span>
        <input name="role" maxlength="120" required value="<?= e($values['role'] ?? '') ?>">
        <?php if (isset($errors['role'])): ?><small class="field-error"><?= e($errors['role']) ?></small><?php endif; ?>
    </label>
    <?php if (!$isCore): ?>
        <label class="form-field">
            <span>Batch</span>
            <select name="batch_id" required>
                <option value="">Pilih batch</option>
                <?php foreach ($batches as $batch): ?>
                    <option value="<?= (int) $batch['id'] ?>" <?= (string) ($values['batch_id'] ?? '') === (string) $batch['id'] ? 'selected' : '' ?>><?= e($batch['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['batch_id'])): ?><small class="field-error"><?= e($errors['batch_id']) ?></small><?php endif; ?>
        </label>
        <label class="form-field">
            <span>Asal universitas</span>
            <input name="university" maxlength="120" value="<?= e($values['university'] ?? '') ?>">
            <?php if (isset($errors['university'])): ?><small class="field-error"><?= e($errors['university']) ?></small><?php endif; ?>
        </label>
    <?php endif; ?>
    <label class="form-field form-field-wide">
        <span>Deskripsi singkat</span>
        <textarea name="bio" rows="3" maxlength="400"><?= e($values['bio'] ?? '') ?></textarea>
        <?php if (isset($errors['bio'])): ?><small class="field-error"><?= e($errors['bio']) ?></small><?php endif; ?>
    </label>
    <?php if ($isCore): ?>
        <label class="form-field form-field-wide">
            <span>Profil panjang (opsional)</span>
            <textarea name="long_bio" rows="5"><?= e($values['long_bio'] ?? '') ?></textarea>
            <?php if (isset($errors['long_bio'])): ?><small class="field-error"><?= e($errors['long_bio']) ?></small><?php endif; ?>
        </label>
    <?php endif; ?>
    <label class="form-field">
        <span>Foto profil (JPG, PNG, WebP · maksimal 2 MB)</span>
        <input type="file" name="photo" accept="image/jpeg,image/png,image/webp">
        <?php if (isset($errors['photo'])): ?><small class="field-error"><?= e($errors['photo']) ?></small><?php endif; ?>
        <?php if (!empty($values['photo_path'])): ?><small>Foto saat ini: <?= e(basename($values['photo_path'])) ?>. Kosongkan jika ingin tetap memakai foto ini.</small><?php endif; ?>
    </label>
    <label class="form-field">
        <span>Urutan tampil</span>
        <input type="number" name="sort_order" min="0" max="100000" required value="<?= e($values['sort_order'] ?? 0) ?>">
        <?php if (isset($errors['sort_order'])): ?><small class="field-error"><?= e($errors['sort_order']) ?></small><?php endif; ?>
    </label>
    <?php if ($isCore): ?>
        <label class="checkbox-field"><input type="checkbox" name="is_founder" value="1" <?= !empty($values['is_founder']) ? 'checked' : '' ?>> Tandai sebagai Founder (hanya satu profil)</label>
    <?php endif; ?>
    <label class="checkbox-field"><input type="hidden" name="is_published" value="0"><input type="checkbox" name="is_published" value="1" <?= !isset($values['is_published']) || !empty($values['is_published']) ? 'checked' : '' ?>> Tampilkan di halaman publik</label>
    <div class="form-actions form-field-wide">
        <button class="button-primary" type="submit">Simpan anggota</button>
        <a class="button-secondary" href="<?= url('/admin/tim/' . $group) ?>">Batal</a>
    </div>
</form>
