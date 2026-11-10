<?php
declare(strict_types=1);

$categoryLabels = [
    'ACTIVITY' => 'Kegiatan Lapangan',
    'UPDATE' => 'Update Program',
    'MEDIA' => 'Liputan Media',
    'AWARD' => 'Penghargaan',
];
$categoryBadge = [
    'ACTIVITY' => 'badge-lapangan',
    'UPDATE' => 'badge-program',
    'MEDIA' => 'badge-media',
    'AWARD' => 'badge-award',
];
$categoryLabel = $categoryLabels[$post['category']] ?? 'Berita';
$dateLabel = !empty($post['published_at']) ? date('d F Y', strtotime((string) $post['published_at'])) : '';
$paragraphs = preg_split('/\R\s*\R/', trim((string) ($post['content'] ?? ''))) ?: [];
?>
<section class="hero-mini news-detail-hero">
    <div class="news-detail-hero-copy">
        <h1><?= e($post['title']) ?></h1>
        <div class="news-detail-meta"><span><?= e($dateLabel) ?></span><span><?= e($post['author_name']) ?></span></div>
    </div>
</section>

<section class="pfp-section news-detail-content-section">
    <div class="pfp-container">
        <div class="artikel-grid">
            <article class="article-detail-body">
                <?php if (!empty($post['cover_image'])): ?>
                    <img class="article-detail-cover" src="<?= e(url('/uploads/' . ltrim($post['cover_image'], '/'))) ?>" alt="<?= e($post['title']) ?>" fetchpriority="high">
                <?php endif; ?>
                <?php if (trim((string) ($post['excerpt'] ?? '')) !== ''): ?><p class="article-detail-excerpt"><?= e($post['excerpt']) ?></p><?php endif; ?>
                <div class="article-detail-copy">
                    <?php foreach ($paragraphs as $paragraph): ?>
                        <?php if (trim($paragraph) !== ''): ?><p><?= nl2br(e(trim($paragraph))) ?></p><?php endif; ?>
                    <?php endforeach; ?>
                </div>

                <?php if (!empty($post['meta_label'])): ?><p class="article-detail-label"><strong><?= e($post['meta_label']) ?></strong></p><?php endif; ?>
                <?php if ($post['category'] === 'MEDIA' && !empty($post['source_url'])): ?>
                    <p><strong>Sumber:</strong> <a href="<?= e($post['source_url']) ?>" target="_blank" rel="noopener noreferrer"><?= e(trim((string) ($post['source_name'] ?? '')) !== '' ? $post['source_name'] : 'Baca sumber') ?></a></p>
                <?php endif; ?>

                <?php if ($photos !== []): ?>
                    <section class="article-detail-gallery" aria-labelledby="article-gallery-title">
                        <span class="pfp-tag">Dokumentasi</span>
                        <h2 id="article-gallery-title">Cerita dari lapangan.</h2>
                        <div class="article-photo-grid">
                            <?php foreach ($photos as $photo): ?>
                                <figure>
                                    <img src="<?= e(url('/uploads/' . ltrim($photo['file_path'], '/'))) ?>" alt="<?= e($photo['caption'] ?: 'Dokumentasi ' . $post['title']) ?>" loading="lazy">
                                    <?php if (!empty($photo['caption'])): ?><figcaption><?= e($photo['caption']) ?></figcaption><?php endif; ?>
                                </figure>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>

                <div class="article-share">
                    <span>Bagikan cerita ini</span>
                    <a href="https://wa.me/?text=<?= rawurlencode($post['title'] . ' ' . url('/berita/' . $post['slug'])) ?>" target="_blank" rel="noopener noreferrer" aria-label="Bagikan artikel melalui WhatsApp">Bagikan via WhatsApp</a>
                    <button type="button" data-action="copy-link">Salin tautan</button>
                    <span id="copy-toast" class="copy-toast" role="status" aria-live="polite">Tautan disalin.</span>
                </div>
            </article>

            <aside class="article-detail-sidebar" aria-label="Informasi artikel dan berita lainnya">
                <div class="sidebar-box sidebar-info">
                    <h2 class="sidebar-info-title">Informasi artikel</h2>
                    <span class="sidebar-label">Tanggal terbit</span><div class="sidebar-value"><?= e($dateLabel) ?></div>
                    <span class="sidebar-label">Kategori</span><div class="sidebar-value"><?= e($categoryLabel) ?></div>
                    <span class="sidebar-label">Ditulis oleh</span><div class="sidebar-value"><?= e($post['author_name']) ?></div>
                    <?php if (!empty($post['source_name'])): ?>
                        <span class="sidebar-label">Sumber</span><div class="sidebar-value"><?= e($post['source_name']) ?></div>
                    <?php endif; ?>
                </div>

                <?php if ($related !== []): ?>
                    <div class="sidebar-box sidebar-news">
                        <h2 class="sidebar-info-title">Berita lainnya</h2>
                        <?php foreach ($related as $item): ?>
                            <article class="sidebar-news-item">
                                <div class="sidebar-news-thumb">
                                    <?php if (!empty($item['cover_image'])): ?>
                                        <img src="<?= e(url('/uploads/' . ltrim($item['cover_image'], '/'))) ?>" alt="" loading="lazy">
                                    <?php else: ?>
                                        <span aria-hidden="true">PFP</span>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <a class="sidebar-news-title" href="<?= url('/berita/' . rawurlencode($item['slug'])) ?>"><?= e($item['title']) ?></a>
                                    <time class="sidebar-news-date" datetime="<?= e(date('Y-m-d', strtotime((string) $item['published_at']))) ?>"><?= e(date('d M Y', strtotime((string) $item['published_at']))) ?></time>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </aside>
        </div>
    </div>
</section>

<?php if ($related !== []): ?>
    <section class="pfp-section pfp-section-alt news-related-section">
        <div class="pfp-container">
            <span class="pfp-tag">Baca juga</span>
            <h2 class="pfp-title">Berita dan aktivitas lainnya.</h2>
            <div class="pfp-divider"></div>
            <div class="news-grid">
                <?php foreach ($related as $item): ?>
                    <article class="news-card">
                        <a class="news-card-link" href="<?= url('/berita/' . rawurlencode($item['slug'])) ?>">
                            <div class="news-img news-img-placeholder dynamic-news-photo<?= empty($item['cover_image']) ? ' is-empty' : '' ?>">
                                <?php if (!empty($item['cover_image'])): ?><img src="<?= e(url('/uploads/' . ltrim($item['cover_image'], '/'))) ?>" alt="" loading="lazy"><?php endif; ?>
                                <span class="badge <?= e($categoryBadge[$item['category']] ?? 'badge-lapangan') ?>"><?= e($categoryLabels[$item['category']] ?? 'Berita') ?></span>
                            </div>
                            <div class="news-body">
                                <time class="news-meta" datetime="<?= e(date('Y-m-d', strtotime((string) $item['published_at']))) ?>"><?= e(date('d M Y', strtotime((string) $item['published_at']))) ?></time>
                                <h3><?= e($item['title']) ?></h3>
                                <p><?= e($item['excerpt']) ?></p>
                                <span class="news-link">Baca selengkapnya <span aria-hidden="true">→</span></span>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<section class="pfp-section news-detail-return">
    <div class="pfp-container"><a class="news-link" href="<?= url('/berita') ?>"><span aria-hidden="true">←</span> Kembali ke berita &amp; kegiatan</a></div>
</section>
