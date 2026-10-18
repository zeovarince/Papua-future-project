<section class="pfp-section" id="sejarah">
<div class="pfp-container">
<!-- Sub A: Narasi + Timeline -->
<div class="history-grid">
<!-- Kolom Kiri: Narasi -->
<div>
<span class="pfp-tag">Sejarah &amp; Latar Belakang</span>
<h2 class="pfp-title">Dari satu tenda belajar di Mansinam, semua ini dimulai.</h2>
<div class="pfp-divider"></div>
<?php foreach (preg_split('/\R\s*\R/', trim((string) ($about['story'] ?? ''))) ?: [] as $paragraph): ?>
<?php if (trim($paragraph) !== ''): ?><p class="pfp-body"><?= nl2br(e(trim($paragraph))) ?></p><?php endif; ?>
<?php endforeach; ?>
</div>
<!-- Kolom Kanan: Timeline -->
<div>
<span class="pfp-tag" data-inline-style="about-16">Perjalanan kami</span>
<div class="timeline">
<div class="tl-item">
<div class="tl-dot"></div>
<div class="tl-year">2020</div>
<div class="tl-text">Papua Future Project berdiri.<br/>Kegiatan perdana di Pulau Mansinam, Papua Barat.</div>
</div>
<div class="tl-item">
<div class="tl-dot"></div>
<div class="tl-year">2021</div>
<div class="tl-text">Program Bimbingan Belajar aktif berjalan.<br/>Relawan pertama mulai berdatangan dari berbagai kota.</div>
</div>
<div class="tl-item">
<div class="tl-dot"></div>
<div class="tl-year">2022</div>
<div class="tl-text">SATU Indonesia Awards — Bidang Pendidikan (Astra).<br/>Diakui sebagai Mitra NGO Potensial UNICEF &amp; Kemenkes RI.</div>
</div>
<div class="tl-item">
<div class="tl-dot"></div>
<div class="tl-year">2023</div>
<div class="tl-text">Berdiri resmi 18 Agustus 2023.<br/>250+ relawan aktif. 13 kampung binaan di Papua Barat.</div>
</div>
</div>
</div>
</div>
<!-- Sub B: Masonry Foto Grid -->
<div class="masonry-grid">
<!-- Foto 1: 4:3 -->
<div class="masonry-item" data-caption="Bimbingan belajar Mansinam" data-src="<?= asset('img/about/profilsingkat.png') ?>">
<div data-inline-style="about-17">
<img alt="Bimbingan belajar Mansinam" data-inline-style="about-18" src="<?= asset('img/about/profilsingkat.png') ?>"/>
</div>
<div class="masonry-overlay">
<div class="masonry-overlay-icon">
<svg data-inline-style="about-19" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
</div>
</div>
<div class="masonry-caption">Bimbingan belajar Mansinam</div>
</div>
<!-- Foto 2: 3:4 -->
<div class="masonry-item" data-caption="Relawan mengajar" data-src="<?= asset('img/about/profilsingkat.png') ?>">
<div data-inline-style="about-20">
<img alt="Relawan mengajar" data-inline-style="about-21" src="<?= asset('img/about/profilsingkat.png') ?>"/>
</div>
<div class="masonry-overlay">
<div class="masonry-overlay-icon">
<svg data-inline-style="about-22" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
</div>
</div>
<div class="masonry-caption">Relawan mengajar</div>
</div>
<!-- Foto 3: 1:1 -->
<div class="masonry-item" data-caption="Pojok baca" data-src="<?= asset('img/about/profilsingkat.png') ?>">
<div data-inline-style="about-23">
<img alt="Pojok baca" data-inline-style="about-24" src="<?= asset('img/about/profilsingkat.png') ?>"/>
</div>
<div class="masonry-overlay">
<div class="masonry-overlay-icon">
<svg data-inline-style="about-25" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
</div>
</div>
<div class="masonry-caption">Pojok baca</div>
</div>
<!-- Foto 4: 1:1 -->
<div class="masonry-item" data-caption="Imunisasi bersama UNICEF" data-src="<?= asset('img/about/profilsingkat.png') ?>">
<div data-inline-style="about-26">
<img alt="Imunisasi bersama UNICEF" data-inline-style="about-27" src="<?= asset('img/hero/herobanner.png') ?>"/>
</div>
<div class="masonry-overlay">
<div class="masonry-overlay-icon">
<svg data-inline-style="about-28" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
</div>
</div>
<div class="masonry-caption">Imunisasi bersama UNICEF</div>
</div>
<!-- Foto 5: 16:9 -->
<div class="masonry-item" data-caption="Bersama warga kampung" data-src="<?= asset('img/about/profilsingkat.png') ?>">
<div data-inline-style="about-29">
<img alt="Bersama warga kampung" data-inline-style="about-30" src="<?= asset('img/about/profilsingkat.png') ?>"/>
</div>
<div class="masonry-overlay">
<div class="masonry-overlay-icon">
<svg data-inline-style="about-31" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
</div>
</div>
<div class="masonry-caption">Bersama warga kampung</div>
</div>
<!-- Foto 6: 3:4 -->
<div class="masonry-item" data-caption="Pelatihan pemuda lokal" data-src="<?= asset('img/about/profilsingkat.png') ?>">
<div data-inline-style="about-32">
<img alt="Pelatihan pemuda lokal" data-inline-style="about-33" src="<?= asset('img/hero/herobanner.png') ?>"/>
</div>
<div class="masonry-overlay">
<div class="masonry-overlay-icon">
<svg data-inline-style="about-34" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
</div>
</div>
<div class="masonry-caption">Pelatihan pemuda lokal</div>
</div>
<!-- Foto 7: 4:3 -->
<div class="masonry-item" data-caption="Kegiatan lapangan" data-src="<?= asset('img/about/profilsingkat.png') ?>">
<div data-inline-style="about-35">
<img alt="Kegiatan lapangan" data-inline-style="about-36" src="<?= asset('img/about/profilsingkat.png') ?>"/>
</div>
<div class="masonry-overlay">
<div class="masonry-overlay-icon">
<svg data-inline-style="about-37" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
</div>
</div>
<div class="masonry-caption">Kegiatan lapangan</div>
</div>
<!-- Foto 8: 1:1 -->
<div class="masonry-item" data-caption="Foto bersama relawan" data-src="<?= asset('img/about/profilsingkat.png') ?>">
<div data-inline-style="about-38">
<img alt="Foto bersama relawan" data-inline-style="about-39" src="<?= asset('img/about/profilsingkat.png') ?>"/>
</div>
<div class="masonry-overlay">
<div class="masonry-overlay-icon">
<svg data-inline-style="about-40" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
</div>
</div>
<div class="masonry-caption">Foto bersama relawan</div>
</div>
<!-- Foto 9: 3:4 -->
<div class="masonry-item" data-caption="Kegiatan Papua Future Project" data-src="<?= asset('img/about/profilsingkat.png') ?>">
<div data-inline-style="about-41">
<img alt="Kegiatan Papua Future Project" data-inline-style="about-42" src="<?= asset('img/about/profilsingkat.png') ?>"/>
</div>
<div class="masonry-overlay">
<div class="masonry-overlay-icon">
<svg data-inline-style="about-43" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
</div>
</div>
<div class="masonry-caption">Kegiatan Papua Future Project</div>
</div>
</div><!-- /masonry-grid -->
<!-- LIGHTBOX -->
<div data-inline-style="about-44" id="lightbox">
<button aria-label="Tutup" data-inline-style="about-45" id="lb-close">
<svg data-inline-style="about-46" fill="none" stroke="white" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
</button>
<div data-inline-style="about-47">
<img alt="" data-inline-style="about-48" id="lb-img" src=""/>
<p data-inline-style="about-49" id="lb-caption"></p>
</div>
<!-- Nav panah kiri -->
<button aria-label="Sebelumnya" data-inline-style="about-50" id="lb-prev">
<svg data-inline-style="about-51" fill="none" stroke="white" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M15 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
</button>
<!-- Nav panah kanan -->
<button aria-label="Berikutnya" data-inline-style="about-52" id="lb-next">
<svg data-inline-style="about-53" fill="none" stroke="white" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M9 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
</button>
<!-- Counter -->
<div data-inline-style="about-54" id="lb-counter"></div>
</div>
</div>
</section>
