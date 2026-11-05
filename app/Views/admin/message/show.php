<?php

declare(strict_types=1);

$title = 'Pesan dari ' . $message['full_name'];
$breadcrumb = [
    'Admin',
    ['label' => 'Pesan Masuk', 'url' => url('/admin/pesan')],
    $message['full_name'],
];

$cleanWa = preg_replace('/\D+/', '', (string) ($message['whatsapp'] ?? ''));
if (str_starts_with($cleanWa, '0')) {
    $cleanWa = '62' . substr($cleanWa, 1);
}
?>

<div class="grid-12">
    <!-- Main Column (8 of 12 cols): Message Content -->
    <div class="col-8">
        <article class="card">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Isi Pesan Lengkap</h2>
                    <p class="card-subtitle">Diterima pada <?= date('d M Y H:i', strtotime($message['created_at'])) ?> WIT</p>
                </div>
                <span class="status-pill status-pill--read">Sudah dibaca</span>
            </div>
            <div class="card-body">
                <div class="detail-long-text" style="font-size: 14px; line-height: 1.7; background-color: #ffffff; border: none; padding: 0;">
                    <?= nl2br(e($message['message'])) ?>
                </div>
            </div>
        </article>
    </div>

    <!-- Side Column (4 of 12 cols): Contact & Actions -->
    <div class="col-4">
        <!-- Data Pengirim -->
        <article class="card">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Informasi Pengirim</h2>
                </div>
            </div>
            <div class="card-body">
                <dl class="detail-list" style="grid-template-columns: 80px 1fr;">
                    <dt>Nama</dt>
                    <dd><strong><?= e($message['full_name']) ?></strong></dd>

                    <dt>Email</dt>
                    <dd>
                        <a href="mailto:<?= e($message['email']) ?>" style="color: var(--admin-primary); word-break: break-all;">
                            <?= e($message['email']) ?>
                        </a>
                    </dd>

                    <?php if (!empty($message['whatsapp'])): ?>
                        <dt>WhatsApp</dt>
                        <dd><?= e($message['whatsapp']) ?></dd>
                    <?php endif; ?>

                    <dt>Waktu</dt>
                    <dd><?= date('d M Y H:i', strtotime($message['created_at'])) ?></dd>
                </dl>
            </div>
        </article>

        <!-- Tindak Lanjut -->
        <article class="card">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Tindak Lanjut</h2>
                </div>
            </div>
            <div class="card-body">
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <a href="mailto:<?= e($message['email']) ?>" class="btn btn-primary btn-sm" style="justify-content: center;">
                        <?= icon('mail', '', 16) ?>
                        <span>Balas lewat Email</span>
                    </a>

                    <?php if ($cleanWa !== ''): ?>
                        <a href="https://wa.me/<?= e($cleanWa) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-sm" style="justify-content: center;">
                            <?= icon('external-link', '', 16) ?>
                            <span>Balas lewat WhatsApp</span>
                        </a>
                    <?php endif; ?>

                    <form method="post" action="<?= url('/admin/pesan/' . (int) $message['id'] . '/hapus') ?>" data-confirm="Hapus pesan dari &quot;<?= e($message['full_name']) ?>&quot;?" style="margin: 0;">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-soft-danger btn-sm" style="width: 100%; justify-content: center;">
                            <?= icon('trash-2', '', 16) ?>
                            <span>Hapus Pesan</span>
                        </button>
                    </form>
                </div>
            </div>
        </article>

        <a href="<?= url('/admin/pesan') ?>" class="btn btn-ghost btn-sm" style="justify-content: center;">
            <?= icon('arrow-left', '', 15) ?>
            <span>Kembali ke Pesan Masuk</span>
        </a>
    </div>
</div>
