<?php

declare(strict_types=1);

$questionId = (int) ($question['id'] ?? 0);
$get = static fn (string $key, mixed $default = '') => $values[$key] ?? $default;
$title = ($questionId === 0 ? 'Tambah Pertanyaan' : 'Ubah Pertanyaan') . ' (' . $typeLabel . ')';
$breadcrumb = ['Admin', 'Data', 'Form Pendaftaran', 'Pertanyaan ' . $typeLabel, $questionId === 0 ? 'Tambah' : 'Ubah'];
$hasStickyActionBar = true;
?>

<div class="form-container-narrow">
    <div style="margin-bottom: 20px;">
        <p style="color: var(--admin-text-muted); font-size: 13px; margin: 0;">
            Pertanyaan khusus yang hanya akan ditanyakan kepada pendaftar formulir <?= e($typeLabel) ?>.
        </p>
    </div>

    <form action="<?= url($questionId === 0 ? '/admin/pendaftaran/' . $typeSlug . '/pertanyaan' : '/admin/pendaftaran/' . $typeSlug . '/pertanyaan/' . $questionId) ?>" method="post">
        <?= csrf_field() ?>

        <div class="card">
            <div class="card-header">
                <div>
                    <h2 class="card-title"><?= $questionId === 0 ? 'Pertanyaan Baru' : 'Ubah Pertanyaan' ?></h2>
                    <p class="card-subtitle">Untuk pendaftar jenis <?= e($typeLabel) ?></p>
                </div>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label" for="qText">Teks Pertanyaan *</label>
                    <input type="text" id="qText" name="question" maxlength="250" required value="<?= e($get('question')) ?>" class="form-control" placeholder="Contoh: Ceritakan pengalaman relevan dalam kegiatan sosial...">
                    <?php if (isset($errors['question'])): ?>
                        <small class="field-error"><?= e($errors['question']) ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label class="form-label" for="qFieldType">Jenis Jawaban *</label>
                    <select id="qFieldType" name="field_type" required class="form-control" data-question-type-select>
                        <option value="TEXT" <?= $get('field_type', 'TEXTAREA') === 'TEXT' ? 'selected' : '' ?>>Jawaban Singkat (Satu Baris)</option>
                        <option value="TEXTAREA" <?= $get('field_type', 'TEXTAREA') === 'TEXTAREA' ? 'selected' : '' ?>>Paragraf (Teks Panjang)</option>
                        <option value="RADIO" <?= $get('field_type') === 'RADIO' ? 'selected' : '' ?>>Pilihan Ganda (Pilih 1)</option>
                        <option value="CHECKBOX" <?= $get('field_type') === 'CHECKBOX' ? 'selected' : '' ?>>Kotak Centang (Pilih Satu atau Lebih)</option>
                        <option value="SELECT" <?= $get('field_type') === 'SELECT' ? 'selected' : '' ?>>Dropdown (Menu Pilihan)</option>
                        <option value="FILE" <?= $get('field_type') === 'FILE' ? 'selected' : '' ?>>Unggah Berkas Tambahan (PDF/Gambar)</option>
                    </select>
                    <?php if (isset($errors['field_type'])): ?>
                        <small class="field-error"><?= e($errors['field_type']) ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group" id="optionsGroup" style="<?= in_array($get('field_type'), ['RADIO', 'CHECKBOX', 'SELECT'], true) ? '' : 'display: none;' ?>">
                    <label class="form-label" for="qOptions">Pilihan Jawaban (Satu baris per pilihan) *</label>
                    <textarea id="qOptions" name="options" rows="4" class="form-control" placeholder="Pilihan 1&#10;Pilihan 2&#10;Pilihan 3"><?= e($get('options')) ?></textarea>
                    <small class="field-hint">Tulis tiap opsi pilihan pada baris baru (tekan Enter untuk opsi berikutnya).</small>
                    <?php if (isset($errors['options'])): ?>
                        <small class="field-error"><?= e($errors['options']) ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label class="form-label" for="qSort">Nomor Urut Tampil</label>
                    <input type="number" id="qSort" name="sort_order" min="-10000" max="10000" value="<?= e($get('sort_order', 0)) ?>" class="form-control" style="max-width: 160px;">
                    <small class="field-hint">Urutan terkecil tampil lebih awal di bagian pertanyaan tambahan.</small>
                </div>

                <div style="display: flex; flex-direction: column; gap: 12px; border-top: 1px solid var(--admin-border); padding-top: 14px;">
                    <label class="checkbox-field">
                        <input type="checkbox" name="is_required" value="1" <?= (int) $get('is_required', 1) === 1 ? 'checked' : '' ?>>
                        <span>Wajib Dijawab oleh Pendaftar</span>
                    </label>
                    <label class="checkbox-field">
                        <input type="checkbox" name="is_active" value="1" <?= (int) $get('is_active', 1) === 1 ? 'checked' : '' ?>>
                        <span>Tampilkan pada Formulir Publik</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Sticky Action Bar -->
        <div class="sticky-action-bar">
            <a href="<?= url('/admin/pendaftaran/' . $typeSlug . '/pertanyaan') ?>" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">
                <?= icon('check', '', 16) ?>
                <span><?= $questionId === 0 ? 'Simpan Pertanyaan' : 'Simpan Perubahan' ?></span>
            </button>
        </div>
    </form>
</div>
