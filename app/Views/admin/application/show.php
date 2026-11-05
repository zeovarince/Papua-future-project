<?php

declare(strict_types=1);

$title = $application['full_name'];
$subtitle = ($types[$application['batch_type']] ?? $application['batch_type']) . ' · ' . $application['batch_name'];
$breadcrumb = [
    'Admin',
    ['label' => 'Pendaftar', 'url' => url('/admin/pendaftar')],
    $application['full_name'],
];

$photoUrl = url('/admin/berkas/' . (int) $application['id'] . '/foto');
$cvUrl = url('/admin/berkas/' . (int) $application['id'] . '/cv');
?>

<div class="grid-12">
    <!-- Main Column (8 of 12 cols): Detailed Information -->
    <div class="col-8">
        <!-- 1. Data Diri -->
        <article class="card">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Data Pribadi</h2>
                    <p class="card-subtitle">Informasi identitas dan kontak pendaftar.</p>
                </div>
            </div>
            <div class="card-body">
                <dl class="detail-list">
                    <dt>Nama Lengkap</dt>
                    <dd><strong><?= e($application['full_name']) ?></strong></dd>

                    <dt>Email</dt>
                    <dd>
                        <a href="mailto:<?= e($application['email']) ?>" style="color: var(--admin-primary); font-weight: 500;">
                            <?= e($application['email']) ?>
                        </a>
                    </dd>

                    <dt>Nomor WhatsApp</dt>
                    <dd>
                        <?php
                            $cleanWa = preg_replace('/\D+/', '', (string) $application['whatsapp']);
                            if (str_starts_with($cleanWa, '0')) $cleanWa = '62' . substr($cleanWa, 1);
                        ?>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span><?= e($application['whatsapp']) ?></span>
                            <?php if ($cleanWa !== ''): ?>
                                <a href="https://wa.me/<?= e($cleanWa) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-soft-success btn-xs">
                                    Hubungi WA
                                </a>
                            <?php endif; ?>
                        </div>
                    </dd>

                    <dt>Tanggal Lahir</dt>
                    <dd><?= date('d M Y', strtotime($application['birth_date'])) ?></dd>

                    <dt>Domisili Saat Ini</dt>
                    <dd><?= e($application['domicile']) ?></dd>

                    <dt>Status Pekerjaan</dt>
                    <dd><?= e($occupations[$application['occupation_status']] ?? $application['occupation_status']) ?></dd>

                    <dt>Universitas / Instansi</dt>
                    <dd><?= e($application['university'] ?: '—') ?></dd>

                    <dt>Waktu Pendaftaran</dt>
                    <dd><?= date('d M Y H:i', strtotime($application['created_at'])) ?> WIT</dd>
                </dl>
            </div>
        </article>

        <!-- 2. Ketersediaan & Motivasi -->
        <article class="card">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Ketersediaan &amp; Motivasi</h2>
                    <p class="card-subtitle">Minat kontribusi dan alasan mendaftar.</p>
                </div>
            </div>
            <div class="card-body">
                <dl class="detail-list">
                    <dt>Durasi Penugasan</dt>
                    <dd><?= e($durations[$application['duration']] ?? $application['duration']) ?></dd>

                    <dt>Bidang Kontribusi</dt>
                    <dd>
                        <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                            <?php foreach ($application['interest_areas'] as $intKey): ?>
                                <span class="badge-pill"><?= e($interests[$intKey] ?? $intKey) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </dd>

                    <dt>Sumber Informasi</dt>
                    <dd><?= e($referralSources[$application['referral_source']] ?? $application['referral_source']) ?></dd>

                    <dt>Motivasi Bergabung</dt>
                    <dd>
                        <div class="detail-long-text">
                            <?= nl2br(e($application['motivation'])) ?>
                        </div>
                    </dd>

                    <dt>Keahlian Khusus</dt>
                    <dd>
                        <?php if (!empty($application['special_skills'])): ?>
                            <div class="detail-long-text">
                                <?= nl2br(e($application['special_skills'])) ?>
                            </div>
                        <?php else: ?>
                            <span style="color: var(--admin-text-muted);">—</span>
                        <?php endif; ?>
                    </dd>
                </dl>
            </div>
        </article>

        <!-- 3. Jawaban Pertanyaan Tambahan (jika ada) -->
        <?php if (!empty($application['custom_answers'])): ?>
            <article class="card">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Jawaban Pertanyaan Tambahan</h2>
                        <p class="card-subtitle">Pertanyaan khusus yang diisi pada formulir.</p>
                    </div>
                </div>
                <div class="card-body">
                    <dl class="detail-list">
                        <?php foreach ($application['custom_answers'] as $ans): ?>
                            <dt><?= e($ans['question']) ?></dt>
                            <dd>
                                <div class="detail-long-text">
                                    <?php if (str_starts_with($ans['answer'], 'http://') || str_starts_with($ans['answer'], 'https://')): ?>
                                        <a href="<?= e($ans['answer']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-soft btn-xs" style="margin-top: 4px;">
                                            <?= icon('external-link', '', 14) ?>
                                            <span>Buka Tautan Berkas</span>
                                        </a>
                                    <?php else: ?>
                                        <?= nl2br(e($ans['answer'])) ?>
                                    <?php endif; ?>
                                </div>
                            </dd>
                        <?php endforeach; ?>
                    </dl>
                </div>
            </article>
        <?php endif; ?>
    </div>

    <!-- Side Column (4 of 12 cols): Status, Foto, Berkas CV -->
    <div class="col-4">
        <!-- Status Pendaftaran & Aksi -->
        <article class="card">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Status Pendaftaran</h2>
                </div>
            </div>
            <div class="card-body">
                <div style="margin-bottom: 16px;">
                    <span style="font-size: 12px; color: var(--admin-text-muted); display: block; margin-bottom: 6px;">Status saat ini:</span>
                    <?php $status = $application['status']; require APP_ROOT . '/app/Views/partials/admin/status-pill.php'; ?>
                </div>

                <form action="<?= url('/admin/pendaftar/' . (int) $application['id'] . '/status') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label class="form-label" for="statusSelect">Ubah Status</label>
                        <select id="statusSelect" name="status" required class="form-control">
                            <?php foreach ($statuses as $key => $label): ?>
                                <option value="<?= e($key) ?>" <?= $application['status'] === $key ? 'selected' : '' ?>>
                                    <?= e($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm" style="width: 100%;">
                        <?= icon('check', '', 15) ?>
                        <span>Simpan Status</span>
                    </button>
                </form>
            </div>
        </article>

        <!-- Foto Pendaftar -->
        <article class="card">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Foto Diri</h2>
                </div>
            </div>
            <div class="card-body" style="text-align: center;">
                <img src="<?= e($photoUrl) ?>" alt="Foto <?= e($application['full_name']) ?>" class="applicant-photo-preview" loading="lazy">
                <div style="margin-top: 12px;">
                    <a href="<?= e($photoUrl) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-xs">
                        <?= icon('external-link', '', 13) ?>
                        <span>Buka Ukuran Penuh</span>
                    </a>
                </div>
            </div>
        </article>

        <!-- Berkas CV -->
        <article class="card">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Berkas Curriculum Vitae</h2>
                </div>
            </div>
            <div class="card-body">
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <a href="<?= e($cvUrl) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-sm" style="justify-content: center;">
                        <?= icon('file-text', '', 16) ?>
                        <span>Lihat CV (Tab Baru)</span>
                    </a>
                    <a href="<?= e($cvUrl) ?>?unduh=1" class="btn btn-primary btn-sm" style="justify-content: center;">
                        <?= icon('download', '', 16) ?>
                        <span>Download Dokumen CV</span>
                    </a>
                </div>
            </div>
        </article>

        <a href="<?= url('/admin/pendaftar') ?>" class="btn btn-ghost btn-sm" style="justify-content: center;">
            <?= icon('arrow-left', '', 15) ?>
            <span>Kembali ke Daftar Pendaftar</span>
        </a>
    </div>
</div>
