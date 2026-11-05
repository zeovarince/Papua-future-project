<?php

declare(strict_types=1);

$title = 'Program';
$breadcrumb = ['Admin', 'Konten', 'Program'];
$primaryAction = [
    'label' => 'Tambah Program',
    'url' => url('/admin/program/tambah'),
    'icon' => 'plus',
];
$totalPrograms = count($programs);
?>

<div class="card">
    <div class="card-header">
        <div style="display: flex; align-items: center; gap: 10px;">
            <h2 class="card-title">Daftar Program</h2>
            <span class="badge-pill"><?= $totalPrograms ?> program</span>
        </div>
        <div class="card-filter-bar">
            <div class="search-pill-wrap">
                <span class="search-pill-icon"><?= icon('search', '', 14) ?></span>
                <input type="search" class="search-pill-input" placeholder="Cari program..." aria-label="Cari program" data-search-target=".program-row">
            </div>
        </div>
    </div>

    <?php if ($programs === []): ?>
        <?php
            $icon = 'book-open';
            $emptyTitle = 'Belum ada program';
            $description = 'Tambahkan program pertama untuk ditampilkan di website.';
            $actionUrl = url('/admin/program/tambah');
            $actionLabel = 'Tambah Program';
            require APP_ROOT . '/app/Views/partials/admin/empty-state.php';
        ?>
    <?php else: ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 76px;">Sampul</th>
                        <th>Program</th>
                        <th>Kategori</th>
                        <th>Unggulan</th>
                        <th>Status</th>
                        <th>Urutan</th>
                        <th>Foto</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($programs as $prog): ?>
                        <?php
                            $cover = !empty($prog['cover_image']) ? url('/uploads/' . $prog['cover_image']) : null;
                            $isPublished = ((int) ($prog['is_published'] ?? 0) === 1);
                            $isFeatured = ((int) ($prog['is_featured'] ?? 0) === 1);
                        ?>
                        <tr class="program-row">
                            <td>
                                <?php if ($cover): ?>
                                    <img src="<?= e($cover) ?>" alt="" class="table-thumb" loading="lazy">
                                <?php else: ?>
                                    <div class="table-thumb" style="display: flex; align-items: center; justify-content: center; color: var(--admin-text-muted);">
                                        <?= icon('book-open', '', 18) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= e($prog['title']) ?></strong>
                                <small style="display: block; color: var(--admin-text-muted); font-size: 11px;">/program/<?= e($prog['slug']) ?></small>
                            </td>
                            <td>
                                <span class="badge-pill"><?= e($prog['category_label'] ?? 'Program') ?></span>
                            </td>
                            <td>
                                <?php if ($isFeatured): ?>
                                    <span style="color: #f59e0b; display: inline-flex; align-items: center; gap: 4px;" title="Program Unggulan">
                                        <?= icon('star', '', 16) ?>
                                        <span style="font-size: 12px; font-weight: 600;">Ya</span>
                                    </span>
                                <?php else: ?>
                                    <span style="color: var(--admin-text-muted); font-size: 12px;">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php $status = $isPublished ? 'published' : 'draft'; require APP_ROOT . '/app/Views/partials/admin/status-pill.php'; ?>
                            </td>
                            <td>
                                <span style="font-weight: 600; color: var(--admin-text-muted);"><?= (int) $prog['sort_order'] ?></span>
                            </td>
                            <td>
                                <span class="badge-pill"><?= (int) ($prog['photo_count'] ?? 0) ?> foto</span>
                            </td>
                            <td style="text-align: right;">
                                <div class="table-actions" style="justify-content: flex-end;">
                                    <a href="<?= url('/admin/program/' . (int) $prog['id'] . '/ubah') ?>" class="btn btn-soft btn-xs">
                                        <?= icon('edit', '', 13) ?>
                                        <span>Ubah</span>
                                    </a>
                                    <form method="post" action="<?= url('/admin/program/' . (int) $prog['id'] . '/hapus') ?>" data-confirm="Hapus program &quot;<?= e($prog['title']) ?>&quot; dan semua dokumentasinya?">
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
            <span style="font-size: 12px; color: var(--admin-text-muted);">Menampilkan <?= $totalPrograms ?> program</span>
        </div>
    <?php endif; ?>
</div>
