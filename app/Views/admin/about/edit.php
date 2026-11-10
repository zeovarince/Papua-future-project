<?php

declare(strict_types=1);

$title = 'Tentang Kami';
$breadcrumb = ['Admin', 'Tentang Kami'];
$hasStickyActionBar = true;
?>

<div class="form-container-narrow">
    <div style="margin-bottom: 20px;">
        <p style="color: var(--admin-text-muted); font-size: 13px; margin: 0;">
            Perbarui cerita sejarah, visi, dan butir-butir misi yang ditampilkan di halaman publik Tentang Kami.
        </p>
    </div>

    <form action="<?= url('/admin/tentang') ?>" method="post">
        <?= csrf_field() ?>

        <div style="display: flex; flex-direction: column; gap: 16px;">
            <!-- Card 1: Cerita dan Latar Belakang -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Cerita &amp; Latar Belakang</h2>
                        <p class="card-subtitle">Kisah berdirinya Papua Future Project dan tenda belajar pertama.</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" for="storyField">Isi Cerita</label>
                        <textarea id="storyField" name="content[story]" rows="10" required class="form-control" style="min-height: 200px;"><?= e($contentData['story'] ?? '') ?></textarea>
                        <?php if (isset($errors['story'])): ?>
                            <small class="field-error"><?= e($errors['story']) ?></small>
                        <?php endif; ?>
                        <small class="field-hint">Pisahkan setiap paragraf dengan satu baris kosong. Penggunaan tanda <code>**tebal**</code> diperbolehkan.</small>
                    </div>
                </div>
            </div>

            <!-- Card 2: Visi Organisasi -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Visi Organisasi</h2>
                        <p class="card-subtitle">Pernyataan cita-cita jangka panjang lembaga.</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" for="visionField">Pernyataan Visi</label>
                        <textarea id="visionField" name="content[vision]" rows="3" required class="form-control" style="min-height: 90px;"><?= e($contentData['vision'] ?? '') ?></textarea>
                        <?php if (isset($errors['vision'])): ?>
                            <small class="field-error"><?= e($errors['vision']) ?></small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Card 3: Misi Organisasi -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Misi Organisasi</h2>
                        <p class="card-subtitle">Langkah-langkah strategis untuk mewujudkan visi.</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" for="missionField">Daftar Butir Misi</label>
                        <textarea id="missionField" name="content[mission]" rows="6" required class="form-control"><?= e($contentData['mission'] ?? '') ?></textarea>
                        <?php if (isset($errors['mission'])): ?>
                            <small class="field-error"><?= e($errors['mission']) ?></small>
                        <?php endif; ?>
                        <small class="field-hint">Tuliskan satu butir misi per baris.</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky Action Bar (DESIGN-ADMIN.md section 4.16) -->
        <div class="sticky-action-bar">
            <a href="<?= url('/tentang') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-secondary">
                <?= icon('external-link', '', 16) ?>
                <span>Lihat Halaman Publik</span>
            </a>
            <button type="submit" class="btn btn-primary">
                <?= icon('check', '', 16) ?>
                <span>Simpan Perubahan</span>
            </button>
        </div>
    </form>
</div>
