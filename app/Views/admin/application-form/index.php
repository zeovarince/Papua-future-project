<?php

declare(strict_types=1);

$title = 'Form Pendaftaran';
$breadcrumb = ['Admin', 'Data', 'Form Pendaftaran'];
?>

<div style="margin-bottom: 20px;">
    <p style="color: var(--admin-text-muted); font-size: 13px; margin: 0;">
        Buka pendaftaran untuk tiap batch melalui menu Batch. Sakelar formulir di halaman ini adalah pengunci utama per jenis: pendaftaran baru tampil di publik jika formulir aktif dan minimal satu batch pada jenis yang sama dibuka. Internship dan Relawan dapat dibuka bersamaan atau terpisah.
    </p>
</div>

<div class="grid-12">
    <?php foreach ($settings as $setting): ?>
        <?php
            $type = $setting['application_type'];
            $isIntern = ($type === 'INTERN');
            $label = $isIntern ? 'Internship' : 'Relawan';
            $slug = $isIntern ? 'internship' : 'relawan';
            $isActive = ((int) $setting['is_active'] === 1);
            $openBatchCount = (int) $setting['open_batch_count'];
            $isPublicOpen = $isActive && $openBatchCount > 0;
            $iconName = $isIntern ? 'graduation-cap' : 'hand-heart';
        ?>
        <div class="col-6">
            <article class="card" style="padding: 24px; display: flex; flex-direction: column; justify-content: space-between; min-height: 240px;">
                <div>
                    <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; margin-bottom: 16px;">
                        <div>
                            <?php $status = $isPublicOpen ? 'open' : 'closed'; $statusLabel = $isPublicOpen ? 'Pendaftaran publik dibuka' : 'Pendaftaran publik tertutup'; require APP_ROOT . '/app/Views/partials/admin/status-pill.php'; ?>
                            <h2 style="margin: 8px 0 0; font-size: 20px; font-weight: 700; color: var(--admin-text);"><?= e($label) ?></h2>
                        </div>
                        <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(55, 112, 193, 0.1); color: var(--admin-primary); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <?= icon($iconName, '', 22) ?>
                        </div>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 6px; margin-bottom: 24px;">
                        <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--admin-text);">
                            <span style="font-weight: 700; color: var(--admin-primary);"><?= $openBatchCount ?></span>
                            <span>batch berstatus dibuka</span>
                        </div>
                        <?php if ($isActive && $openBatchCount === 0): ?>
                            <p style="margin: 4px 0 0; font-size: 12px; color: var(--admin-text-muted);">Sakelar utama formulir aktif, tetapi belum ada batch yang dibuka. Buka batch yang ingin menerima pendaftar melalui tombol <strong>Kelola Batch</strong>.</p>
                        <?php elseif (!$isActive): ?>
                            <p style="margin: 4px 0 0; font-size: 12px; color: var(--admin-text-muted);">Sakelar utama formulir nonaktif. Aktifkan di sini dan buka minimal satu batch agar pendaftaran tampil di publik.</p>
                        <?php else: ?>
                            <p style="margin: 4px 0 0; font-size: 12px; color: var(--admin-text-muted);">Pendaftaran publik dibuka untuk jenis ini. Atur batch mana yang menerima pendaftar di menu Batch.</p>
                        <?php endif; ?>
                        <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--admin-text-muted);">
                            <span style="font-weight: 600;"><?= (int) $setting['active_question_count'] ?></span>
                            <span>pertanyaan aktif yang tampil di formulir</span>
                        </div>
                    </div>
                </div>

                <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; padding-top: 16px; border-top: 1px solid var(--admin-border); flex-wrap: wrap;">
                    <a href="<?= url('/admin/pendaftaran/' . $slug . '/pertanyaan') ?>" class="btn btn-secondary btn-sm">
                        <?= icon('file-cog', '', 15) ?>
                        <span>Atur Pertanyaan</span>
                    </a>

                    <a href="<?= url('/admin/batch') ?>" class="btn btn-secondary btn-sm">
                        <?= icon('layers', '', 15) ?>
                        <span>Kelola Batch</span>
                    </a>

                    <form method="post" action="<?= url('/admin/pendaftaran/' . $slug . '/toggle') ?>">
                        <?= csrf_field() ?>
                        <label class="toggle-switch" title="<?= $isActive ? 'Nonaktifkan sakelar utama formulir ' . $label : 'Aktifkan sakelar utama formulir ' . $label ?>">
                            <input type="checkbox" class="toggle-switch-input" <?= $isActive ? 'checked' : '' ?> data-auto-submit>
                            <span class="toggle-switch-track"></span>
                            <span class="toggle-switch-label" style="font-size: 12px; font-weight: 500;">
                                <?= $isActive ? 'Form aktif' : 'Form nonaktif' ?>
                            </span>
                        </label>
                    </form>
                </div>
            </article>
        </div>
    <?php endforeach; ?>
</div>

<div class="card" style="margin-top: 20px; padding: 18px 20px; background-color: #fafbfc;">
    <div style="display: flex; align-items: center; gap: 12px;">
        <span style="color: var(--admin-primary); flex-shrink: 0;"><?= icon('info', '', 20) ?></span>
        <p style="margin: 0; font-size: 12px; color: var(--admin-text-muted); line-height: 1.5;">
            Sakelar formulir hanya mengizinkan atau menghentikan semua pendaftaran baru pada jenis tersebut; status tiap batch tetap tersimpan. Tutup batch saat masa pendaftaran berakhir. Anda dapat menonaktifkan formulir utama untuk menghentikan sementara semua pendaftaran pada jenis tersebut tanpa mengubah status batch atau menghapus data lama. <a href="<?= url('/admin/batch') ?>" style="color: var(--admin-primary); font-weight: 600;">Kelola Batch</a>.
        </p>
    </div>
</div>
