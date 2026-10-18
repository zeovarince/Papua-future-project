<aside class="admin-sidebar" id="admin-sidebar" aria-label="Menu admin" data-admin-sidebar>
    <a class="admin-brand" href="<?= url('/admin') ?>">
        <span class="admin-brand-mark" aria-hidden="true">P</span>
        <span><strong>Papua Future Project</strong><small>Admin Panel</small></span>
    </a>
    <nav class="sidebar-nav">
        <a class="sidebar-link <?= ($page ?? '') === 'dashboard' ? 'is-active' : '' ?>" href="<?= url('/admin') ?>">Ringkasan</a>
        <p class="sidebar-label">Tentang &amp; Tim</p>
        <a class="sidebar-link <?= ($page ?? '') === 'about' ? 'is-active' : '' ?>" href="<?= url('/admin/tentang') ?>">Tentang Kami</a>
        <a class="sidebar-link <?= ($page ?? '') === 'team-penggerak' ? 'is-active' : '' ?>" href="<?= url('/admin/tim/penggerak') ?>">Tim Penggerak</a>
        <a class="sidebar-link <?= ($page ?? '') === 'team-internship' ? 'is-active' : '' ?>" href="<?= url('/admin/tim/internship') ?>">Internship</a>
        <a class="sidebar-link <?= ($page ?? '') === 'team-volunteer' ? 'is-active' : '' ?>" href="<?= url('/admin/tim/volunteer') ?>">Volunteer</a>
        <a class="sidebar-link <?= ($page ?? '') === 'batch' ? 'is-active' : '' ?>" href="<?= url('/admin/batch') ?>">Batch</a>
        <p class="sidebar-label">Konten</p>
        <span class="sidebar-link is-disabled" aria-disabled="true">Program <small>Segera</small></span>
        <span class="sidebar-link is-disabled" aria-disabled="true">Berita <small>Segera</small></span>
        <span class="sidebar-link is-disabled" aria-disabled="true">FAQ <small>Segera</small></span>
        <p class="sidebar-label">Data</p>
        <span class="sidebar-link is-disabled" aria-disabled="true">Pendaftar <small>Segera</small></span>
        <span class="sidebar-link is-disabled" aria-disabled="true">Pesan Masuk <small>Segera</small></span>
    </nav>
    <div class="sidebar-account">
        <span class="admin-avatar" aria-hidden="true"><?= e(mb_strtoupper(mb_substr((string) (($admin['name'] ?? 'A')), 0, 1))) ?></span>
        <span class="admin-account-name"><strong><?= e($admin['name'] ?? 'Administrator') ?></strong><small>Administrator</small></span>
        <form action="<?= url('/admin/logout') ?>" method="post" class="logout-form">
            <?= csrf_field() ?><button type="submit" class="logout-button" aria-label="Keluar" title="Keluar">Keluar</button>
        </form>
    </div>
</aside>
