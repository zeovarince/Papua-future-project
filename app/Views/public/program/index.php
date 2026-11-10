<?php
declare(strict_types=1);

$programRows = array_chunk($programs, 3);
$programStats = [
    ['value' => '700+', 'label' => 'Anak terjangkau', 'sub' => 'di Papua Barat'],
    ['value' => '13', 'label' => 'Kampung binaan', 'sub' => 'Mansinam dan sekitarnya'],
    ['value' => '250+', 'label' => 'Relawan aktif', 'sub' => 'dari seluruh Indonesia'],
    ['value' => '2020', 'label' => 'Mulai bergerak', 'sub' => 'Every Child Matters'],
];
?>
<section class="hero-bg program-archive-hero-section h-[95vh] relative flex flex-col justify-center items-center text-center px-4 pt-20">
    <div class="max-w-3xl relative z-10 program-archive-hero">
        <p>Papua Future Project</p>
        <h1>Program Kami</h1>
        <p><?= count($programs) ?> program yang lahir dari kebutuhan nyata anak-anak dan komunitas di Papua Barat.</p>
    </div>
    <div class="wave-divider" aria-hidden="true"><svg preserveAspectRatio="none" viewBox="0 0 1200 120" xmlns="http://www.w3.org/2000/svg"><path class="shape-fill" d="M321.39 56.44c58-10.79 114.16-30.13 172-41.86 82.39-16.72 168.19-17.73 250.45-.39C823.78 31 906.67 72 985.66 92.83c70.05 18.48 146.53 26.09 214.34 3V120H0V95.8C59.71 118.08 130.83 123.1 192.27 110.15 237.49 100.35 280.9 82.6 321.39 56.44Z"/></svg></div>
</section>

<section class="pfp-section program-impact-section" aria-label="Dampak program Papua Future Project">
    <div class="pfp-container">
        <div class="stats-grid">
            <?php foreach ($programStats as $stat): ?>
                <div class="stat-item">
                    <div class="stat-num"><?= e($stat['value']) ?></div>
                    <div class="stat-label"><?= e($stat['label']) ?></div>
                    <div class="stat-sub"><?= e($stat['sub']) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="pfp-section program-archive-list">
    <div class="pfp-container">
        <span class="pfp-tag">Yang kami lakukan</span>
        <h2 class="pfp-title">Program yang dirancang bersama, bukan untuk kampung.</h2>
        <div class="pfp-divider"></div>
        <p class="pfp-body program-archive-intro">Setiap program berangkat dari kebutuhan nyata yang didengar langsung dari anak-anak dan komunitas.</p>

<?php foreach ($programRows as $row): ?>
            <div class="<?= count($row) === 3 ? 'prog-grid-full' : 'prog-row-2' ?>">
                <?php foreach ($row as $program): ?>
                    <article class="prog-card">
                        <a class="program-card-link" href="<?= url('/program/' . rawurlencode($program['slug'])) ?>" aria-label="Lihat detail <?= e($program['title']) ?>">
                            <div class="prog-img program-photo<?= empty($program['cover_image']) ? ' is-empty' : '' ?>">
                                <?php if (!empty($program['cover_image'])): ?>
                                    <img src="<?= e(url('/uploads/' . ltrim($program['cover_image'], '/'))) ?>" alt="Dokumentasi <?= e($program['title']) ?>" loading="lazy">
                                <?php endif; ?>
                                <span class="prog-badge"><?= e($program['category_label']) ?></span>
                            </div>
                            <div class="prog-body">
                                <h3><?= e($program['title']) ?></h3>
                                <?php if (trim((string) ($program['summary'] ?? '')) !== ''): ?><p><?= e($program['summary']) ?></p><?php endif; ?>
                                <?php if (!empty($program['stat1_value']) || !empty($program['stat2_value'])): ?>
                                    <div class="stat-chips">
                                        <?php foreach (['1', '2'] as $statIndex): ?>
                                            <?php if (!empty($program['stat' . $statIndex . '_value'])): ?>
                                                <span class="stat-chip"><?= e($program['stat' . $statIndex . '_value']) ?><?php if (trim((string) ($program['stat' . $statIndex . '_label'] ?? '')) !== ''): ?> <?= e($program['stat' . $statIndex . '_label']) ?><?php endif; ?></span>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                <hr class="prog-divider">
                                <span class="prog-link">Lihat detail program <span aria-hidden="true">→</span></span>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>

    </div>
</section>

<?php if ($programPhotos !== []): ?>
    <section class="pfp-section pfp-section-alt program-archive-gallery">
        <div class="pfp-container">
            <div class="program-gallery-heading">
                <span class="pfp-tag">Dokumentasi</span>
                <h2 class="pfp-title">Kegiatan program kami di lapangan.</h2>
                <div class="pfp-divider"></div>
            </div>
            <div class="masonry-grid">
                <?php foreach ($programPhotos as $index => $photo): ?>
                    <a class="masonry-item" href="<?= url('/program/' . rawurlencode($photo['program_slug'])) ?>" aria-label="Lihat program <?= e($photo['program_title']) ?>">
                        <div class="program-masonry-media program-masonry-media-<?= $index % 4 ?>">
                            <img src="<?= e(url('/uploads/' . ltrim($photo['file_path'], '/'))) ?>" alt="<?= e($photo['caption'] ?: 'Dokumentasi ' . $photo['program_title']) ?>" loading="lazy">
                        </div>
                        <span class="masonry-overlay" aria-hidden="true"><span class="masonry-overlay-icon">⌕</span></span>
                        <?php if (trim((string) ($photo['caption'] ?? '')) !== ''): ?><span class="masonry-caption"><?= e($photo['caption']) ?></span><?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>
