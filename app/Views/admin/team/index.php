<section class="admin-page-toolbar">
    <p>Kelola profil anggota. Urutan terkecil ditampilkan lebih dahulu.</p>
    <a class="button-primary button-link" href="<?= url('/admin/tim/' . $group . '/tambah') ?>">+ Tambah anggota</a>
</section>
<?php if ($group !== 'penggerak' && $members === []): ?>
    <section class="card-surface empty-state">
        <h2>Belum ada anggota</h2>
        <p><?= $group === 'internship' ? 'Buat batch Intern terlebih dahulu di halaman Batch, lalu tambahkan anggota.' : 'Buat batch Relawan terlebih dahulu di halaman Batch, lalu tambahkan anggota.' ?></p>
        <a class="button-secondary button-link" href="<?= url('/admin/batch') ?>">Kelola batch</a>
    </section>
<?php elseif ($members === []): ?>
    <section class="card-surface empty-state"><h2>Belum ada Tim Penggerak</h2><p>Tambahkan profil inti lembaga agar tampil di halaman Tentang Kami.</p></section>
<?php else: ?>
    <div class="card-surface table-scroll">
        <table class="admin-table">
            <thead><tr><th>Anggota</th><th>Kelompok / Batch</th><th>Urutan</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($members as $member): ?>
                <tr>
                    <td>
                        <strong><?= e($member['name']) ?><?= (int) $member['is_founder'] === 1 ? ' ★ Founder' : '' ?></strong>
                        <small><?= e($member['role']) ?><?= !empty($member['university']) ? ' · ' . e($member['university']) : '' ?></small>
                    </td>
                    <td><?= e($member['batch_name'] ?? 'Tim Penggerak') ?></td>
                    <td><?= (int) $member['sort_order'] ?></td>
                    <td><span class="status-pill <?= (int) $member['is_published'] === 1 ? 'status-open' : 'status-closed' ?>"><?= (int) $member['is_published'] === 1 ? 'Tampil' : 'Disembunyikan' ?></span></td>
                    <td class="table-actions">
                        <a href="<?= url('/admin/tim/' . $group . '/' . (int) $member['id'] . '/ubah') ?>">Ubah</a>
                        <form method="post" action="<?= url('/admin/tim/' . $group . '/' . (int) $member['id'] . '/hapus') ?>" data-confirm="Hapus profil ini?">
                            <?= csrf_field() ?><button class="text-danger" type="submit">Hapus</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
