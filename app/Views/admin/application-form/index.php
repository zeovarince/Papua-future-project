<section class="admin-page-toolbar">
  <p>Aktifkan Internship dan Relawan secara terpisah. Formulir hanya menerima pendaftaran jika jenisnya aktif dan ada batch yang dibuka.</p>
  <a class="button-secondary" href="<?= url('/admin/batch') ?>">Kelola batch</a>
</section>

<div class="application-form-settings-grid">
  <?php foreach ($settings as $setting): ?>
    <?php
      $type = $setting['application_type'];
      $isIntern = $type === 'INTERN';
      $label = $isIntern ? 'Internship' : 'Relawan';
      $slug = $isIntern ? 'internship' : 'relawan';
      $isActive = (int) $setting['is_active'] === 1;
    ?>
    <article class="card-surface application-form-setting-card">
      <div class="application-form-setting-heading">
        <div>
          <span class="status-pill <?= $isActive ? 'status-open' : 'status-closed' ?>"><?= $isActive ? 'Formulir aktif' : 'Formulir nonaktif' ?></span>
          <h2><?= e($label) ?></h2>
        </div>
        <span class="application-form-setting-icon" aria-hidden="true"><?= $isIntern ? '🎓' : '🤝' ?></span>
      </div>
      <p><?= (int) $setting['open_batch_count'] ?> batch dengan toggle batch dibuka · <?= (int) $setting['active_question_count'] ?> pertanyaan tambahan aktif</p>
      <div class="application-form-setting-actions">
        <a class="button-secondary" href="<?= url('/admin/pendaftaran/' . $slug . '/pertanyaan') ?>">Atur pertanyaan</a>
        <form method="post" action="<?= url('/admin/pendaftaran/' . $slug . '/toggle') ?>" data-confirm="<?= $isActive ? 'Nonaktifkan semua pendaftaran ' . e($label) . '?' : 'Aktifkan formulir pendaftaran ' . e($label) . '?' ?>">
          <?= csrf_field() ?>
          <button class="<?= $isActive ? 'button-secondary' : 'button-primary' ?>" type="submit"><?= $isActive ? 'Nonaktifkan' : 'Aktifkan' ?> formulir</button>
        </form>
      </div>
    </article>
  <?php endforeach; ?>
</div>

<p class="application-form-admin-note">Menonaktifkan formulir tidak mengubah status batch dan tidak menghapus data pendaftar atau jawaban lama. Buka/tutup batch tertentu melalui halaman Batch.</p>
