<?php

declare(strict_types=1);

$title = 'FAQ';
$breadcrumb = ['Admin', 'Konten', 'FAQ'];
$primaryAction = [
    'label' => 'Tambah FAQ',
    'url' => url('/admin/faq/tambah'),
    'icon' => 'plus',
];
$totalFaqs = count($faqs);
?>

<div class="card">
    <div class="card-header">
        <div style="display: flex; align-items: center; gap: 10px;">
            <h2 class="card-title">Daftar Pertanyaan Umum (FAQ)</h2>
            <span class="badge-pill"><?= $totalFaqs ?> pertanyaan</span>
        </div>
        <div class="card-filter-bar">
            <div class="search-pill-wrap">
                <span class="search-pill-icon"><?= icon('search', '', 14) ?></span>
                <input type="search" class="search-pill-input" placeholder="Cari pertanyaan..." aria-label="Cari FAQ" data-search-target=".faq-row">
            </div>
        </div>
    </div>

    <?php if ($faqs === []): ?>
        <?php
            $icon = 'help-circle';
            $emptyTitle = 'Belum ada FAQ';
            $description = 'Tambahkan pertanyaan yang sering diajukan pengunjung di halaman Kontak.';
            $actionUrl = url('/admin/faq/tambah');
            $actionLabel = 'Tambah FAQ';
            require APP_ROOT . '/app/Views/partials/admin/empty-state.php';
        ?>
    <?php else: ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Pertanyaan</th>
                        <th style="width: 100px;">Urutan</th>
                        <th style="width: 140px;">Status</th>
                        <th style="width: 160px; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($faqs as $faq): ?>
                        <?php $isPublished = ((int) ($faq['is_published'] ?? 0) === 1); ?>
                        <tr class="faq-row">
                            <td>
                                <strong><?= e(mb_strimwidth($faq['question'], 0, 80, '…')) ?></strong>
                            </td>
                            <td>
                                <span style="font-weight: 600; color: var(--admin-text-muted);"><?= (int) $faq['sort_order'] ?></span>
                            </td>
                            <td>
                                <?php $status = $isPublished ? 'published' : 'closed'; $label = $isPublished ? 'Terbit' : 'Sembunyi'; require APP_ROOT . '/app/Views/partials/admin/status-pill.php'; ?>
                            </td>
                            <td style="text-align: right;">
                                <div class="table-actions" style="justify-content: flex-end;">
                                    <a href="<?= url('/admin/faq/' . (int) $faq['id'] . '/ubah') ?>" class="btn btn-soft btn-xs">
                                        <?= icon('edit', '', 13) ?>
                                        <span>Ubah</span>
                                    </a>
                                    <form method="post" action="<?= url('/admin/faq/' . (int) $faq['id'] . '/hapus') ?>" data-confirm="Hapus FAQ &quot;<?= e($faq['question']) ?>&quot;?">
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
            <span style="font-size: 12px; color: var(--admin-text-muted);">Menampilkan <?= $totalFaqs ?> pertanyaan</span>
        </div>
    <?php endif; ?>
</div>
