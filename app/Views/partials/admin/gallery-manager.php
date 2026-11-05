<?php

declare(strict_types=1);

/**
 * Reusable Gallery Manager component (DESIGN-ADMIN.md section 4.10)
 *
 * Parameters:
 * - $entityType: 'program' | 'berita'
 * - $entityId: int
 * - $photos: array
 * - $maxPhotos: int (default 30)
 */
$maxPhotos = $maxPhotos ?? 30;
$photoCount = count($photos ?? []);
$uploadUrl = url('/admin/' . $entityType . '/' . $entityId . '/foto');
?>
<section class="card gallery-manager-card" id="gallerySection">
    <div class="card-header">
        <div>
            <h2 class="card-title">Foto Dokumentasi</h2>
            <p class="card-subtitle">Kelola dokumentasi kegiatan untuk ditampilkan di halaman publik.</p>
        </div>
        <span class="badge-pill"><?= $photoCount ?> dari <?= $maxPhotos ?> foto</span>
    </div>

    <div class="card-body">
        <?php if ($photoCount < $maxPhotos): ?>
            <form action="<?= e($uploadUrl) ?>" method="post" enctype="multipart/form-data" class="gallery-upload-banner">
                <?= csrf_field() ?>
                <div class="gallery-upload-fields">
                    <label class="gallery-dropzone-label">
                        <input type="file" name="photos[]" accept="image/jpeg,image/png,image/webp" multiple required class="gallery-file-input">
                        <?= icon('camera', 'gallery-dropzone-icon', 24) ?>
                        <span class="gallery-dropzone-text">Pilih satu atau beberapa foto (JPEG, PNG, WebP · maks 8 MB per file)</span>
                    </label>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <?= icon('plus', 'btn-icon', 16) ?>
                        <span>Unggah Foto</span>
                    </button>
                </div>
            </form>
        <?php endif; ?>

        <?php if ($photoCount === 0): ?>
            <div class="empty-state empty-state-compact">
                <div class="empty-state-icon">
                    <?= icon('camera', 'empty-icon-svg', 32) ?>
                </div>
                <h4 class="empty-state-title">Belum ada foto dokumentasi</h4>
                <p class="empty-state-desc">Unggah foto kegiatan lapangan untuk melengkapi halaman ini.</p>
            </div>
        <?php else: ?>
            <div class="gallery-grid">
                <?php foreach ($photos as $photo): ?>
                    <?php
                        $photoId = (int) $photo['id'];
                        $updateUrl = url('/admin/' . $entityType . '/' . $entityId . '/foto/' . $photoId);
                        $deleteUrl = url('/admin/' . $entityType . '/' . $entityId . '/foto/' . $photoId . '/hapus');
                    ?>
                    <div class="gallery-item">
                        <div class="gallery-thumb-wrap">
                            <img src="<?= e(url('/uploads/' . $photo['file_path'])) ?>" alt="Dokumentasi" class="gallery-thumb" loading="lazy">
                            <form method="post" action="<?= e($deleteUrl) ?>" class="gallery-delete-form" data-confirm="Hapus foto dokumentasi ini?">
                                <?= csrf_field() ?>
                                <button type="submit" class="gallery-delete-btn" aria-label="Hapus foto" title="Hapus foto">
                                    <?= icon('x', '', 14) ?>
                                </button>
                            </form>
                        </div>
                        <form method="post" action="<?= e($updateUrl) ?>" class="gallery-item-form">
                            <?= csrf_field() ?>
                            <input type="text" name="caption" maxlength="160" value="<?= e($photo['caption'] ?? '') ?>" placeholder="Keterangan foto..." class="form-control form-control-sm">
                            <div class="gallery-item-bottom">
                                <label class="gallery-order-label">
                                    <span>Urutan:</span>
                                    <input type="number" name="sort_order" value="<?= (int) ($photo['sort_order'] ?? 0) ?>" class="form-control form-control-sm gallery-order-input">
                                </label>
                                <button type="submit" class="btn btn-soft btn-xs">Simpan</button>
                            </div>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

