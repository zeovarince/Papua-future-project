<?php

declare(strict_types=1);

$categoryLabels = [
    'ACTIVITY' => 'Kegiatan Lapangan',
    'UPDATE' => 'Update Program',
    'MEDIA' => 'Liputan Media',
    'AWARD' => 'Penghargaan',
];
$badgeClasses = [
    'ACTIVITY' => 'badge-lapangan',
    'UPDATE' => 'badge-program',
    'MEDIA' => 'badge-media',
    'AWARD' => 'badge-award',
];
$dateLabel = static fn (array $post): string => !empty($post['published_at'])
    ? date('d M Y', strtotime((string) $post['published_at']))
    : '';
?>
<section class="hero-bg news-archive-hero-section h-[95vh] relative flex items-center justify-center text-center px-4 pt-20">
    <div class="max-w-3xl relative z-10 news-archive-hero">
        <p>Papua Future Project</p>
        <h1>Berita &amp; Aktivitas</h1>
        <p>Cerita terbaru dari lapangan, liputan media, dan pencapaian yang kami raih bersama.</p>
    </div>
    <div class="wave-divider" aria-hidden="true"><svg preserveAspectRatio="none" viewBox="0 0 1200 120" xmlns="http://www.w3.org/2000/svg"><path class="shape-fill" d="M321.39 56.44c58-10.79 114.16-30.13 172-41.86 82.39-16.72 168.19-17.73 250.45-.39C823.78 31 906.67 72 985.66 92.83c70.05 18.48 146.53 26.09 214.34 3V120H0V95.8C59.71 118.08 130.83 123.1 192.27 110.15 237.49 100.35 280.9 82.6 321.39 56.44Z"/></svg></div>
</section>

<?php if ($featured !== null): ?>
    <section class="pfp-section news-featured-section">
        <div class="pfp-container">
            <span class="pfp-tag">Terbaru</span>
            <h2 class="pfp-title">Berita &amp; aktivitas Papua Future Project.</h2>
            <div class="pfp-divider"></div>
            <a class="featured-card dynamic-featured-card" href="<?= url('/berita/' . rawurlencode($featured['slug'])) ?>">
                <div class="featured-photo dynamic-featured-photo<?= empty($featured['cover_image']) ? ' is-empty' : '' ?>">
                    <?php if (!empty($featured['cover_image'])): ?>
                        <img src="<?= e(url('/uploads/' . ltrim($featured['cover_image'], '/'))) ?>" alt="Dokumentasi <?= e($featured['title']) ?>" loading="lazy">
                    <?php endif; ?>
                    <span class="badge <?= e($badgeClasses[$featured['category']] ?? 'badge-lapangan') ?>"><?= e($categoryLabels[$featured['category']] ?? 'Berita') ?></span>
                </div>
                <div class="featured-body">
                    <span class="badge <?= e($badgeClasses[$featured['category']] ?? 'badge-lapangan') ?>"><?= e($categoryLabels[$featured['category']] ?? 'Berita') ?></span>
                    <h2 class="feat-title"><?= e($featured['title']) ?></h2>
                    <?php if (trim((string) ($featured['excerpt'] ?? '')) !== ''): ?><p class="feat-excerpt"><?= e($featured['excerpt']) ?></p><?php endif; ?>
                    <div class="feat-meta"><time class="feat-meta-item" datetime="<?= e(date('Y-m-d', strtotime((string) $featured['published_at']))) ?>"><?= e($dateLabel($featured)) ?></time></div>
                    <span class="feat-link">Baca selengkapnya</span>
                </div>
            </a>
        </div>
    </section>
<?php endif; ?>

