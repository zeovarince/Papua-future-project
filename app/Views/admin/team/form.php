<?php

declare(strict_types=1);

$isCore = ($group === 'penggerak');
$action = ($member === null ? url('/admin/tim/' . $group) : url('/admin/tim/' . $group . '/' . (int) $member['id']));
$groupTitles = [
    'penggerak' => 'Core Team',
    'internship' => 'Internship',
    'volunteer' => 'Volunteer',
];
$groupTitle = $groupTitles[$group] ?? 'Tim';
$title = ($member === null ? 'Tambah ' . $groupTitle : 'Ubah ' . $groupTitle);
$breadcrumb = ['Admin', 'Tim', $groupTitle, $member === null ? 'Tambah' : 'Ubah'];
$hasStickyActionBar = true;
$photoPath = $values['photo_path'] ?? null;
$homePhotoPath = $values['home_photo_path'] ?? null;
$photoUrl = static fn (?string $path): string => $path === null || $path === ''
    ? ''
    : (str_starts_with($path, 'img/') ? asset($path) : url('/uploads/' . $path));
$isFounder = $isCore && !empty($values['is_founder']);
?>

<form action="<?= e($action) ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="grid-12">
        <!-- Main Column (8 of 12 cols) -->
        <div class="col-8">
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Profil Anggota</h2>
                        <p class="card-subtitle">Nama lengkap, peran, dan deskripsi singkat.</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label" for="memName">Nama Lengkap *</label>
                        <input type="text" id="memName" name="name" maxlength="100" required value="<?= e($values['name'] ?? '') ?>" class="form-control" placeholder="Nama lengkap anggota">
                        <?php if (isset($errors['name'])): ?>
                            <small class="field-error"><?= e($errors['name']) ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="memRole"><?= $isCore ? 'Jabatan *' : 'Peran / Bidang *' ?></label>
                        <input type="text" id="memRole" name="role" maxlength="120" required value="<?= e($values['role'] ?? '') ?>" class="form-control" placeholder="<?= $isCore ? 'Founder / Koordinator Program' : 'Fasilitator Literasi / Tim Medis' ?>">
                        <?php if (isset($errors['role'])): ?>
                            <small class="field-error"><?= e($errors['role']) ?></small>
                        <?php endif; ?>
                    </div>

                    <?php if (!$isCore): ?>
                        <div class="grid-12" style="gap: 12px; margin-bottom: 16px;">
                            <div class="col-8" style="grid-column: span 6 / span 6;">
                                <label class="form-label" for="memBatch">Batch *</label>
                                <select id="memBatch" name="batch_id" required class="form-control">
                                    <option value="">Pilih batch</option>
                                    <?php foreach ($batches as $batch): ?>
                                        <option value="<?= (int) $batch['id'] ?>" <?= (string) ($values['batch_id'] ?? '') === (string) $batch['id'] ? 'selected' : '' ?>>
                                            <?= e($batch['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['batch_id'])): ?>
                                    <small class="field-error"><?= e($errors['batch_id']) ?></small>
                                <?php endif; ?>
                            </div>
                            <div class="col-4" style="grid-column: span 6 / span 6;">
                                <label class="form-label" for="memUniv">Asal Universitas / Instansi</label>
                                <input type="text" id="memUniv" name="university" maxlength="120" value="<?= e($values['university'] ?? '') ?>" class="form-control" placeholder="Universitas Papua">
                                <?php if (isset($errors['university'])): ?>
                                    <small class="field-error"><?= e($errors['university']) ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label class="form-label" for="memBio">Deskripsi Singkat</label>
                        <textarea id="memBio" name="bio" rows="3" maxlength="400" class="form-control" placeholder="Kutipan singkat atau latar belakang..."><?= e($values['bio'] ?? '') ?></textarea>
                        <?php if (isset($errors['bio'])): ?>
                            <small class="field-error"><?= e($errors['bio']) ?></small>
                        <?php endif; ?>
                    </div>

                    <?php if ($isCore): ?>
                <div class="form-group" data-founder-bio-field <?= $isFounder ? '' : 'hidden' ?> style="margin-bottom: 0;">
                            <label class="form-label" for="memLongBio">Profil Founder (Opsional, untuk Beranda)</label>
                            <textarea id="memLongBio" name="long_bio" rows="6" class="form-control" placeholder="Cerita lengkap perjalanan founder..." <?= $isFounder ? '' : 'disabled' ?>><?= e($values['long_bio'] ?? '') ?></textarea>
                            <?php if (isset($errors['long_bio'])): ?>
                                <small class="field-error"><?= e($errors['long_bio']) ?></small>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Side Column (4 of 12 cols) -->
        <div class="col-4">
            <!-- Foto Profil -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Foto Profil</h2>
                        <p class="card-subtitle">Hapus latar foto sendiri sebelum mengunggah (PNG/WebP, maks 2 MB).</p>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($photoPath)): ?>
                        <div style="display: flex; justify-content: center; margin-bottom: 16px;">
                                <img src="<?= e($photoUrl($photoPath)) ?>" alt="Foto profil saat ini" class="user-avatar-circle" style="width: 96px; height: 96px; border: 2px solid var(--admin-border);">
                        </div>
                    <?php endif; ?>

                    <div class="form-group" style="margin-bottom: 0;">
                        <input type="file" name="photo" accept="image/png,image/webp" class="form-control" style="padding-top: 8px;">
                        <small class="field-hint">Unggah foto yang sudah dihapus latarnya dan disimpan sebagai PNG/WebP transparan.</small>
                        <?php if (isset($errors['photo'])): ?>
                            <small class="field-error"><?= e($errors['photo']) ?></small>
                        <?php endif; ?>
                        <?php if (!empty($photoPath)): ?>
                            <small class="field-hint">Kosongkan jika tetap menggunakan foto yang ada.</small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if ($isCore): ?>
                <div class="card" data-founder-photo-field <?= $isFounder ? '' : 'hidden' ?>>
                    <div class="card-header">
                        <div>
                            <h2 class="card-title">Foto Founder di Beranda</h2>
                            <p class="card-subtitle">Foto ini terpisah dari foto profil Core Team.</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php $homePreviewPath = $homePhotoPath ?: $photoPath; ?>
                        <?php if (!empty($homePreviewPath)): ?>
                            <div style="display: flex; justify-content: center; margin-bottom: 16px;">
                                <img src="<?= e($photoUrl($homePreviewPath)) ?>" alt="Foto yang saat ini tampil di Beranda" style="width: 120px; height: 150px; object-fit: contain; border: 1px solid var(--admin-border); background: var(--admin-surface-muted);">
                            </div>
                        <?php endif; ?>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" for="homeFounderPhoto">Foto khusus Beranda</label>
                            <input type="file" id="homeFounderPhoto" name="home_photo" accept="image/png,image/webp" class="form-control" style="padding-top: 8px;" <?= $isFounder ? '' : 'disabled' ?>>
                            <small class="field-hint">
                                <?php if (!empty($homePhotoPath)): ?>Kosongkan jika foto khusus Beranda tetap digunakan.<?php else: ?>Jika belum diunggah, Beranda memakai foto profil Core Team.<?php endif; ?>
                                Hapus latar secara manual sebelum mengunggah PNG/WebP transparan.
                            </small>
                            <?php if (isset($errors['home_photo'])): ?>
                                <small class="field-error"><?= e($errors['home_photo']) ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Pengaturan & Urutan -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Pengaturan</h2>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label" for="memSort">Urutan Tampil *</label>
                        <input type="number" id="memSort" name="sort_order" min="0" max="100000" required value="<?= e($values['sort_order'] ?? 0) ?>" class="form-control">
                        <?php if (isset($errors['sort_order'])): ?>
                            <small class="field-error"><?= e($errors['sort_order']) ?></small>
                        <?php endif; ?>
                        <small class="field-hint">Urutan terkecil tampil lebih awal.</small>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 12px; border-top: 1px solid var(--admin-border); padding-top: 14px;">
                        <?php if ($isCore): ?>
                            <label class="checkbox-field">
                                <input type="checkbox" id="memIsFounder" name="is_founder" value="1" data-founder-toggle <?= $isFounder ? 'checked' : '' ?>>
                                <span>Tandai sebagai Founder</span>
                            </label>
                        <?php endif; ?>

                        <label class="checkbox-field">
                            <input type="hidden" name="is_published" value="0">
                            <input type="checkbox" name="is_published" value="1" <?= !isset($values['is_published']) || !empty($values['is_published']) ? 'checked' : '' ?>>
                            <span>Tampilkan di Halaman Publik</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sticky Action Bar -->
    <div class="sticky-action-bar">
        <a href="<?= url('/admin/tim/' . $group) ?>" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-primary">
            <?= icon('check', '', 16) ?>
            <span><?= $member === null ? 'Simpan Anggota' : 'Simpan Perubahan' ?></span>
        </button>
    </div>
</form>
