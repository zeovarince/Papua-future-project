<section class="admin-page-toolbar">
    <p>Buat batch untuk mengelompokkan intern dan relawan, lalu atur status pendaftarannya.</p>
    <a class="button-primary button-link" href="<?= url('/admin/batch/tambah') ?>">+ Tambah batch</a>
</section>
<?php if ($batches === []): ?>
    <section class="card-surface empty-state"><h2>Belum ada batch</h2><p>Tambahkan batch Intern atau Volunteer untuk mulai mengelola anggota dan pendaftaran.</p></section>
<?php else: ?>
    <div class="card-surface table-scroll">
        <table class="admin-table">
            <thead><tr><th>Batch</th><th>Tipe</th><th>Anggota</th><th>Pendaftar</th><th>Pendaftaran</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($batches as $batch): ?>
                <tr>
                    <td><strong><?= e($batch['name']) ?></strong><small>Dibuat <?= e(date('d M Y', strtotime($batch['created_at']))) ?></small></td>
                    <td><?= $batch['batch_type'] === 'INTERN' ? 'Intern' : 'Volunteer' ?></td>
                    <td><?= (int) $batch['member_count'] ?></td>
                    <td><?= (int) $batch['application_count'] ?></td>
                    <td><span class="status-pill <?= (int) $batch['is_open'] === 1 ? 'status-open' : 'status-closed' ?>"><?= (int) $batch['is_open'] === 1 ? 'Dibuka' : 'Ditutup' ?></span></td>
                    <td class="table-actions">
                        <form method="post" action="<?= url('/admin/batch/' . (int) $batch['id'] . '/toggle') ?>">
                            <?= csrf_field() ?><button type="submit"><?= (int) $batch['is_open'] === 1 ? 'Tutup' : 'Buka' ?> pendaftaran</button>
                        </form>
                        <a href="<?= url('/admin/batch/' . (int) $batch['id'] . '/ubah') ?>">Ubah</a>
                        <form method="post" action="<?= url('/admin/batch/' . (int) $batch['id'] . '/hapus') ?>" data-confirm="Hapus batch ini?">
                            <?= csrf_field() ?><button class="text-danger" type="submit">Hapus</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