<section class="pfp-section pfp-section-alt news-list-section">
    <div class="pfp-container">
        <span class="pfp-tag">Semua berita &amp; aktivitas</span>
        <h2 class="pfp-title">Perjalanan kami dari lapangan ke lapangan.</h2>
        <div class="pfp-divider"></div>
        <div class="news-grid">
            <?php foreach ($posts as $post): ?>
                <article class="news-card">
                    <a class="news-card-link" href="<?= url('/berita/' . rawurlencode($post['slug'])) ?>">
                        <div class="news-img news-img-placeholder dynamic-news-photo<?= empty($post['cover_image']) ? ' is-empty' : '' ?>">
                            <?php if (!empty($post['cover_image'])): ?>
                                <img src="<?= e(url('/uploads/' . ltrim($post['cover_image'], '/'))) ?>" alt="Dokumentasi <?= e($post['title']) ?>" loading="lazy">
                            <?php endif; ?>
                            <span class="badge <?= e($badgeClasses[$post['category']] ?? 'badge-lapangan') ?>"><?= e($categoryLabels[$post['category']] ?? 'Berita') ?></span>
                        </div>
                        <div class="news-body">
                            <time class="news-meta" datetime="<?= e(date('Y-m-d', strtotime((string) $post['published_at']))) ?>"><?= e($dateLabel($post)) ?></time>
                            <h3><?= e($post['title']) ?></h3>
                            <?php if (trim((string) ($post['excerpt'] ?? '')) !== ''): ?><p><?= e($post['excerpt']) ?></p><?php endif; ?>
                            <span class="news-link">Baca selengkapnya</span>
                        </div>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php if ($awards !== [] || $media !== []): ?>
<section class="pfp-section news-recognition-section">
    <div class="pfp-container">
        <span class="pfp-tag">Penghargaan dan liputan</span>
        <h2 class="pfp-title">Catatan apresiasi dan cerita di media.</h2>
        <div class="pfp-divider"></div>
        <div class="award-media-grid">
            <?php if ($awards !== []): ?><div class="recognition-column">
                <h3 class="news-recognition-title">Penghargaan</h3>
                <?php foreach ($awards as $award): ?>
                    <?php
                        $awardText = mb_strtolower((string) ($award['meta_label'] ?? '') . ' ' . (string) $award['title']);
                        $awardLogo = !empty($award['cover_image'])
                            ? url('/uploads/' . ltrim($award['cover_image'], '/'))
                            : (str_contains($awardText, 'unicef')
                                ? asset('img/partners/UNICEF_Logo.png')
                                : (str_contains($awardText, 'kemenkes')
                                    ? asset('img/partners/kemenkes.png')
                                    : (str_contains($awardText, 'astra') || str_contains($awardText, 'satu indonesia')
                                        ? asset('img/partners/id-astra-2.png')
                                        : null)));
                    ?>
                    <article class="award-item">
                        <?php if ($awardLogo !== null): ?><div class="award-logo">
                                <img src="<?= e($awardLogo) ?>" alt="Logo <?= e($award['meta_label'] ?: $award['title']) ?>" loading="lazy">
                        </div><?php endif; ?>
                        <div class="recognition-copy">
                            <h4><?= e($award['meta_label'] ?: $award['title']) ?></h4>
                            <?php if (trim((string) ($award['excerpt'] ?? '')) !== ''): ?><p><?= e($award['excerpt']) ?></p><?php endif; ?>
                            <a class="media-link" href="<?= url('/berita/' . rawurlencode($award['slug'])) ?>">Lihat detail</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div><?php endif; ?>

            <?php if ($media !== []): ?><div class="recognition-column">
                <h3 class="news-recognition-title">Liputan media</h3>
                <?php foreach ($media as $item): ?>
                    <article class="media-item">
                        <div class="media-header">
                            <?php if (!empty($item['cover_image']) || trim((string) ($item['source_name'] ?? '')) !== ''): ?><div class="media-source">
                                <?php if (!empty($item['cover_image'])): ?>
                                    <img src="<?= e(url('/uploads/' . ltrim($item['cover_image'], '/'))) ?>" alt="<?= e($item['source_name'] ?? '') ?>" loading="lazy">
                                <?php endif; ?>
                                <?php if (trim((string) ($item['source_name'] ?? '')) !== ''): ?><span class="media-name"><?= e($item['source_name']) ?></span><?php endif; ?>
                            </div><?php endif; ?>
                            <time class="media-date" datetime="<?= e(date('Y-m-d', strtotime((string) $item['published_at']))) ?>"><?= e($dateLabel($item)) ?></time>
                        </div>
                        <h4 class="media-title"><?= e($item['title']) ?></h4>
                        <?php if (trim((string) ($item['excerpt'] ?? '')) !== ''): ?><p><?= e($item['excerpt']) ?></p><?php endif; ?>
                        <?php if (!empty($item['source_url'])): ?>
                            <a class="media-link" href="<?= e($item['source_url']) ?>" target="_blank" rel="noopener noreferrer">Baca liputan</a>
                        <?php else: ?>
                            <a class="media-link" href="<?= url('/berita/' . rawurlencode($item['slug'])) ?>">Baca liputan</a>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div><?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>
