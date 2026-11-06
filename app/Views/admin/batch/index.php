<?php

declare(strict_types=1);

$title = 'Batch';
$breadcrumb = ['Admin', 'Tim', 'Batch'];
$primaryAction = [
    'label' => 'Tambah Batch',
    'url' => url('/admin/batch/tambah'),
    'icon' => 'plus',
];
$totalBatches = count($batches);
?>

<div class="card" style="margin-bottom: 16px; padding: 16px 20px; background: #fafbfc;">
    <p style="margin: 0; color: var(--admin-text-muted); font-size: 13px; line-height: 1.65;">
        Toggle di tabel ini mengatur <strong>batch yang dipilih saja</strong>. Agar pendaftaran tampil di website, sakelar jenis formulirnya juga harus aktif di <a href="<?= url('/admin/pendaftaran') ?>" style="color: var(--admin-primary); font-weight: 600;">Form Pendaftaran</a>. Internship dan Relawan dapat dibuka bersamaan; tutup setiap batch saat masa pendaftarannya selesai.
    </p>
</div>

<div class="card">
    <div class="card-header">
        <div style="display: flex; align-items: center; gap: 10px;">
            <h2 class="card-title">Daftar Batch</h2>
            <span class="badge-pill"><?= $totalBatches ?> batch</span>
        </div>
        <div class="card-filter-bar">
            <select class="card-filter-select" aria-label="Filter Tipe Batch" data-filter-target=".batch-row" data-filter-key="type">
                <option value="">Semua tipe</option>
                <option value="INTERN">Internship</option>
                <option value="VOLUNTEER">Volunteer</option>
            </select>

            <div class="search-pill-wrap">
                <span class="search-pill-icon"><?= icon('search', '', 14) ?></span>
                <input type="search" class="search-pill-input" placeholder="Cari batch..." aria-label="Cari batch" data-search-target=".batch-row">
            </div>
        </div>
    </div>

    <?php if ($batches === []): ?>
        <?php
            $icon = 'layers';
            $emptyTitle = 'Belum ada batch';
            $description = 'Tambahkan batch Intern atau Volunteer untuk mulai mengelola anggota dan pendaftaran.';
            $actionUrl = url('/admin/batch/tambah');
            $actionLabel = 'Tambah Batch';
            require APP_ROOT . '/app/Views/partials/admin/empty-state.php';
        ?>
    <?php else: ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama Batch</th>
                        <th>Tipe</th>
                        <th>Anggota</th>
                        <th>Pendaftar</th>
                        <th>Status Batch</th>
                        <th>Buka / Tutup Batch</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($batches as $b): ?>
                        <?php
                            $isOpen = ((int) ($b['is_open'] ?? 0) === 1);
                            $typeLabel = $b['batch_type'] === 'INTERN' ? 'Internship' : 'Relawan';
                        ?>
                        <tr class="batch-row" data-type="<?= e($b['batch_type']) ?>">
                            <td>
                                <strong><?= e($b['name']) ?></strong>
                                <small style="display: block; color: var(--admin-text-muted); font-size: 11px;">
                                    Dibuat <?= date('d M Y', strtotime($b['created_at'])) ?>
                                </small>
                            </td>
                            <td>
                                <span class="badge-pill"><?= e($typeLabel) ?></span>
                            </td>
                            <td>
                                <span style="font-weight: 600;"><?= (int) $b['member_count'] ?></span>
                            </td>
                            <td>
                                <span style="font-weight: 600;"><?= (int) $b['application_count'] ?></span>
                            </td>
                            <td>
                                <?php $status = $isOpen ? 'open' : 'closed'; require APP_ROOT . '/app/Views/partials/admin/status-pill.php'; ?>
                            </td>
                            <td>
                                <form method="post" action="<?= url('/admin/batch/' . (int) $b['id'] . '/toggle') ?>">
                                    <?= csrf_field() ?>
                                    <label class="toggle-switch" title="<?= $isOpen ? 'Tutup batch ini untuk pendaftaran' : 'Buka batch ini untuk pendaftaran' ?>">
                                        <input type="checkbox" class="toggle-switch-input" <?= $isOpen ? 'checked' : '' ?> data-auto-submit>
                                        <span class="toggle-switch-track"></span>
                                        <span class="toggle-switch-label" style="font-size: 12px; font-weight: 500;">
                                            <?= $isOpen ? 'Dibuka' : 'Ditutup' ?>
                                        </span>
                                    </label>
                                </form>
                            </td>
                            <td style="text-align: right;">
                                <div class="table-actions" style="justify-content: flex-end;">
                                    <a href="<?= url('/admin/batch/' . (int) $b['id'] . '/ubah') ?>" class="btn btn-soft btn-xs">
                                        <?= icon('edit', '', 13) ?>
                                        <span>Ubah</span>
                                    </a>
                                    <form method="post" action="<?= url('/admin/batch/' . (int) $b['id'] . '/hapus') ?>" data-confirm="Hapus batch &quot;<?= e($b['name']) ?>&quot;? Batch yang sudah memiliki anggota atau pendaftar tidak dapat dihapus.">
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
            <span style="font-size: 12px; color: var(--admin-text-muted);">Menampilkan <?= $totalBatches ?> batch</span>
        </div>
    <?php endif; ?>
</div>
