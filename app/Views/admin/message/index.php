<?php

declare(strict_types=1);

$title = 'Pesan Masuk';
$breadcrumb = ['Admin', 'Data', 'Pesan Masuk'];
$totalMessages = count($messages);
$unreadTotal = count(array_filter($messages, static fn($m) => (int) $m['is_read'] === 0));
?>

<div class="card">
    <div class="card-header">
        <div style="display: flex; align-items: center; gap: 10px;">
            <h2 class="card-title">Daftar Pesan Masuk</h2>
            <span class="badge-pill"><?= $totalMessages ?> pesan</span>
            <?php if ($unreadTotal > 0): ?>
                <span class="status-pill status-pill--rejected"><?= $unreadTotal ?> belum dibaca</span>
            <?php endif; ?>
        </div>
        <div class="card-filter-bar">
            <div class="search-pill-wrap">
                <span class="search-pill-icon"><?= icon('search', '', 14) ?></span>
                <input type="search" class="search-pill-input" placeholder="Cari pesan atau pengirim..." aria-label="Cari pesan" data-search-target=".message-row">
            </div>
        </div>
    </div>

    <?php if ($messages === []): ?>
        <?php
            $icon = 'mail';
            $emptyTitle = 'Belum ada pesan masuk';
            $description = 'Pesan yang dikirim pengunjung dari formulir kontak publik akan tercatat di sini.';
            require APP_ROOT . '/app/Views/partials/admin/empty-state.php';
        ?>
    <?php else: ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Pengirim</th>
                        <th>Email</th>
                        <th>Ringkasan Pesan</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $msg): ?>
                        <?php $isUnread = ((int) $msg['is_read'] === 0); ?>
                        <tr class="message-row <?= $isUnread ? 'unread-row' : '' ?>">
                            <td>
                                <div style="display: flex; align-items: center;">
                                    <?php if ($isUnread): ?>
                                        <span class="unread-dot" title="Belum dibaca"></span>
                                    <?php endif; ?>
                                    <strong><?= e($msg['full_name']) ?></strong>
                                </div>
                            </td>
                            <td>
                                <a href="mailto:<?= e($msg['email']) ?>" style="color: var(--admin-primary);"><?= e($msg['email']) ?></a>
                            </td>
                            <td>
                                <span><?= e(mb_strimwidth($msg['message'], 0, 50, '…')) ?></span>
                            </td>
                            <td>
                                <span style="color: var(--admin-text-muted); font-size: 12px;"><?= date('d M Y H:i', strtotime($msg['created_at'])) ?> WIT</span>
                            </td>
                            <td>
                                <?php $status = $isUnread ? 'unread' : 'read'; require APP_ROOT . '/app/Views/partials/admin/status-pill.php'; ?>
                            </td>
                            <td style="text-align: right;">
                                <a href="<?= url('/admin/pesan/' . (int) $msg['id']) ?>" class="btn btn-soft btn-xs">
                                    <?= icon('eye', '', 13) ?>
                                    <span>Lihat</span>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer" style="display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 12px; color: var(--admin-text-muted);">Menampilkan <?= $totalMessages ?> pesan</span>
        </div>
    <?php endif; ?>
</div>
