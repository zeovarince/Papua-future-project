<?php

declare(strict_types=1);

$isCore = ($group === 'penggerak');
$groupTitles = [
    'penggerak' => 'Core Team',
    'internship' => 'Internship',
    'volunteer' => 'Volunteer',
];
$title = $groupTitles[$group] ?? 'Tim';
$breadcrumb = ['Admin', 'Tim', $title];
$primaryAction = [
    'label' => 'Tambah Anggota',
    'url' => url('/admin/tim/' . $group . '/tambah'),
    'icon' => 'plus',
];

if (!$isCore) {
    $batchType = ($group === 'internship' ? 'INTERN' : 'VOLUNTEER');
    $batches = $batches ?? \App\Models\Batch::byType($batchType);
    $membersByBatch = [];
    foreach ($members as $m) {
        $bid = (int) ($m['batch_id'] ?? 0);
        $membersByBatch[$bid][] = $m;
    }
}
?>

<?php if ($isCore): ?>
    <!-- Core Team -->
    <div class="card">
        <div class="card-header">
            <div style="display: flex; align-items: center; gap: 10px;">
                <h2 class="card-title">Daftar Core Team</h2>
                <span class="badge-pill"><?= count($members) ?> anggota</span>
            </div>
            <div class="card-filter-bar">
                <div class="search-pill-wrap">
                    <span class="search-pill-icon"><?= icon('search', '', 14) ?></span>
                    <input type="search" class="search-pill-input" placeholder="Cari anggota..." aria-label="Cari anggota" data-search-target=".member-row">
                </div>
            </div>
        </div>

        <?php if ($members === []): ?>
            <?php
                $icon = 'user-check';
                $emptyTitle = 'Belum ada anggota Core Team';
                $description = 'Tambahkan profil anggota Core Team agar tampil di halaman Tentang Kami.';
                $actionUrl = url('/admin/tim/penggerak/tambah');
                $actionLabel = 'Tambah Anggota';
                require APP_ROOT . '/app/Views/partials/admin/empty-state.php';
            ?>
        <?php else: ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 56px;">Foto</th>
                            <th>Nama &amp; Peran</th>
                            <th>Jabatan</th>
                            <th>Urutan</th>
                            <th>Status</th>
                            <th style="text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($members as $member): ?>
                            <?php
                                $photoUrl = !empty($member['photo_path']) ? url('/uploads/' . $member['photo_path']) : null;
                                $isFounder = ((int) ($member['is_founder'] ?? 0) === 1);
                                $isPublished = ((int) ($member['is_published'] ?? 0) === 1);
                                $initial = mb_strtoupper(mb_substr($member['name'], 0, 1));
                            ?>
                            <tr class="member-row">
                                <td>
                                    <?php if ($photoUrl): ?>
                                        <img src="<?= e($photoUrl) ?>" alt="" class="user-avatar-circle" loading="lazy">
                                    <?php else: ?>
                                        <span class="user-avatar-circle"><?= e($initial) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <strong><?= e($member['name']) ?></strong>
                                        <?php if ($isFounder): ?>
                                            <span class="status-pill status-pill--new" style="font-size: 10px; padding: 2px 8px;">
                                                <?= icon('star', '', 12) ?> Founder
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <small style="color: var(--admin-text-muted); font-size: 11px;"><?= e($member['role']) ?></small>
                                </td>
                                <td><?= e($member['role']) ?></td>
                                <td>
                                    <span style="font-weight: 600; color: var(--admin-text-muted);"><?= (int) $member['sort_order'] ?></span>
                                </td>
                                <td>
                                    <?php $status = $isPublished ? 'published' : 'closed'; $label = $isPublished ? 'Tampil' : 'Sembunyi'; require APP_ROOT . '/app/Views/partials/admin/status-pill.php'; ?>
                                </td>
                                <td style="text-align: right;">
                                    <div class="table-actions" style="justify-content: flex-end;">
                                        <a href="<?= url('/admin/tim/penggerak/' . (int) $member['id'] . '/ubah') ?>" class="btn btn-soft btn-xs">
                                            <?= icon('edit', '', 13) ?>
                                            <span>Ubah</span>
                                        </a>
                                        <form method="post" action="<?= url('/admin/tim/penggerak/' . (int) $member['id'] . '/hapus') ?>" data-confirm="Hapus profil &quot;<?= e($member['name']) ?>&quot;?">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-soft-danger btn-xs">
                                                <?= icon('trash-2', '', 13) ?>
                                                <span>Hapus</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer" style="display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 12px; color: var(--admin-text-muted);">Menampilkan <?= count($members) ?> pengurus</span>
            </div>
        <?php endif; ?>
    </div>

