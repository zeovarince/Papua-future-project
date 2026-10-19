<section class="admin-page-toolbar">
  <p>Pertanyaan ini hanya muncul untuk pendaftar <?= e($typeLabel) ?>. Kamu bisa mengatur teks, jenis jawaban, kewajiban, status tampil, dan urutannya.</p>
  <a class="button-primary button-link" href="<?= url('/admin/pendaftaran/' . $typeSlug . '/pertanyaan/tambah') ?>">+ Tambah pertanyaan</a>
</section>

<section class="application-form-type-switch" aria-label="Pilih jenis formulir">
  <a class="<?= $type === 'INTERN' ? 'is-current' : '' ?>" href="<?= url('/admin/pendaftaran/internship/pertanyaan') ?>">Internship</a>
  <a class="<?= $type === 'VOLUNTEER' ? 'is-current' : '' ?>" href="<?= url('/admin/pendaftaran/relawan/pertanyaan') ?>">Relawan</a>
</section>

<?php if ($questions === []): ?>
  <section class="card-surface empty-state"><h2>Belum ada pertanyaan tambahan</h2><p>Formulir <?= e($typeLabel) ?> tetap memiliki pertanyaan data diri dan berkas yang standar. Tambahkan pertanyaan khusus bila diperlukan.</p><a class="button-secondary" href="<?= url('/admin/pendaftaran/' . $typeSlug . '/pertanyaan/tambah') ?>">Tambah pertanyaan pertama</a></section>
<?php else: ?>
  <div class="card-surface table-scroll">
    <table class="admin-table">
      <thead><tr><th>Pertanyaan</th><th>Jenis jawaban</th><th>Kewajiban</th><th>Status</th><th>Urutan</th><th>Aksi</th></tr></thead>
      <tbody>
      <?php foreach ($questions as $question): ?>
        <tr>
          <td><strong><?= e($question['question']) ?></strong></td>
          <td><?= $question['field_type'] === 'TEXT' ? 'Jawaban singkat' : 'Paragraf' ?></td>
          <td><?= (int) $question['is_required'] === 1 ? 'Wajib' : 'Opsional' ?></td>
          <td><span class="status-pill <?= (int) $question['is_active'] === 1 ? 'status-open' : 'status-closed' ?>"><?= (int) $question['is_active'] === 1 ? 'Tampil' : 'Disembunyikan' ?></span></td>
          <td><?= (int) $question['sort_order'] ?></td>
          <td class="table-actions">
            <a href="<?= url('/admin/pendaftaran/' . $typeSlug . '/pertanyaan/' . (int) $question['id'] . '/ubah') ?>">Ubah</a>
            <form method="post" action="<?= url('/admin/pendaftaran/' . $typeSlug . '/pertanyaan/' . (int) $question['id'] . '/hapus') ?>" data-confirm="Hapus pertanyaan ini? Jawaban pendaftar lama tetap tersimpan.">
              <?= csrf_field() ?><button class="text-danger" type="submit">Hapus</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<p class="application-form-admin-note"><a href="<?= url('/admin/pendaftaran') ?>">Kembali ke status formulir</a>. Pertanyaan yang dinonaktifkan tidak ditampilkan pada formulir baru. Jawaban yang sudah masuk tetap dapat dilihat admin.</p>
