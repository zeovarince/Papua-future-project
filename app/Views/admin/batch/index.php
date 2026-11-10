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

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger" role="alert">
        <?= e(implode(' ', array_map('strval', $errors))) ?>
    </div>
<?php endif; ?>

<p class="page-description" style="margin: 0 0 18px; color: var(--admin-text-muted);">
    Atur pendaftaran di sini: masukkan link formulir apa pun lewat <strong>Ubah</strong>, lalu buka atau tutup dengan sakelar. Setiap batch memiliki link dan status sendiri.
</p>

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
            $description = 'Tambahkan batch Internship atau Relawan untuk mengelompokkan anggota tim.';
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
                        <th>Status Pendaftaran</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($batches as $b): ?>
                        <?php
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
                                <?php $hasFormUrl = trim((string) ($b['google_form_url'] ?? '')) !== ''; ?>
                                <?php if ($hasFormUrl): ?>
                                    <form method="post" action="<?= url('/admin/batch/' . (int) $b['id'] . '/toggle') ?>" style="display: inline-flex; align-items: center;">
                                        <?= csrf_field() ?>
                                        <label class="toggle-switch" title="<?= (int) $b['is_open'] === 1 ? 'Tutup pendaftaran' : 'Buka pendaftaran' ?>">
                                            <input type="checkbox" class="toggle-switch-input" <?= (int) $b['is_open'] === 1 ? 'checked' : '' ?> data-auto-submit aria-label="<?= (int) $b['is_open'] === 1 ? 'Tutup' : 'Buka' ?> pendaftaran <?= e($b['name']) ?>">
                                            <span class="toggle-switch-track"></span>
                                            <span class="toggle-switch-label"><?= (int) $b['is_open'] === 1 ? 'Dibuka' : 'Ditutup' ?></span>
                                        </label>
                                    </form>
                                <?php else: ?>
                                    <span class="toggle-switch-label" title="Isi link pendaftaran melalui Ubah Batch">Ditutup · link belum diisi</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: right;">
                                <div class="table-actions" style="justify-content: flex-end;">
                                    <a href="<?= url('/admin/batch/' . (int) $b['id'] . '/ubah') ?>" class="btn btn-soft btn-xs">
                                        <?= icon('edit', '', 13) ?>
                                        <span>Ubah</span>
                                    </a>
                                    <?php $deletePrompt = 'Hapus batch "' . (string) $b['name'] . '"? Batch yang sudah memiliki anggota tim tidak dapat dihapus.'; ?>
                                    <form method="post" action="<?= url('/admin/batch/' . (int) $b['id'] . '/hapus') ?>" data-confirm="<?= e($deletePrompt) ?>">
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
