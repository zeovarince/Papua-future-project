<?php

declare(strict_types=1);

$isEdit = ($batch !== null);
$title = $isEdit ? 'Ubah Batch' : 'Tambah Batch';
$breadcrumb = ['Admin', 'Tim', 'Batch', $isEdit ? 'Ubah' : 'Tambah'];
$hasStickyActionBar = true;
$isLocked = ($isEdit && (int) ($batch['member_count'] ?? 0) > 0);
?>

<div class="form-container-narrow">
    <div style="margin-bottom: 20px;">
        <p style="color: var(--admin-text-muted); font-size: 13px; margin: 0;">
            Atur nama batch dan tautan pendaftarannya. Status buka/tutup dapat diubah langsung dari daftar Batch.
        </p>
    </div>

    <form action="<?= url($isEdit ? '/admin/batch/' . (int) $batch['id'] : '/admin/batch') ?>" method="post">
        <?= csrf_field() ?>

        <div class="card">
            <div class="card-header">
                <div>
                    <h2 class="card-title"><?= $isEdit ? 'Ubah Informasi Batch' : 'Buat Batch Baru' ?></h2>
                    <p class="card-subtitle">Tentukan jenis, nama, dan link formulir untuk batch ini.</p>
                </div>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label" for="batchTypeField">Jenis Batch *</label>
                    <select id="batchTypeField" name="batch_type" class="form-control" <?= $isLocked ? 'disabled' : '' ?>>
                        <option value="INTERN" <?= ($values['batch_type'] ?? '') === 'INTERN' ? 'selected' : '' ?>>Internship (Magang)</option>
                        <option value="VOLUNTEER" <?= ($values['batch_type'] ?? '') === 'VOLUNTEER' ? 'selected' : '' ?>>Volunteer (Relawan)</option>
                    </select>
                    <?php if ($isLocked): ?>
                        <input type="hidden" name="batch_type" value="<?= e($batch['batch_type']) ?>">
                        <small class="field-hint">Jenis batch dikunci karena sudah digunakan anggota tim.</small>
                    <?php endif; ?>
                    <?php if (isset($errors['batch_type'])): ?>
                        <small class="field-error"><?= e($errors['batch_type']) ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label class="form-label" for="batchNameField">Nama Batch *</label>
                    <input type="text" id="batchNameField" name="name" maxlength="60" required value="<?= e($values['name'] ?? '') ?>" class="form-control" placeholder="Contoh: Batch 1 2026">
                    <?php if (isset($errors['name'])): ?>
                        <small class="field-error"><?= e($errors['name']) ?></small>
                    <?php endif; ?>
                    <small class="field-hint">Nama batch ini akan menjadi judul kelompok anggota di halaman Tentang Kami.</small>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="batchRegistrationUrlField">Link Pendaftaran</label>
                    <input type="url" id="batchRegistrationUrlField" name="google_form_url" maxlength="500" value="<?= e($values['google_form_url'] ?? '') ?>" class="form-control" placeholder="https://...">
                    <?php if (isset($errors['google_form_url'])): ?>
                        <small class="field-error"><?= e($errors['google_form_url']) ?></small>
                    <?php endif; ?>
                    <small class="field-hint">Masukkan link dari layanan formulir apa pun. Gunakan alamat http:// atau https://. Tombol buka pendaftaran aktif setelah link disimpan.</small>
                </div>
            </div>
        </div>

        <!-- Sticky Action Bar -->
        <div class="sticky-action-bar">
            <a href="<?= url('/admin/batch') ?>" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">
                <?= icon('check', '', 16) ?>
                <span><?= $isEdit ? 'Simpan Perubahan' : 'Simpan Batch' ?></span>
            </button>
        </div>
    </form>
</div>
