<nav class="navbar" id="navbar" aria-label="Navigasi utama">
    <div class="nav-inner">
        <a class="nav-brand" aria-label="Papua Future Project - Beranda" href="<?= url('/') ?>">
            <img class="nav-brand-logo" alt="Papua Future Project" data-inline-style="shared-1" src="<?= asset('img/logo.png') ?>"/>
        </a>

        <button class="nav-toggle" type="button" aria-label="Buka navigasi" aria-expanded="false" aria-controls="public-navigation" data-nav-toggle>
            <span></span><span></span><span></span>
        </button>

        <div class="nav-menu" id="public-navigation">
            <div class="nav-links">
                <a class="nav-link" href="<?= url('/') ?>">Beranda</a>
                <a class="nav-link" href="<?= url('/tentang') ?>">Tentang Kami</a>
                <a class="nav-link" href="<?= url('/program') ?>">Program</a>
                <a class="nav-link" href="<?= url('/berita') ?>">Berita</a>
                <a class="nav-link" href="<?= url('/kontak') ?>">Kontak</a>
            </div>
            <a class="nav-cta" href="<?= url('/bergabung') ?>">Jadi Relawan</a>
        </div>
    </div>
</nav>
