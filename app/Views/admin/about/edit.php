<section class="admin-page-intro">
    <p>Perbarui cerita, visi, dan misi yang tampil di halaman Tentang Kami.</p>
</section>
<form action="<?= url('/admin/tentang') ?>" method="post" class="admin-form card-surface">
    <?= csrf_field() ?>
    <label class="form-field form-field-wide">
        <span>Cerita dan latar belakang</span>
        <textarea name="content[story]" rows="12" required><?= e($contentData['story'] ?? '') ?></textarea>
        <?php if (isset($errors['story'])): ?><small class="field-error"><?= e($errors['story']) ?></small><?php endif; ?>
        <small>Pisahkan paragraf dengan satu baris kosong.</small>
    </label>
    <label class="form-field form-field-wide">
        <span>Visi</span>
        <textarea name="content[vision]" rows="3" required><?= e($contentData['vision'] ?? '') ?></textarea>
        <?php if (isset($errors['vision'])): ?><small class="field-error"><?= e($errors['vision']) ?></small><?php endif; ?>
    </label>
    <label class="form-field form-field-wide">
        <span>Misi</span>
        <textarea name="content[mission]" rows="7" required><?= e($contentData['mission'] ?? '') ?></textarea>
        <?php if (isset($errors['mission'])): ?><small class="field-error"><?= e($errors['mission']) ?></small><?php endif; ?>
        <small>Satu butir misi per baris.</small>
    </label>
    <div class="form-actions form-field-wide">
        <button class="button-primary" type="submit">Simpan perubahan</button>
        <a class="button-secondary" href="<?= url('/tentang') ?>" target="_blank" rel="noopener">Lihat halaman publik</a>
    </div>
</form>
