<?php

declare(strict_types=1);
?>

<section class="metric-grid" aria-label="Ringkasan website">
    <?php foreach (['unread_messages', 'published_posts'] as $key): ?>
        <?php $metric = $metrics[$key]; ?>
        <a href="<?= e($metric['url']) ?>" class="card metric-card card-clickable">
            <div class="metric-card-top">
                <span class="metric-card-label"><?= e($metric['label']) ?></span>
            </div>
            <div class="metric-card-bottom">
                <div>
                    <div class="metric-card-number"><?= (int) $metric['value'] ?></div>
                    <div class="metric-card-note"><?= e($metric['note']) ?></div>
                </div>
            </div>
        </a>
    <?php endforeach; ?>
</section>

<div class="grid-12">
    <div class="col-12">
        <article class="card">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Pesan Terbaru</h2>
                    <p class="card-subtitle">Pesan dari pengunjung halaman Kontak.</p>
                </div>
                <a href="<?= url('/admin/pesan') ?>" class="card-view-all">Lihat semua</a>
            </div>
            <?php if ($latestMessages === []): ?>
                <div class="empty-state empty-state-compact">
                    <p class="empty-state-desc">Belum ada pesan masuk.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr><th>Pengirim</th><th>Status</th><th style="text-align: right;">Aksi</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($latestMessages as $message): ?>
                                <tr>
                                    <td>
                                        <strong><?= e($message['full_name']) ?></strong>
                                        <small style="display:block;color:var(--admin-text-muted)"><?= e($message['email']) ?></small>
                                    </td>
                                    <td>
                                        <?php $status = (int) $message['is_read'] === 1 ? 'read' : 'unread'; require APP_ROOT . '/app/Views/partials/admin/status-pill.php'; ?>
                                    </td>
                                    <td style="text-align: right;"><a href="<?= url('/admin/pesan/' . (int) $message['id']) ?>" class="btn btn-soft btn-xs">Lihat</a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </article>
    </div>

</div>
