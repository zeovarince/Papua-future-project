<?php

declare(strict_types=1);

$id = (int) ($program['id'] ?? 0);
$get = static fn(string $key, mixed $default = '') => $values[$key] ?? $default;
$title = $id > 0 ? 'Ubah Program' : 'Tambah Program';
$breadcrumb = ['Admin', 'Konten', 'Program', $id > 0 ? 'Ubah' : 'Tambah'];
$hasStickyActionBar = true;

$decodedRows = json_decode((string) ($get('info_rows', '') ?? ''), true) ?: [];
$rowsText = implode("\n", array_map(static fn($row) => (string) ($row['label'] ?? '') . ' | ' . (string) ($row['value'] ?? ''), $decodedRows));
$coverImg = $get('cover_image');
?>

<form action="<?= url($id > 0 ? '/admin/program/' . $id : '/admin/program') ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <?php if (isset($errors['form'])): ?>
        <div class="toast toast-error" style="position: static; max-width: 100%; margin-bottom: 20px;">
            <div class="toast-body"><?= e($errors['form']) ?></div>
        </div>
    <?php endif; ?>

    <div class="grid-12">
        <!-- Main Column (8 of 12 cols) -->
        <div class="col-8">
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Informasi Program</h2>
                        <p class="card-subtitle">Judul, slug URL, kategori, dan ringkasan.</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label" for="progTitle">Nama Program *</label>
                        <input type="text" id="progTitle" name="title" maxlength="160" required value="<?= e($get('title')) ?>" class="form-control" placeholder="Contoh: Bimbingan Belajar dan Literasi">
                        <?php if (isset($errors['title'])): ?>
                            <small class="field-error"><?= e($errors['title']) ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="grid-12" style="gap: 12px; margin-bottom: 16px;">
                        <div class="col-8" style="grid-column: span 6 / span 6;">
                            <label class="form-label" for="progSlug">Slug URL *</label>
                            <input type="text" id="progSlug" name="slug" maxlength="160" required value="<?= e($get('slug')) ?>" class="form-control" placeholder="bimbel-literasi">
                            <?php if (isset($errors['slug'])): ?>
                                <small class="field-error"><?= e($errors['slug']) ?></small>
                            <?php endif; ?>
                        </div>
                        <div class="col-4" style="grid-column: span 6 / span 6;">
                            <label class="form-label" for="progCategory">Kategori *</label>
                            <input type="text" id="progCategory" name="category_label" maxlength="40" required value="<?= e($get('category_label')) ?>" class="form-control" placeholder="Pendidikan / Kesehatan / Pemuda">
                            <?php if (isset($errors['category_label'])): ?>
                                <small class="field-error"><?= e($errors['category_label']) ?></small>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="progTagline">Tagline Detail *</label>
                        <input type="text" id="progTagline" name="tagline" maxlength="220" required value="<?= e($get('tagline')) ?>" class="form-control" placeholder="Pendidikan inklusif untuk masa depan anak Papua">
                        <?php if (isset($errors['tagline'])): ?>
                            <small class="field-error"><?= e($errors['tagline']) ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" for="progSummary">Ringkasan Program *</label>
                        <textarea id="progSummary" name="summary" maxlength="400" rows="3" required class="form-control"><?= e($get('summary')) ?></textarea>
                        <?php if (isset($errors['summary'])): ?>
                            <small class="field-error"><?= e($errors['summary']) ?></small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Konten &amp; Rincian Kegiatan</h2>
                        <p class="card-subtitle">Deskripsi lengkap, mitra kolaborasi, dan informasi pendukung.</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label" for="progBody">Deskripsi Lengkap *</label>
                        <textarea id="progBody" name="body" rows="8" required class="form-control" style="min-height: 180px;"><?= e($get('body')) ?></textarea>
                        <?php if (isset($errors['body'])): ?>
                            <small class="field-error"><?= e($errors['body']) ?></small>
                        <?php endif; ?>
                        <small class="field-hint">Format teks biasa dengan dukungan <code>**tebal**</code>.</small>
                    </div>

                    <div class="grid-12" style="gap: 12px; margin-bottom: 16px;">
                        <div class="col-8" style="grid-column: span 6 / span 6;">
                            <label class="form-label" for="progActivities">Kegiatan dalam Program</label>
                            <textarea id="progActivities" name="activities" rows="4" class="form-control" placeholder="Satu kegiatan per baris"><?= e($get('activities')) ?></textarea>
                            <small class="field-hint">Satu kegiatan per baris.</small>
                        </div>
                        <div class="col-4" style="grid-column: span 6 / span 6;">
                            <label class="form-label" for="progPartners">Mitra Kolaborasi</label>
                            <textarea id="progPartners" name="partners" rows="4" class="form-control" placeholder="UNICEF, Kemenkes RI"><?= e($get('partners')) ?></textarea>
                            <small class="field-hint">Satu mitra per baris.</small>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" for="progInfoRows">Informasi Tambahan (Pasangan Label | Nilai)</label>
                        <textarea id="progInfoRows" name="info_rows" rows="3" class="form-control" placeholder="Target Usia | 6 - 15 Tahun&#10;Lokasi | Pulau Mansinam"><?= e($rowsText) ?></textarea>
                        <small class="field-hint">Satu baris dengan format: Label | Nilai</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Side Column (4 of 12 cols) -->
        <div class="col-4">
            <!-- Publikasi & Status -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Publikasi &amp; Status</h2>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label" for="progSort">Nomor Urut Tampil</label>
                        <input type="number" id="progSort" name="sort_order" value="<?= e($get('sort_order', 0)) ?>" class="form-control">
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 16px;">
                        <label class="checkbox-field">
                            <input type="checkbox" name="is_featured" value="1" <?= (int) $get('is_featured', 0) === 1 ? 'checked' : '' ?>>
                            <span>Program Unggulan di Beranda</span>
                        </label>
                        <label class="checkbox-field">
                            <input type="checkbox" name="is_published" value="1" <?= (int) $get('is_published', 1) === 1 ? 'checked' : '' ?>>
                            <span>Terbitkan ke Halaman Publik</span>
                        </label>
                    </div>

                    <div style="border-top: 1px solid var(--admin-border); padding-top: 14px;">
                        <label class="form-label">Statistik Singkat</label>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 8px;">
                            <input type="text" name="stat1_value" placeholder="700+" value="<?= e($get('stat1_value')) ?>" class="form-control form-control-sm">
                            <input type="text" name="stat1_label" placeholder="Anak" value="<?= e($get('stat1_label')) ?>" class="form-control form-control-sm">
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                            <input type="text" name="stat2_value" placeholder="13" value="<?= e($get('stat2_value')) ?>" class="form-control form-control-sm">
                            <input type="text" name="stat2_label" placeholder="Kampung" value="<?= e($get('stat2_label')) ?>" class="form-control form-control-sm">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gambar Sampul -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Gambar Sampul</h2>
                        <p class="card-subtitle">JPEG, PNG, WebP (maks 8 MB)</p>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($coverImg)): ?>
                        <div style="margin-bottom: 12px; border-radius: 12px; overflow: hidden; border: 1px solid var(--admin-border); background-color: #f1f5f9;">
                            <img src="<?= e(url('/uploads/' . $coverImg)) ?>" alt="Sampul saat ini" style="width: 100%; height: 160px; object-fit: cover; display: block;">
                        </div>
                    <?php endif; ?>

                    <div class="form-group" style="margin-bottom: 0;">
                        <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp" class="form-control" style="padding-top: 8px;">
                        <?php if (isset($errors['cover_image'])): ?>
                            <small class="field-error"><?= e($errors['cover_image']) ?></small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sticky Action Bar -->
    <div class="sticky-action-bar">
        <a href="<?= url('/admin/program') ?>" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-primary">
            <?= icon('check', '', 16) ?>
            <span><?= $id > 0 ? 'Simpan Perubahan' : 'Simpan Program' ?></span>
        </button>
    </div>
</form>

<!-- Gallery Manager (DESIGN-ADMIN.md section 4.10) - Available in Edit mode -->
<?php if ($id > 0): ?>
    <?php
        $entityType = 'program';
        $entityId = $id;
        $maxPhotos = 30;
        require APP_ROOT . '/app/Views/partials/admin/gallery-manager.php';
    ?>
<?php endif; ?>
