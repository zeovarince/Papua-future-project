<?php
declare(strict_types=1);

$infoRows = json_decode((string) ($program['info_rows'] ?? '[]'), true);
$infoRows = is_array($infoRows) ? $infoRows : [];
$activities = preg_split('/\R+/', trim((string) ($program['activities'] ?? ''))) ?: [];
$activities = array_values(array_filter(array_map('trim', $activities), static fn (string $item): bool => $item !== ''));
$partners = preg_split('/[,;\r\n]+/', trim((string) ($program['partners'] ?? ''))) ?: [];
$partners = array_values(array_filter(array_map('trim', $partners), static fn (string $item): bool => $item !== ''));
$gallery = $photos;
if ($gallery === [] && !empty($program['cover_image'])) {
    $gallery[] = ['file_path' => $program['cover_image'], 'caption' => ''];
}
?>
<section class="hero-mini program-detail-hero">
    <div class="program-detail-hero-copy">
        <h1><?= e($program['title']) ?></h1>
        <?php if (trim((string) ($program['tagline'] ?? '')) !== ''): ?><p><?= e($program['tagline']) ?></p><?php endif; ?>
    </div>
</section>

<section class="pfp-section program-detail-main">
    <div class="pfp-container">
        <div class="content-grid">
            <article class="detail-body">
                <span class="pfp-tag">Tentang program</span>
                <h2 class="pfp-title"><?= e($program['title']) ?></h2>
                <div class="pfp-divider"></div>
                <?php if (!empty($program['cover_image'])): ?>
                    <img class="program-detail-cover" src="<?= e(url('/uploads/' . ltrim($program['cover_image'], '/'))) ?>" alt="Dokumentasi program <?= e($program['title']) ?>" fetchpriority="high">
                <?php endif; ?>
                <?php if (!empty($program['summary'])): ?><p class="program-detail-summary"><?= e($program['summary']) ?></p><?php endif; ?>
                <?php foreach (preg_split('/\R\s*\R/', trim((string) ($program['body'] ?? ''))) ?: [] as $paragraph): ?>
                    <?php if (trim($paragraph) !== ''): ?><p><?= nl2br(e(trim($paragraph))) ?></p><?php endif; ?>
                <?php endforeach; ?>

                <?php if ($activities !== []): ?>
                    <hr class="section-sep">
                    <span class="pfp-tag">Kegiatan dalam program</span>
                    <h3 class="program-detail-subtitle">Apa yang kami lakukan</h3>
                    <ul class="kegiatan-list">
                        <?php foreach ($activities as $activity): ?>
                            <li><span class="kegiatan-dot" aria-hidden="true"></span><span><?= e($activity) ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <?php if ($infoRows !== []): ?>
                    <hr class="section-sep">
                    <span class="pfp-tag">Informasi program</span>
                    <div class="program-detail-facts">
                        <?php foreach ($infoRows as $row): ?>
                            <?php if (is_array($row) && trim((string) ($row['value'] ?? '')) !== ''): ?>
                                <div><?php if (trim((string) ($row['label'] ?? '')) !== ''): ?><span><?= e($row['label']) ?></span><?php endif; ?><?php if (trim((string) ($row['value'] ?? '')) !== ''): ?><strong><?= e($row['value']) ?></strong><?php endif; ?></div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if ($partners !== []): ?>
                    <hr class="section-sep">
                    <span class="pfp-tag">Mitra kolaborasi</span>
                    <h3 class="program-detail-subtitle">Bersama mereka program ini berjalan</h3>
                    <ul class="mitra-list">
                        <?php foreach ($partners as $partner): ?>
                            <li class="mitra-item"><span class="mitra-icon" aria-hidden="true">✦</span><?= e($partner) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </article>

            <aside class="sidebar-sticky" aria-label="Informasi tambahan program">
                <div class="sidebar-info">
                    <h2 class="sidebar-info-title">Informasi program</h2>
                    <div class="sidebar-info-row"><span class="sidebar-info-icon" aria-hidden="true">⌖</span><div><span class="sidebar-info-label">Wilayah kegiatan</span><span class="sidebar-info-value">Kabupaten Manokwari, Papua Barat</span></div></div>
                    <div class="sidebar-info-row"><span class="sidebar-info-icon" aria-hidden="true">▧</span><div><span class="sidebar-info-label">Bidang</span><span class="sidebar-info-value"><?= e($program['category_label']) ?></span></div></div>
                    <?php if (!empty($program['stat1_value'])): ?>
                        <div class="sidebar-info-row"><span class="sidebar-info-icon" aria-hidden="true">◉</span><div><?php if (trim((string) ($program['stat1_label'] ?? '')) !== ''): ?><span class="sidebar-info-label"><?= e($program['stat1_label']) ?></span><?php endif; ?><span class="sidebar-info-value"><?= e($program['stat1_value']) ?></span></div></div>
                    <?php endif; ?>
                    <?php if (!empty($program['stat2_value'])): ?>
                        <div class="sidebar-info-row"><span class="sidebar-info-icon" aria-hidden="true">⌂</span><div><?php if (trim((string) ($program['stat2_label'] ?? '')) !== ''): ?><span class="sidebar-info-label"><?= e($program['stat2_label']) ?></span><?php endif; ?><span class="sidebar-info-value"><?= e($program['stat2_value']) ?></span></div></div>
                    <?php endif; ?>
                </div>

                <a class="btn-relawan" href="<?= url('/bergabung') ?>">Bergabung bersama kami</a>

                <?php if ($otherPrograms !== []): ?>
                    <div class="sidebar-programs">
                        <h2 class="sidebar-programs-title">Program lainnya</h2>
                        <?php foreach ($otherPrograms as $other): ?>
                            <a class="sidebar-prog-link" href="<?= url('/program/' . rawurlencode($other['slug'])) ?>"><span aria-hidden="true">→</span><?= e($other['title']) ?></a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </aside>
        </div>
    </div>
</section>

<?php if ($gallery !== []): ?>
    <section class="pfp-section pfp-section-alt program-detail-gallery-section">
        <div class="pfp-container">
            <div class="program-gallery-heading">
                <span class="pfp-tag">Dokumentasi</span>
                <h2 class="pfp-title">Kegiatan program di lapangan.</h2>
                <div class="pfp-divider"></div>
            </div>
            <div class="masonry-grid">
                <?php foreach ($gallery as $index => $photo): ?>
                    <figure class="masonry-item">
                        <div class="program-masonry-media program-masonry-media-<?= $index % 4 ?>">
                            <img src="<?= e(url('/uploads/' . ltrim($photo['file_path'], '/'))) ?>" alt="<?= e($photo['caption'] ?: 'Dokumentasi ' . $program['title']) ?>" loading="lazy">
                        </div>
                        <span class="masonry-overlay" aria-hidden="true"><span class="masonry-overlay-icon">⌕</span></span>
                        <?php if (trim((string) ($photo['caption'] ?? '')) !== ''): ?><figcaption class="masonry-caption"><?= e($photo['caption']) ?></figcaption><?php endif; ?>
                    </figure>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<section class="pfp-section program-detail-return">
    <div class="pfp-container"><a class="prog-link" href="<?= url('/program') ?>"><span aria-hidden="true">←</span> Kembali ke semua program</a></div>
</section>
