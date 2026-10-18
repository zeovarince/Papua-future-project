<?php

declare(strict_types=1);

$exportQuery = http_build_query(array_filter([
    'tipe' => $filters['type'],
    'batch' => $filters['batch_id'],
    'status' => $filters['status'],
    'q' => $filters['q'],
], static fn (mixed $value): bool => $value !== '' && $value !== null));
$queryForPage = static function (int $page) use ($exportQuery): string {
    parse_str($exportQuery, $query);
    $query['page'] = $page;
    return http_build_query($query);
};
?>
<section class="admin-page-toolbar application-page-toolbar">
  <p>Tinjau data pendaftaran, perbarui status, atau unduh laporan sesuai filter aktif.</p>
  <div class="application-export-actions" aria-label="Ekspor data pendaftar">
    <a class="button-secondary" href="<?= url('/admin/pendaftar/ekspor/csv' . ($exportQuery === '' ? '' : '?' . $exportQuery)) ?>">Unduh CSV</a>
    <a class="button-secondary" href="<?= url('/admin/pendaftar/ekspor/xlsx' . ($exportQuery === '' ? '' : '?' . $exportQuery)) ?>">Unduh Excel</a>
    <a class="button-primary button-link" href="<?= url('/admin/pendaftar/ekspor/pdf' . ($exportQuery === '' ? '' : '?' . $exportQuery)) ?>">Unduh PDF</a>
  </div>
</section>

<form class="card-surface application-filter-form" action="<?= url('/admin/pendaftar') ?>" method="get">
  <label class="form-field"><span>Tipe</span>
    <select name="tipe"><option value="">Semua tipe</option>
      <?php foreach ($types as $key => $label): ?><option value="<?= e($key) ?>" <?= $filters['type'] === $key ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?>
    </select>
  </label>
  <label class="form-field"><span>Batch</span>
    <select name="batch"><option value="">Semua batch</option>
      <?php foreach ($batches as $batch): ?><option value="<?= (int) $batch['id'] ?>" <?= (int) ($filters['batch_id'] ?? 0) === (int) $batch['id'] ? 'selected' : '' ?>><?= e($batch['name']) ?> · <?= e($types[$batch['batch_type']] ?? $batch['batch_type']) ?></option><?php endforeach; ?>
    </select>
  </label>
  <label class="form-field"><span>Status</span>
    <select name="status"><option value="">Semua status</option>
      <?php foreach ($statuses as $key => $label): ?><option value="<?= e($key) ?>" <?= $filters['status'] === $key ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?>
    </select>
  </label>
  <label class="form-field application-search-field"><span>Cari nama</span>
    <input type="search" name="q" maxlength="100" value="<?= e($filters['q']) ?>" placeholder="Nama pendaftar">
  </label>
  <div class="application-filter-actions"><button class="button-primary" type="submit">Terapkan filter</button><a class="button-secondary" href="<?= url('/admin/pendaftar') ?>">Reset</a></div>
</form>

<section class="application-list-summary" aria-live="polite">
  <p><strong><?= number_format($pagination['total'], 0, ',', '.') ?></strong> pendaftar ditemukan.</p>
  <?php if ($pagination['total'] > 0): ?><small>Halaman <?= (int) $pagination['page'] ?> dari <?= (int) $pagination['last_page'] ?></small><?php endif; ?>
</section>

<?php if ($applications === []): ?>
  <section class="card-surface empty-state"><h2>Belum ada pendaftar yang cocok</h2><p>Ubah filter atau tunggu pendaftaran baru masuk.</p></section>
<?php else: ?>
  <div class="card-surface admin-table-wrap">
    <table class="admin-table application-table">
      <thead><tr><th>Nama</th><th>Batch</th><th>Tipe</th><th>Status</th><th>Tanggal daftar</th><th>Aksi</th></tr></thead>
      <tbody>
      <?php foreach ($applications as $application): ?>
        <tr>
          <td><strong><?= e($application['full_name']) ?></strong><small><?= e($application['email']) ?></small></td>
          <td><?= e($application['batch_name']) ?></td>
          <td><?= e($types[$application['batch_type']] ?? $application['batch_type']) ?></td>
          <td><span class="status-pill application-status-<?= e(strtolower($application['status'])) ?>"><?= e($statuses[$application['status']] ?? $application['status']) ?></span></td>
          <td><?= e(date('d M Y H:i', strtotime($application['created_at']))) ?> WIT</td>
          <td><a class="button-secondary application-view-link" href="<?= url('/admin/pendaftar/' . (int) $application['id']) ?>">Lihat detail</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <?php if ($pagination['last_page'] > 1): ?>
    <nav class="application-pagination" aria-label="Navigasi halaman pendaftar">
      <?php if ($pagination['page'] > 1): ?><a class="button-secondary" href="<?= url('/admin/pendaftar?' . e($queryForPage($pagination['page'] - 1))) ?>">Sebelumnya</a><?php else: ?><span></span><?php endif; ?>
      <span>Halaman <?= (int) $pagination['page'] ?> / <?= (int) $pagination['last_page'] ?></span>
      <?php if ($pagination['page'] < $pagination['last_page']): ?><a class="button-secondary" href="<?= url('/admin/pendaftar?' . e($queryForPage($pagination['page'] + 1))) ?>">Berikutnya</a><?php else: ?><span></span><?php endif; ?>
    </nav>
  <?php endif; ?>
<?php endif; ?>
