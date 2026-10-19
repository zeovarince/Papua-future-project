<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <style>
    @page { margin: 24px 28px 30px; }
    body { color: #1f2937; font-family: "DejaVu Sans", sans-serif; font-size: 9px; }
    h1 { margin: 0 0 5px; color: #17483e; font-size: 20px; }
    .report-meta { margin: 0 0 18px; color: #64748b; font-size: 9px; }
    .record { margin: 0 0 13px; padding: 12px; border: 1px solid #dbe4e2; border-radius: 6px; page-break-inside: avoid; }
    .record-top { width: 100%; margin-bottom: 8px; border-collapse: collapse; }
    .record-top td { padding: 0; vertical-align: top; }
    .record-top h2 { margin: 0 0 3px; color: #17483e; font-size: 13px; }
    .record-top .sub { color: #64748b; }
    .status { display: inline-block; padding: 4px 7px; border-radius: 10px; background: #eff6ff; color: #1d4ed8; font-size: 8px; font-weight: bold; }
    .details { width: 100%; border-collapse: collapse; }
    .details th, .details td { padding: 4px 6px; border: 1px solid #e5e7eb; text-align: left; vertical-align: top; }
    .details th { width: 22%; background: #f8fafc; color: #475569; font-weight: bold; }
    .long-text { margin-top: 7px; padding: 7px 9px; border-left: 2px solid #40b8fd; background: #f8fafc; line-height: 1.45; }
    .files { width: 100%; margin-top: 8px; border-collapse: collapse; }
    .files td { padding: 3px 5px 3px 0; vertical-align: middle; }
    .photo { width: 58px; height: 58px; border: 1px solid #e5e7eb; object-fit: cover; }
    a { color: #1d4ed8; text-decoration: underline; }
    .footer { margin-top: 18px; color: #94a3b8; font-size: 8px; text-align: center; }
  </style>
</head>
<body>
  <h1>Laporan Pendaftar Papua Future Project</h1>
  <p class="report-meta">Dibuat <?= e($generatedAt) ?> · <?= count($records) ?> pendaftar</p>

  <?php foreach ($records as $record): ?>
    <section class="record">
      <table class="record-top"><tr>
        <td>
          <h2><?= e($record['full_name']) ?></h2>
          <div class="sub"><?= e($record['type']) ?> · <?= e($record['batch_name']) ?> · <?= e($record['email']) ?></div>
        </td>
        <td style="width: 94px; text-align: right"><span class="status"><?= e($record['status']) ?></span></td>
      </tr></table>

      <table class="details">
        <tr><th>Tanggal lahir</th><td><?= e($record['birth_date']) ?></td><th>WhatsApp</th><td><?= e($record['whatsapp']) ?></td></tr>
        <tr><th>Domisili</th><td><?= e($record['domicile']) ?></td><th>Status</th><td><?= e($record['occupation']) ?></td></tr>
        <tr><th>Universitas / instansi</th><td><?= e($record['university'] !== '' ? $record['university'] : '—') ?></td><th>Durasi</th><td><?= e($record['duration']) ?></td></tr>
        <tr><th>Bidang kontribusi</th><td><?= e($record['interests']) ?></td><th>Sumber informasi</th><td><?= e($record['referral']) ?></td></tr>
        <tr><th>Tanggal daftar</th><td colspan="3"><?= e($record['created_at']) ?></td></tr>
      </table>

      <div class="long-text"><strong>Motivasi:</strong><br><?= nl2br(e($record['motivation'])) ?></div>
      <?php if ($record['special_skills'] !== ''): ?><div class="long-text"><strong>Keahlian khusus:</strong><br><?= nl2br(e($record['special_skills'])) ?></div><?php endif; ?>
      <?php foreach ($record['custom_answers'] as $answer): ?><div class="long-text"><strong><?= e($answer['question']) ?>:</strong><br><?= nl2br(e($answer['answer'])) ?></div><?php endforeach; ?>

      <table class="files"><tr>
        <td style="width: 68px"><?php if ($record['photo_data_uri'] !== null): ?><img class="photo" src="<?= e($record['photo_data_uri']) ?>" alt="Foto <?= e($record['full_name']) ?>"><?php else: ?><span>Foto tidak tersedia</span><?php endif; ?></td>
        <td><strong>Foto pendaftar</strong><br><?= e($record['full_name']) ?></td>
        <td style="width: 100px; text-align: right"><a href="<?= e($record['cv_url']) ?>">Lihat CV</a></td>
      </tr></table>
    </section>
  <?php endforeach; ?>

  <div class="footer">Papua Future Project · Dokumen internal</div>
</body>
</html>
