<?php

declare(strict_types=1);

$photoUrl = url('/admin/berkas/' . (int) $application['id'] . '/foto');
$cvUrl = url('/admin/berkas/' . (int) $application['id'] . '/cv');
?>
<section class="admin-page-toolbar">
  <p>Data pribadi hanya dapat dilihat oleh admin yang sudah masuk.</p>
  <a class="button-secondary" href="<?= url('/admin/pendaftar') ?>">Kembali ke daftar</a>
</section>

<div class="application-detail-layout">
  <article class="card-surface application-detail-card">
    <header class="application-detail-heading">
      <div>
        <span class="status-pill application-status-<?= e(strtolower($application['status'])) ?>"><?= e($statuses[$application['status']] ?? $application['status']) ?></span>
        <h2><?= e($application['full_name']) ?></h2>
        <p><?= e($types[$application['batch_type']] ?? $application['batch_type']) ?> · <?= e($application['batch_name']) ?></p>
      </div>
      <img class="application-photo" src="<?= e($photoUrl) ?>" alt="Foto <?= e($application['full_name']) ?>" width="180" height="180">
    </header>

    <section class="application-detail-section">
      <h3>Data diri</h3>
      <dl class="application-detail-list">
        <dt>Email</dt><dd><a href="mailto:<?= e($application['email']) ?>"><?= e($application['email']) ?></a></dd>
        <dt>WhatsApp</dt><dd><?= e($application['whatsapp']) ?></dd>
        <dt>Tanggal lahir</dt><dd><?= e(date('d M Y', strtotime($application['birth_date']))) ?></dd>
        <dt>Domisili</dt><dd><?= e($application['domicile']) ?></dd>
        <dt>Status pekerjaan</dt><dd><?= e($occupations[$application['occupation_status']] ?? $application['occupation_status']) ?></dd>
        <dt>Universitas / instansi</dt><dd><?= e($application['university'] ?: '—') ?></dd>
        <dt>Terdaftar</dt><dd><?= e(date('d M Y H:i', strtotime($application['created_at']))) ?> WIT</dd>
      </dl>
    </section>

    <section class="application-detail-section">
      <h3>Ketersediaan dan minat</h3>
      <dl class="application-detail-list">
        <dt>Durasi</dt><dd><?= e($durations[$application['duration']] ?? $application['duration']) ?></dd>
        <dt>Bidang kontribusi</dt><dd><?= e(implode(', ', array_map(static fn (string $key): string => $interests[$key] ?? $key, $application['interest_areas']))) ?></dd>
        <dt>Sumber informasi</dt><dd><?= e($referralSources[$application['referral_source']] ?? $application['referral_source']) ?></dd>
        <dt>Motivasi</dt><dd class="application-long-text"><?= nl2br(e($application['motivation'])) ?></dd>
        <dt>Keahlian khusus</dt><dd class="application-long-text"><?= $application['special_skills'] ? nl2br(e($application['special_skills'])) : '—' ?></dd>
      </dl>
    </section>

    <section class="application-detail-section application-document-actions">
      <h3>Berkas</h3>
      <a class="button-secondary" href="<?= e($cvUrl) ?>" target="_blank" rel="noopener noreferrer">Lihat CV</a>
      <a class="button-secondary" href="<?= e($cvUrl) ?>?unduh=1">Download CV</a>
    </section>
  </article>

  <aside class="card-surface application-status-card">
    <h2>Perbarui status</h2>
    <p>Status ini digunakan untuk pengelolaan internal pendaftar.</p>
    <form action="<?= url('/admin/pendaftar/' . (int) $application['id'] . '/status') ?>" method="post">
      <?= csrf_field() ?>
      <label class="form-field"><span>Status pendaftaran</span>
        <select name="status" required>
          <?php foreach ($statuses as $key => $label): ?><option value="<?= e($key) ?>" <?= $application['status'] === $key ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?>
        </select>
      </label>
      <button class="button-primary" type="submit">Simpan status</button>
    </form>
  </aside>
</div>
