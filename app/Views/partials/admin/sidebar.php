<aside class="admin-sidebar" id="admin-sidebar" aria-label="Menu admin" data-admin-sidebar>
    <button class="sidebar-close" type="button" aria-label="Tutup menu" data-sidebar-close>&times;</button>
    <a class="admin-brand" href="<?= url('/admin') ?>" aria-label="Papua Future Project, Admin Panel">
        <img src="<?= asset('img/logo.png') ?>" class="admin-brand-mark" alt="" aria-hidden="true" width="50" height="50" />
        <span class="admin-brand-copy"><strong>Papua Future Project</strong><small>Admin Panel</small></span>
    </a>
    <nav class="sidebar-nav" aria-label="Navigasi admin">
        <a class="sidebar-link <?= ($page ?? '') === 'dashboard' ? 'is-active' : '' ?>" href="<?= url('/admin') ?>" aria-label="Ringkasan" title="Ringkasan"><span class="sidebar-link-icon" aria-hidden="true">&#9638;</span><span class="sidebar-link-label">Ringkasan</span></a>
        <p class="sidebar-label">Tentang &amp; Tim</p>
        <a class="sidebar-link <?= ($page ?? '') === 'about' ? 'is-active' : '' ?>" href="<?= url('/admin/tentang') ?>" aria-label="Tentang Kami" title="Tentang Kami"><span class="sidebar-link-icon" aria-hidden="true">&#9432;</span><span class="sidebar-link-label">Tentang Kami</span></a>
        <a class="sidebar-link <?= ($page ?? '') === 'team-penggerak' ? 'is-active' : '' ?>" href="<?= url('/admin/tim/penggerak') ?>" aria-label="Tim Penggerak" title="Tim Penggerak"><span class="sidebar-link-icon" aria-hidden="true">&#9822;</span><span class="sidebar-link-label">Tim Penggerak</span></a>
        <a class="sidebar-link <?= ($page ?? '') === 'team-internship' ? 'is-active' : '' ?>" href="<?= url('/admin/tim/internship') ?>" aria-label="Internship" title="Internship"><span class="sidebar-link-icon" aria-hidden="true">&#9733;</span><span class="sidebar-link-label">Internship</span></a>
        <a class="sidebar-link <?= ($page ?? '') === 'team-volunteer' ? 'is-active' : '' ?>" href="<?= url('/admin/tim/volunteer') ?>" aria-label="Volunteer" title="Volunteer"><span class="sidebar-link-icon" aria-hidden="true">&#9829;</span><span class="sidebar-link-label">Volunteer</span></a>
        <a class="sidebar-link <?= ($page ?? '') === 'batch' ? 'is-active' : '' ?>" href="<?= url('/admin/batch') ?>" aria-label="Batch" title="Batch"><span class="sidebar-link-icon" aria-hidden="true">&#9635;</span><span class="sidebar-link-label">Batch</span></a>
        <p class="sidebar-label">Konten</p>
        <a class="sidebar-link <?= ($page ?? '') === 'program' ? 'is-active' : '' ?>" href="<?= url('/admin/program') ?>" aria-label="Program" title="Program"><span class="sidebar-link-icon" aria-hidden="true">&#9776;</span><span class="sidebar-link-label">Program</span></a>
        <a class="sidebar-link <?= ($page ?? '') === 'post' ? 'is-active' : '' ?>" href="<?= url('/admin/berita') ?>" aria-label="Berita dan Kegiatan" title="Berita &amp; Kegiatan"><span class="sidebar-link-icon" aria-hidden="true">&#9641;</span><span class="sidebar-link-label">Berita &amp; Kegiatan</span></a>
        <a class="sidebar-link <?= ($page ?? '') === 'faq' ? 'is-active' : '' ?>" href="<?= url('/admin/faq') ?>" aria-label="FAQ" title="FAQ"><span class="sidebar-link-icon" aria-hidden="true">?</span><span class="sidebar-link-label">FAQ</span></a>
        <p class="sidebar-label">Data</p>
        <a class="sidebar-link <?= ($page ?? '') === 'application' ? 'is-active' : '' ?>" href="<?= url('/admin/pendaftar') ?>" aria-label="Pendaftar" title="Pendaftar"><span class="sidebar-link-icon" aria-hidden="true">&#9776;</span><span class="sidebar-link-label">Pendaftar</span></a>
        <a class="sidebar-link <?= ($page ?? '') === 'message' ? 'is-active' : '' ?>" href="<?= url('/admin/pesan') ?>" aria-label="Pesan Masuk" title="Pesan Masuk"><span class="sidebar-link-icon" aria-hidden="true">&#9993;</span><span class="sidebar-link-label">Pesan Masuk</span></a>
    </nav>
    <div class="sidebar-account">
        <span class="admin-avatar" aria-hidden="true"><?= e(mb_strtoupper(mb_substr((string) (($admin['name'] ?? 'A')), 0, 1))) ?></span>
        <span class="admin-account-name"><strong><?= e($admin['name'] ?? 'Administrator') ?></strong><small>Administrator</small></span>
        <form action="<?= url('/admin/logout') ?>" method="post" class="logout-form">
            <?= csrf_field() ?><button type="submit" class="logout-button" aria-label="Keluar" title="Keluar"><span aria-hidden="true">&#8618;</span><span class="logout-label">Keluar</span></button>
        </form>
    </div>
</aside>