<?php else: ?>
    <!-- Internship & Volunteer: Dikelompokkan per Batch (DESIGN-ADMIN.md section 5.4 & 5.5) -->
    <?php if ($batches === []): ?>
        <div class="card">
            <?php
                $typeName = ($group === 'internship' ? 'Intern' : 'Volunteer');
                $icon = ($group === 'internship' ? 'graduation-cap' : 'hand-heart');
                $emptyTitle = "Belum ada batch {$typeName}";
                $description = "Buat batch {$typeName} lebih dulu di halaman Batch untuk mulai menambahkan anggota.";
                $actionUrl = url('/admin/batch/tambah');
                $actionLabel = 'Buat Batch Baru';
                require APP_ROOT . '/app/Views/partials/admin/empty-state.php';
            ?>
        </div>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <?php foreach ($batches as $batch): ?>
                <?php
                    $bid = (int) $batch['id'];
                    $bMembers = $membersByBatch[$bid] ?? [];
                ?>
                <div class="card">
                    <div class="card-header" style="flex-wrap: wrap; gap: 12px;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <h2 class="card-title"><?= e($batch['name']) ?></h2>
                            <span class="badge-pill"><?= count($bMembers) ?> anggota</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px; margin-left: auto;">
                            <a href="<?= url('/admin/tim/' . $group . '/tambah?batch_id=' . $bid) ?>" class="btn btn-primary btn-sm">
                                <?= icon('plus', '', 16) ?>
                                <span>Tambah Anggota</span>
                            </a>
                        </div>
                    </div>

                    <?php if ($bMembers === []): ?>
                        <div class="empty-state empty-state-compact">
                            <p class="empty-state-desc">Belum ada anggota di batch ini.</p>
                            <a href="<?= url('/admin/tim/' . $group . '/tambah?batch_id=' . $bid) ?>" class="btn btn-soft btn-xs">
                                <?= icon('plus', '', 14) ?>
                                <span>Tambah Anggota Pertama</span>
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th style="width: 56px;">Foto</th>
                                        <th>Nama</th>
                                        <th>Universitas / Instansi</th>
                                        <th>Peran / Bidang</th>
                                        <th>Urutan</th>
                                        <th style="text-align: right;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($bMembers as $mem): ?>
                                        <?php
                                            $photoUrl = !empty($mem['photo_path']) ? url('/uploads/' . $mem['photo_path']) : null;
                                            $initial = mb_strtoupper(mb_substr($mem['name'], 0, 1));
                                        ?>
                                        <tr>
                                            <td>
                                                <?php if ($photoUrl): ?>
                                                    <img src="<?= e($photoUrl) ?>" alt="" class="user-avatar-circle" loading="lazy">
                                                <?php else: ?>
                                                    <span class="user-avatar-circle"><?= e($initial) ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?= e($mem['name']) ?></strong>
                                            </td>
                                            <td><?= e($mem['university'] ?: '—') ?></td>
                                            <td>
                                                <span class="badge-pill"><?= e($mem['role']) ?></span>
                                            </td>
                                            <td>
                                                <span style="font-weight: 600; color: var(--admin-text-muted);"><?= (int) $mem['sort_order'] ?></span>
                                            </td>
                                            <td style="text-align: right;">
                                                <div class="table-actions" style="justify-content: flex-end;">
                                                    <a href="<?= url('/admin/tim/' . $group . '/' . (int) $mem['id'] . '/ubah') ?>" class="btn btn-soft btn-xs">
                                                        <?= icon('edit', '', 13) ?>
                                                        <span>Ubah</span>
                                                    </a>
                                                    <form method="post" action="<?= url('/admin/tim/' . $group . '/' . (int) $mem['id'] . '/hapus') ?>" data-confirm="Hapus anggota &quot;<?= e($mem['name']) ?>&quot;?">
                                                        <?= csrf_field() ?>
                                                        <button type="submit" class="btn btn-soft-danger btn-xs">
                                                            <?= icon('trash-2', '', 13) ?>
                                                            <span>Hapus</span>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>
