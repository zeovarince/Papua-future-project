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
<?php
$galleryPhotos = [
    ['order' => 1, 'src' => 'https://res.cloudinary.com/crvzukvh/image/upload/f_auto,q_auto/tentangkami1', 'caption' => 'Belajar bersama anak-anak', 'ratio' => 'landscape'],
    ['order' => 2, 'src' => 'https://res.cloudinary.com/crvzukvh/image/upload/f_auto,q_auto/tentangkami2', 'caption' => 'Kegiatan relawan di lapangan', 'ratio' => 'portrait'],
    ['order' => 3, 'src' => 'https://res.cloudinary.com/crvzukvh/image/upload/f_auto,q_auto/tentangkami3', 'caption' => 'Aksi lingkungan di lapangan', 'ratio' => 'landscape'],
    ['order' => 4, 'src' => 'https://res.cloudinary.com/crvzukvh/image/upload/f_auto,q_auto/tentangkami4', 'caption' => 'Relawan bersama anak-anak', 'ratio' => 'portrait'],
    ['order' => 5, 'src' => 'https://res.cloudinary.com/crvzukvh/image/upload/f_auto,q_auto/tentangkami5', 'caption' => 'Kegiatan belajar di kampung', 'ratio' => 'wide'],
    ['order' => 6, 'src' => 'https://res.cloudinary.com/crvzukvh/image/upload/f_auto,q_auto/tentangkami6', 'caption' => 'Nonton bersama anak-anak', 'ratio' => 'square'],
    ['order' => 7, 'src' => 'https://res.cloudinary.com/crvzukvh/image/upload/f_auto,q_auto/tenatangkami7', 'caption' => 'Kebersamaan para relawan', 'ratio' => 'landscape'],
    ['order' => 8, 'src' => 'https://res.cloudinary.com/crvzukvh/image/upload/f_auto,q_auto/tentangkami8', 'caption' => 'Membaca bersama anak-anak', 'ratio' => 'portrait-short'],
    ['order' => 9, 'src' => 'https://res.cloudinary.com/crvzukvh/image/upload/f_auto,q_auto/tentangkami9', 'caption' => 'Foto bersama di pesisir', 'ratio' => 'landscape'],
];
$galleryColumns = [
    [$galleryPhotos[0], $galleryPhotos[3], $galleryPhotos[6]],
    [$galleryPhotos[1], $galleryPhotos[4], $galleryPhotos[7]],
    [$galleryPhotos[2], $galleryPhotos[5], $galleryPhotos[8]],
];
?>
<!-- Sub B: Masonry Foto Grid -->
<div class="masonry-grid">
    <?php foreach ($galleryColumns as $column): ?>
    <div class="masonry-column">
        <?php foreach ($column as $photo): ?>
        <div class="masonry-item" data-order="<?= (int) $photo['order'] ?>" style="--gallery-order: <?= (int) $photo['order'] ?>" data-caption="<?= e($photo['caption']) ?>" data-src="<?= e($photo['src']) ?>">
            <div class="masonry-photo-frame masonry-photo-frame--<?= e($photo['ratio']) ?>">
                <img class="masonry-photo" src="<?= e($photo['src']) ?>" alt="<?= e($photo['caption']) ?>" loading="lazy" decoding="async" />
            </div>
            <div class="masonry-overlay" aria-hidden="true">
                <div class="masonry-overlay-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
                </div>
            </div>
            <div class="masonry-caption"><?= e($photo['caption']) ?></div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endforeach; ?>
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
