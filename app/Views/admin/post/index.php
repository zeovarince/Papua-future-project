<?php

declare(strict_types=1);

$title = 'Berita & Kegiatan';
$breadcrumb = ['Admin', 'Konten', 'Berita'];
$primaryAction = [
    'label' => 'Tambah Berita',
    'url' => url('/admin/berita/tambah'),
    'icon' => 'plus',
];

$labels = [
    'ACTIVITY' => 'Kegiatan Lapangan',
    'UPDATE' => 'Update Program',
    'MEDIA' => 'Liputan Media',
    'AWARD' => 'Penghargaan',
];
$totalPosts = count($posts);
?>

<div class="card">
    <div class="card-header">
        <div style="display: flex; align-items: center; gap: 10px;">
            <h2 class="card-title">Daftar Berita &amp; Kegiatan</h2>
            <span class="badge-pill"><?= $totalPosts ?> artikel</span>
        </div>
        <div class="card-filter-bar">
            <select class="card-filter-select" aria-label="Filter Kategori" data-filter-target=".post-row" data-filter-key="category">
                <option value="">Semua kategori</option>
                <?php foreach ($labels as $key => $lbl): ?>
                    <option value="<?= e($key) ?>"><?= e($lbl) ?></option>
                <?php endforeach; ?>
            </select>

            <div class="search-pill-wrap">
                <span class="search-pill-icon"><?= icon('search', '', 14) ?></span>
                <input type="search" class="search-pill-input" placeholder="Cari judul berita..." aria-label="Cari judul berita" data-search-target=".post-row">
            </div>
        </div>
    </div>

    <?php if ($posts === []): ?>
        <?php
            $icon = 'newspaper';
            $emptyTitle = 'Belum ada berita';
            $description = 'Tambahkan artikel atau kegiatan pertama untuk dipublikasikan.';
            $actionUrl = url('/admin/berita/tambah');
            $actionLabel = 'Tambah Berita';
            require APP_ROOT . '/app/Views/partials/admin/empty-state.php';
        ?>
    <?php else: ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 76px;">Sampul</th>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Tanggal Terbit</th>
                        <th>Foto</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($posts as $post): ?>
                        <?php
                            $cover = !empty($post['cover_image']) ? url('/uploads/' . $post['cover_image']) : null;
                            $isPublished = ((int) ($post['is_published'] ?? 0) === 1);
                        ?>
                        <tr class="post-row" data-category="<?= e($post['category']) ?>">
                            <td>
                                <?php if ($cover): ?>
                                    <img src="<?= e($cover) ?>" alt="" class="table-thumb" loading="lazy">
                                <?php else: ?>
                                    <div class="table-thumb" style="display: flex; align-items: center; justify-content: center; color: var(--admin-text-muted);">
                                        <?= icon('newspaper', '', 18) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= e($post['title']) ?></strong>
                                <small style="display: block; color: var(--admin-text-muted); font-size: 11px;">/berita/<?= e($post['slug']) ?></small>
                            </td>
                            <td>
                                <span class="badge-pill"><?= e($labels[$post['category']] ?? $post['category']) ?></span>
                            </td>
                            <td>
                                <?php $status = $isPublished ? 'published' : 'draft'; require APP_ROOT . '/app/Views/partials/admin/status-pill.php'; ?>
                            </td>
                            <td>
                                <span style="color: var(--admin-text-muted); font-size: 12px;"><?= date('d M Y H:i', strtotime($post['published_at'])) ?> WIT</span>
                            </td>
                            <td>
                                <span class="badge-pill"><?= (int) ($post['photo_count'] ?? 0) ?> foto</span>
                            </td>
                            <td style="text-align: right;">
                                <div class="table-actions" style="justify-content: flex-end;">
                                    <a href="<?= url('/admin/berita/' . (int) $post['id'] . '/ubah') ?>" class="btn btn-soft btn-xs">
                                        <?= icon('edit', '', 13) ?>
                                        <span>Ubah</span>
                                    </a>
                                    <form method="post" action="<?= url('/admin/berita/' . (int) $post['id'] . '/hapus') ?>" data-confirm="Hapus berita &quot;<?= e($post['title']) ?>&quot; dan semua fotonya?">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-soft-danger btn-xs">
                                            <?= icon('trash-2', '', 13) ?>
                                            <span>Hapus</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer" style="display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 12px; color: var(--admin-text-muted);">Menampilkan <?= $totalPosts ?> artikel</span>
        </div>
    <?php endif; ?>
</div>
