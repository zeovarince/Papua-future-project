<?php

declare(strict_types=1);

$title = 'Pendaftar';
$breadcrumb = ['Admin', 'Data', 'Pendaftar'];

$exportQuery = http_build_query(array_filter([
    'tipe' => $filters['type'],
    'batch' => $filters['batch_id'],
    'status' => $filters['status'],
    'q' => $filters['q'],
], static fn (mixed $value): bool => $value !== '' && $value !== null));

$queryForPage = static function (int $page) use ($exportQuery): string {
    parse_str($exportQuery, $query);
    $query['page'] = $page;
    return http_build_query($query);
};

$totalApplicants = (int) ($pagination['total'] ?? 0);
$currentPage = (int) ($pagination['page'] ?? 1);
$lastPage = (int) ($pagination['last_page'] ?? 1);
?>

<div class="card">
    <div class="card-header" style="flex-wrap: wrap; gap: 14px;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <h2 class="card-title">Daftar Pendaftar</h2>
            <span class="badge-pill"><?= number_format($totalApplicants, 0, ',', '.') ?> data</span>
        </div>

        <div style="display: flex; align-items: center; gap: 8px; margin-left: auto; flex-wrap: wrap;">
            <!-- Export dropdown / buttons -->
            <a href="<?= url('/admin/pendaftar/ekspor/csv' . ($exportQuery === '' ? '' : '?' . $exportQuery)) ?>" class="btn btn-secondary btn-sm" title="Unduh format CSV">
                <?= icon('download', '', 14) ?>
                <span>CSV</span>
            </a>
            <a href="<?= url('/admin/pendaftar/ekspor/xlsx' . ($exportQuery === '' ? '' : '?' . $exportQuery)) ?>" class="btn btn-secondary btn-sm" title="Unduh format Excel">
                <?= icon('download', '', 14) ?>
                <span>Excel</span>
            </a>
            <a href="<?= url('/admin/pendaftar/ekspor/pdf' . ($exportQuery === '' ? '' : '?' . $exportQuery)) ?>" class="btn btn-primary btn-sm" title="Unduh format PDF">
                <?= icon('file-text', '', 14) ?>
                <span>PDF</span>
            </a>
        </div>
    </div>

    <!-- Filter Bar (DESIGN-ADMIN.md section 5.10) -->
    <div style="padding: 16px 20px; border-bottom: 1px solid var(--admin-border); background-color: #fafbfc;">
        <form action="<?= url('/admin/pendaftar') ?>" method="get" style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap; margin: 0;">
            <select name="tipe" class="card-filter-select" aria-label="Filter Tipe">
                <option value="">Semua tipe</option>
                <?php foreach ($types as $key => $label): ?>
                    <option value="<?= e($key) ?>" <?= $filters['type'] === $key ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>

            <select name="batch" class="card-filter-select" aria-label="Filter Batch">
                <option value="">Semua batch</option>
                <?php foreach ($batches as $b): ?>
                    <option value="<?= (int) $b['id'] ?>" <?= (int) ($filters['batch_id'] ?? 0) === (int) $b['id'] ? 'selected' : '' ?>>
                        <?= e($b['name']) ?> (<?= $b['batch_type'] === 'INTERN' ? 'Intern' : 'Volunteer' ?>)
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="status" class="card-filter-select" aria-label="Filter Status">
                <option value="">Semua status</option>
                <?php foreach ($statuses as $key => $label): ?>
                    <option value="<?= e($key) ?>" <?= $filters['status'] === $key ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>

            <div class="search-pill-wrap">
                <span class="search-pill-icon"><?= icon('search', '', 14) ?></span>
                <input type="search" name="q" class="search-pill-input" placeholder="Cari nama pendaftar..." value="<?= e($filters['q']) ?>" aria-label="Cari nama pendaftar">
            </div>

            <button type="submit" class="btn btn-primary btn-sm">Terapkan</button>
            <?php if (!empty($exportQuery)): ?>
                <a href="<?= url('/admin/pendaftar') ?>" class="btn btn-ghost btn-sm">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if ($applications === []): ?>
        <?php
            $icon = 'clipboard-list';
            $emptyTitle = 'Belum ada pendaftar yang cocok';
            $description = 'Ubah filter pencarian atau tunggu pendaftaran baru masuk.';
            require APP_ROOT . '/app/Views/partials/admin/empty-state.php';
        ?>
    <?php else: ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Batch</th>
                        <th>Tipe</th>
                        <th>Status</th>
                        <th>Tanggal Daftar</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($applications as $app): ?>
                        <tr>
                            <td>
                                <strong><?= e($app['full_name']) ?></strong>
                            </td>
                            <td>
                                <a href="mailto:<?= e($app['email']) ?>" style="color: var(--admin-primary);"><?= e($app['email']) ?></a>
                            </td>
                            <td>
                                <span class="badge-pill"><?= e($app['batch_name']) ?></span>
                            </td>
                            <td>
                                <span class="badge-pill"><?= $app['batch_type'] === 'INTERN' ? 'Intern' : 'Volunteer' ?></span>
                            </td>
                            <td>
                                <?php $status = $app['status']; require APP_ROOT . '/app/Views/partials/admin/status-pill.php'; ?>
                            </td>
                            <td>
                                <span style="color: var(--admin-text-muted); font-size: 12px;"><?= date('d M Y H:i', strtotime($app['created_at'])) ?> WIT</span>
                            </td>
                            <td style="text-align: right;">
                                <a href="<?= url('/admin/pendaftar/' . (int) $app['id']) ?>" class="btn btn-soft btn-xs">
                                    <?= icon('eye', '', 13) ?>
                                    <span>Lihat</span>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination (DESIGN-ADMIN.md section 4.9) -->
        <div class="pagination-wrap">
            <span class="pagination-info">
                Menampilkan <?= count($applications) ?> dari <?= number_format($totalApplicants, 0, ',', '.') ?> pendaftar
                <?php if ($lastPage > 1): ?>
                    (Halaman <?= $currentPage ?> dari <?= $lastPage ?>)
                <?php endif; ?>
            </span>

            <?php if ($lastPage > 1): ?>
                <div class="pagination-controls" aria-label="Navigasi Halaman">
                    <?php if ($currentPage > 1): ?>
                        <a href="<?= url('/admin/pendaftar?' . ($queryForPage)($currentPage - 1)) ?>" class="pagination-btn" aria-label="Halaman sebelumnya">
                            <?= icon('chevron-left', '', 14) ?>
                        </a>
                    <?php else: ?>
                        <span class="pagination-btn is-disabled" aria-hidden="true"><?= icon('chevron-left', '', 14) ?></span>
                    <?php endif; ?>

                    <?php for ($p = max(1, $currentPage - 2); $p <= min($lastPage, $currentPage + 2); $p++): ?>
                        <?php if ($p === $currentPage): ?>
                            <span class="pagination-btn is-active" aria-current="page"><?= $p ?></span>
                        <?php else: ?>
                            <a href="<?= url('/admin/pendaftar?' . ($queryForPage)($p)) ?>" class="pagination-btn"><?= $p ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($currentPage < $lastPage): ?>
                        <a href="<?= url('/admin/pendaftar?' . ($queryForPage)($currentPage + 1)) ?>" class="pagination-btn" aria-label="Halaman berikutnya">
                            <?= icon('chevron-right', '', 14) ?>
                        </a>
                    <?php else: ?>
                        <span class="pagination-btn is-disabled" aria-hidden="true"><?= icon('chevron-right', '', 14) ?></span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
