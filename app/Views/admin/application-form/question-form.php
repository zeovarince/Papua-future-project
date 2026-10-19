<?php $questionId = (int) ($question['id'] ?? 0); $get = static fn (string $key, mixed $default = '') => $values[$key] ?? $default; ?>
<form class="admin-form card-surface" action="<?= url($questionId === 0 ? '/admin/pendaftaran/' . $typeSlug . '/pertanyaan' : '/admin/pendaftaran/' . $typeSlug . '/pertanyaan/' . $questionId) ?>" method="post">
  <?= csrf_field() ?>
  <label class="form-field form-field-wide">
    <span>Teks pertanyaan untuk pendaftar <?= e($typeLabel) ?> *</span>
    <input name="question" maxlength="250" required value="<?= e($get('question')) ?>" placeholder="Contoh: Ceritakan pengalaman yang relevan dengan program ini.">
    <?php if (isset($errors['question'])): ?><small class="field-error"><?= e($errors['question']) ?></small><?php endif; ?>
  </label>
  <label class="form-field">
    <span>Jenis jawaban *</span>
    <select name="field_type" required>
      <option value="TEXT" <?= $get('field_type', 'TEXTAREA') === 'TEXT' ? 'selected' : '' ?>>Jawaban singkat</option>
      <option value="TEXTAREA" <?= $get('field_type', 'TEXTAREA') === 'TEXTAREA' ? 'selected' : '' ?>>Paragraf</option>
    </select>
    <?php if (isset($errors['field_type'])): ?><small class="field-error"><?= e($errors['field_type']) ?></small><?php endif; ?>
  </label>
  <label class="form-field">
    <span>Urutan tampil</span>
    <input type="number" name="sort_order" min="-10000" max="10000" value="<?= e($get('sort_order', 0)) ?>">
  </label>
  <label class="application-form-checkbox"><input type="checkbox" name="is_required" value="1" <?= (int) $get('is_required', 1) === 1 ? 'checked' : '' ?>> Wajib dijawab</label>
  <label class="application-form-checkbox"><input type="checkbox" name="is_active" value="1" <?= (int) $get('is_active', 1) === 1 ? 'checked' : '' ?>> Tampilkan pada formulir publik</label>
  <div class="form-actions form-field-wide">
    <button class="button-primary" type="submit">Simpan pertanyaan</button>
    <a class="button-secondary" href="<?= url('/admin/pendaftaran/' . $typeSlug . '/pertanyaan') ?>">Batal</a>
  </div>
</form>
