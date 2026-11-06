<?php

declare(strict_types=1);

$id = (int) ($post['id'] ?? 0);
$get = static fn(string $key, mixed $default = '') => $values[$key] ?? $default;
$dateValue = $get('published_at', date('Y-m-d H:i:s'));
$dateValue = $dateValue ? date('Y-m-d\TH:i', strtotime((string) $dateValue)) : '';
$title = $id > 0 ? 'Ubah Berita' : 'Tambah Berita';
$breadcrumb = ['Admin', 'Konten', 'Berita', $id > 0 ? 'Ubah' : 'Tambah'];
$hasStickyActionBar = true;

$labels = [
    'ACTIVITY' => 'Kegiatan Lapangan',
    'UPDATE' => 'Update Program',
    'MEDIA' => 'Liputan Media',
    'AWARD' => 'Penghargaan',
];
$coverImg = $get('cover_image');
$coverFit = in_array($get('category', 'ACTIVITY'), ['AWARD', 'MEDIA'], true) ? 'contain' : 'cover';
?>

<form action="<?= url($id > 0 ? '/admin/berita/' . $id : '/admin/berita') ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <?php if (isset($errors['cover_image'])): ?>
        <div class="toast toast-error" style="position: static; max-width: 100%; margin-bottom: 20px;">
            <div class="toast-body"><?= e($errors['cover_image']) ?></div>
        </div>
    <?php endif; ?>

    <div class="grid-12">
        <!-- Main Column (8 of 12 cols) -->
        <div class="col-8">
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Informasi Berita</h2>
                        <p class="card-subtitle">Judul, slug URL, ringkasan, dan isi berita.</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label" for="postTitle">Judul Berita / Kegiatan *</label>
                        <input type="text" id="postTitle" name="title" maxlength="190" required value="<?= e($get('title')) ?>" class="form-control" placeholder="Contoh: Penyaluran Bantuan Buku di Pulau Mansinam">
                        <?php if (isset($errors['title'])): ?>
                            <small class="field-error"><?= e($errors['title']) ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="postSlug">Slug URL (Otomatis dari judul jika dikosongkan)</label>
                        <input type="text" id="postSlug" name="slug" maxlength="190" value="<?= e($get('slug')) ?>" class="form-control" placeholder="penyaluran-bantuan-buku">
                        <?php if (isset($errors['slug'])): ?>
                            <small class="field-error"><?= e($errors['slug']) ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="postExcerpt">Ringkasan Singkat *</label>
                        <textarea id="postExcerpt" name="excerpt" maxlength="300" rows="3" required class="form-control"><?= e($get('excerpt')) ?></textarea>
                        <?php if (isset($errors['excerpt'])): ?>
                            <small class="field-error"><?= e($errors['excerpt']) ?></small>
                        <?php endif; ?>
                        <small class="field-hint">Maksimal 300 karakter untuk pratinjau kartu berita.</small>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" for="postContent">Isi Berita Lengkap *</label>
                        <textarea id="postContent" name="content" rows="10" required class="form-control" style="min-height: 220px;"><?= e($get('content')) ?></textarea>
                        <?php if (isset($errors['content'])): ?>
                            <small class="field-error"><?= e($errors['content']) ?></small>
                        <?php endif; ?>
                        <small class="field-hint">Teks biasa dengan dukungan <code>**tebal**</code> dan kutipan <code>&gt; kutipan</code>.</small>
                    </div>
                </div>
            </div>

            <!-- Detail Tambahan (Penghargaan / Liputan Media) -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Detail Tambahan</h2>
                        <p class="card-subtitle">Khusus untuk kategori Liputan Media atau Penghargaan.</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label" for="metaLabel">Keterangan Khusus / Pemberi Penghargaan</label>
                        <input type="text" id="metaLabel" name="meta_label" maxlength="120" value="<?= e($get('meta_label')) ?>" class="form-control" placeholder="Contoh: Astra SATU Indonesia Awards 2022">
                        <?php if (isset($errors['meta_label'])): ?>
                            <small class="field-error"><?= e($errors['meta_label']) ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="grid-12" style="gap: 12px; margin-bottom: 0;">
                        <div class="col-8" style="grid-column: span 6 / span 6;">
                            <label class="form-label" for="sourceName">Nama Sumber / Media</label>
                            <input type="text" id="sourceName" name="source_name" maxlength="120" value="<?= e($get('source_name')) ?>" class="form-control" placeholder="Contoh: Kompas.com">
                            <?php if (isset($errors['source_name'])): ?>
                                <small class="field-error"><?= e($errors['source_name']) ?></small>
                            <?php endif; ?>
                        </div>
                        <div class="col-4" style="grid-column: span 6 / span 6;">
                            <label class="form-label" for="sourceUrl">Tautan Artikel Asli</label>
                            <input type="url" id="sourceUrl" name="source_url" maxlength="500" value="<?= e($get('source_url')) ?>" class="form-control" placeholder="https://...">
                            <?php if (isset($errors['source_url'])): ?>
                                <small class="field-error"><?= e($errors['source_url']) ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Side Column (4 of 12 cols) -->
        <div class="col-4">
            <!-- Publikasi -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Publikasi</h2>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label" for="postCategory">Kategori *</label>
                        <select id="postCategory" name="category" required class="form-control">
                            <?php foreach ($labels as $key => $lbl): ?>
                                <option value="<?= e($key) ?>" <?= $get('category', 'ACTIVITY') === $key ? 'selected' : '' ?>><?= e($lbl) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['category'])): ?>
                            <small class="field-error"><?= e($errors['category']) ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="postPublishedAt">Tanggal Terbit *</label>
                        <input type="datetime-local" id="postPublishedAt" name="published_at" required value="<?= e($dateValue) ?>" class="form-control">
                        <?php if (isset($errors['published_at'])): ?>
                            <small class="field-error"><?= e($errors['published_at']) ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="authorName">Nama Penulis</label>
                        <input type="text" id="authorName" name="author_name" maxlength="100" value="<?= e($get('author_name', 'Tim Papua Future Project')) ?>" class="form-control">
                        <?php if (isset($errors['author_name'])): ?>
                            <small class="field-error"><?= e($errors['author_name']) ?></small>
                        <?php endif; ?>
                    </div>

                    <div style="border-top: 1px solid var(--admin-border); padding-top: 14px;">
                        <label class="checkbox-field">
                            <input type="checkbox" name="is_published" value="1" <?= (int) $get('is_published', 1) === 1 ? 'checked' : '' ?>>
                            <span>Terbitkan ke Halaman Publik</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Foto utama atau logo -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Foto utama atau logo</h2>
                        <p class="card-subtitle">Unggah logo resmi untuk penghargaan atau media. JPEG, PNG, WebP (maks 8 MB).</p>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($coverImg)): ?>
                        <div style="margin-bottom: 12px; border-radius: 12px; overflow: hidden; border: 1px solid var(--admin-border); background-color: #f1f5f9;">
                            <img src="<?= e(url('/uploads/' . $coverImg)) ?>" alt="Foto atau logo saat ini" style="width: 100%; height: 160px; object-fit: <?= e($coverFit) ?>; display: block;">
                        </div>
                    <?php endif; ?>

                    <div class="form-group" style="margin-bottom: 0;">
                        <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp" class="form-control" style="padding-top: 8px;">
                        <small class="field-hint">Penghargaan: gunakan logo penghargaan. Liputan media: gunakan logo media atau foto liputan. Aktivitas: gunakan foto dokumentasi.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sticky Action Bar -->
    <div class="sticky-action-bar">
        <a href="<?= url('/admin/berita') ?>" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-primary">
            <?= icon('check', '', 16) ?>
            <span><?= $id > 0 ? 'Simpan Perubahan' : 'Simpan Berita' ?></span>
        </button>
    </div>
</form>

<!-- Gallery Manager (DESIGN-ADMIN.md section 4.10) - Available in Edit mode -->
<?php if ($id > 0): ?>
    <?php
        $entityType = 'berita';
        $entityId = $id;
        $maxPhotos = 30;
        require APP_ROOT . '/app/Views/partials/admin/gallery-manager.php';
    ?>
<?php endif; ?>
