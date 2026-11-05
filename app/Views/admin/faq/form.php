<?php

declare(strict_types=1);

$id = (int) ($faq['id'] ?? 0);
$get = static fn(string $key, mixed $default = '') => $values[$key] ?? $default;
$title = $id > 0 ? 'Ubah FAQ' : 'Tambah FAQ';
$breadcrumb = ['Admin', 'Konten', 'FAQ', $id > 0 ? 'Ubah' : 'Tambah'];
$hasStickyActionBar = true;
?>

<div class="form-container-narrow">
    <div style="margin-bottom: 20px;">
        <p style="color: var(--admin-text-muted); font-size: 13px; margin: 0;">
            Kelola pertanyaan dan jawaban yang ditampilkan pada bagian FAQ di halaman Kontak.
        </p>
    </div>

    <form action="<?= url($id > 0 ? '/admin/faq/' . $id : '/admin/faq') ?>" method="post">
        <?= csrf_field() ?>

        <div class="card">
            <div class="card-header">
                <div>
                    <h2 class="card-title"><?= $id > 0 ? 'Ubah Pertanyaan FAQ' : 'Tambah Pertanyaan FAQ' ?></h2>
                    <p class="card-subtitle">Pertanyaan dan jawaban untuk pengunjung.</p>
                </div>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label" for="faqQuestion">Pertanyaan *</label>
                    <input type="text" id="faqQuestion" name="question" maxlength="300" required value="<?= e($get('question')) ?>" class="form-control" placeholder="Contoh: Bagaimana cara menjadi relawan di Papua Future Project?">
                    <?php if (isset($errors['question'])): ?>
                        <small class="field-error"><?= e($errors['question']) ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label class="form-label" for="faqAnswer">Jawaban *</label>
                    <textarea id="faqAnswer" name="answer" rows="6" required class="form-control" placeholder="Tuliskan jawaban lengkap yang informatif..."><?= e($get('answer')) ?></textarea>
                    <?php if (isset($errors['answer'])): ?>
                        <small class="field-error"><?= e($errors['answer']) ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label class="form-label" for="faqSort">Nomor Urut Tampil</label>
                    <input type="number" id="faqSort" name="sort_order" value="<?= e($get('sort_order', 0)) ?>" class="form-control" style="max-width: 160px;">
                    <small class="field-hint">Urutan terkecil tampil lebih atas pada akordeon Kontak.</small>
                </div>

                <div style="border-top: 1px solid var(--admin-border); padding-top: 14px;">
                    <label class="checkbox-field">
                        <input type="checkbox" name="is_published" value="1" <?= (int) $get('is_published', 1) === 1 ? 'checked' : '' ?>>
                        <span>Tampilkan di Halaman Kontak</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Sticky Action Bar -->
        <div class="sticky-action-bar">
            <a href="<?= url('/admin/faq') ?>" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">
                <?= icon('check', '', 16) ?>
                <span><?= $id > 0 ? 'Simpan Perubahan' : 'Simpan FAQ' ?></span>
            </button>
        </div>
    </form>
</div>
