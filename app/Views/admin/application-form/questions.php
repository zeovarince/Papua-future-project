<?php

declare(strict_types=1);

$title = 'Pertanyaan ' . $typeLabel;
$breadcrumb = ['Admin', 'Data', 'Form Pendaftaran', 'Pertanyaan ' . $typeLabel];
$primaryAction = [
    'label' => 'Tambah Pertanyaan',
    'url' => url('/admin/pendaftaran/' . $typeSlug . '/pertanyaan/tambah'),
    'icon' => 'plus',
];
$totalQuestions = count($questions);
?>

<p style="margin: 0 0 16px; color: var(--admin-text-muted);">
    Pengaturan Internship dan Relawan terpisah. Edit tiap baris untuk mengubah teks pertanyaan, urutan, tampil/sembunyi, dan wajib/opsional. Pertanyaan bawaan seperti nama, email, WhatsApp, foto, CV, dan portofolio juga ada di daftar ini.
</p>

<!-- Tab Switcher between Internship and Relawan -->
<div style="display: flex; gap: 8px; margin-bottom: 20px;">
    <a href="<?= url('/admin/pendaftaran/internship/pertanyaan') ?>" class="btn <?= $type === 'INTERN' ? 'btn-primary' : 'btn-secondary' ?> btn-sm">
        <?= icon('graduation-cap', '', 15) ?>
        <span>Internship</span>
    </a>
    <a href="<?= url('/admin/pendaftaran/relawan/pertanyaan') ?>" class="btn <?= $type === 'VOLUNTEER' ? 'btn-primary' : 'btn-secondary' ?> btn-sm">
        <?= icon('hand-heart', '', 15) ?>
        <span>Relawan</span>
    </a>
</div>

<div class="card">
    <div class="card-header">
        <div style="display: flex; align-items: center; gap: 10px;">
            <h2 class="card-title">Daftar Pertanyaan Formulir <?= e($typeLabel) ?></h2>
            <span class="badge-pill"><?= $totalQuestions ?> pertanyaan</span>
        </div>
        <div class="card-filter-bar">
            <a href="<?= url('/admin/pendaftaran') ?>" class="btn btn-secondary btn-sm">
                <?= icon('arrow-left', '', 14) ?>
                <span>Kembali ke Status Formulir</span>
            </a>
        </div>
    </div>

    <?php if ($questions === []): ?>
        <?php
            $icon = 'help-circle';
            $emptyTitle = 'Belum ada pertanyaan tambahan';
            $description = 'Formulir ' . $typeLabel . ' sudah memuat isian standar data diri dan berkas. Tambahkan pertanyaan khusus jika diperlukan.';
            $actionUrl = url('/admin/pendaftaran/' . $typeSlug . '/pertanyaan/tambah');
            $actionLabel = 'Tambah Pertanyaan';
            require APP_ROOT . '/app/Views/partials/admin/empty-state.php';
        ?>
    <?php else: ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Pertanyaan</th>
                        <th>Jenis Jawaban</th>
                        <th>Kewajiban</th>
                        <th>Status</th>
                        <th>Urutan</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($questions as $q): ?>
                        <?php $isActive = ((int) ($q['is_active'] ?? 0) === 1); ?>
                        <tr>
                            <td>
                                    <strong><?= e($q['question']) ?></strong>
                                    <?php if ((int) ($q['is_system'] ?? 0) === 1): ?>
                                    <small style="display: block; margin-top: 4px; color: var(--admin-text-muted);">Field bawaan · dapat diatur dari Admin</small>
                                    <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                    $typeBadgeLabels = [
                                        'TEXT' => 'Jawaban Singkat',
                                        'TEXTAREA' => 'Paragraf',
                                        'RADIO' => 'Pilihan Ganda',
                                        'CHECKBOX' => 'Kotak Centang',
                                        'SELECT' => 'Dropdown',
                                        'FILE' => 'Tautan Website',
                                        'EMAIL' => 'Email',
                                        'TEL' => 'Nomor Telepon',
                                        'DATE' => 'Tanggal',
                                        'URL' => 'Tautan Website',
                                        'IMAGE' => 'Unggah Foto',
                                        'PDF' => 'Unggah PDF',
                                    ];
                                ?>
                                <span class="badge-pill"><?= e($typeBadgeLabels[$q['field_type']] ?? $q['field_type']) ?></span>
                            </td>
                            <td>
                                <?php if ((int) $q['is_required'] === 1): ?>
                                    <span style="font-weight: 600; color: var(--admin-primary);">Wajib</span>
                                <?php else: ?>
                                    <span style="color: var(--admin-text-muted);">Opsional</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php $status = $isActive ? 'published' : 'closed'; $label = $isActive ? 'Tampil' : 'Disembunyikan'; require APP_ROOT . '/app/Views/partials/admin/status-pill.php'; ?>
                            </td>
                            <td>
                                <span style="font-weight: 600; color: var(--admin-text-muted);"><?= (int) $q['sort_order'] ?></span>
                            </td>
                            <td style="text-align: right;">
                                <div class="table-actions" style="justify-content: flex-end;">
                                    <a href="<?= url('/admin/pendaftaran/' . $typeSlug . '/pertanyaan/' . (int) $q['id'] . '/ubah') ?>" class="btn btn-soft btn-xs">
                                        <?= icon('edit', '', 13) ?>
                                        <span>Ubah</span>
                                    </a>
                                    <?php if ((int) ($q['is_system'] ?? 0) !== 1): ?>
                                        <form method="post" action="<?= url('/admin/pendaftaran/' . $typeSlug . '/pertanyaan/' . (int) $q['id'] . '/hapus') ?>" data-confirm="Hapus pertanyaan ini? Jawaban pada pendaftar lama tetap tersimpan.">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-soft-danger btn-xs">
                                                <?= icon('trash-2', '', 13) ?>
                                                <span>Hapus</span>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer" style="display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 12px; color: var(--admin-text-muted);">Menampilkan <?= $totalQuestions ?> pertanyaan formulir, termasuk field bawaan</span>
        </div>
    <?php endif; ?>
</div>
