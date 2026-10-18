<?php declare(strict_types=1); $infoRows = json_decode((string) ($program['info_rows'] ?? '[]'), true) ?: []; ?>
<section class="hero-bg relative flex min-h-[50vh] flex-col justify-center items-center text-center px-4 pt-20">
  <div class="max-w-3xl relative z-10"><p><?= e($program['category_label']) ?></p><h1><?= e($program['title']) ?></h1><p><?= e($program['tagline']) ?></p></div>
  <div class="wave-divider"><svg preserveAspectRatio="none" viewBox="0 0 1200 120" xmlns="http://www.w3.org/2000/svg"><path class="shape-fill" d="M321.39 56.44c58-10.79 114.16-30.13 172-41.86 82.39-16.72 168.19-17.73 250.45-.39C823.78 31 906.67 72 985.66 92.83c70.05 18.48 146.53 26.09 214.34 3V120H0V95.8C59.71 118.08 130.83 123.1 192.27 110.15 237.49 100.35 280.9 82.6 321.39 56.44Z"/></svg></div>
</section>
<section class="pfp-section"><div class="pfp-container program-detail-content">
  <?php if (!empty($program['cover_image'])): ?><img class="program-detail-cover" src="<?= e(url('/uploads/' . $program['cover_image'])) ?>" alt="Dokumentasi <?= e($program['title']) ?>"><?php endif; ?>
  <p class="pfp-body program-summary"><?= e($program['summary']) ?></p>
  <div class="program-copy"><?php foreach (preg_split('/\R\s*\R/', trim((string) $program['body'])) ?: [] as $paragraph): if (trim($paragraph) !== ''): ?><p><?= nl2br(e(trim($paragraph))) ?></p><?php endif; endforeach; ?></div>
  <?php if ($infoRows !== [] || !empty($program['stat1_value']) || !empty($program['stat2_value'])): ?><div class="program-facts"><?php foreach ($infoRows as $row): ?><p><strong><?= e($row['label'] ?? '') ?></strong> <?= e($row['value'] ?? '') ?></p><?php endforeach; ?><?php foreach (['1','2'] as $i): if (!empty($program['stat'.$i.'_value'])): ?><p><strong><?= e($program['stat'.$i.'_value']) ?></strong> <?= e($program['stat'.$i.'_label'] ?? '') ?></p><?php endif; endforeach; ?></div><?php endif; ?>
  <?php if (!empty($program['activities'])): ?><h2>Kegiatan</h2><ul><?php foreach (preg_split('/\R/', (string)$program['activities']) ?: [] as $activity): if(trim($activity)!==''): ?><li><?= e(trim($activity)) ?></li><?php endif; endforeach; ?></ul><?php endif; ?>
  <?php if (!empty($program['partners'])): ?><p><strong>Mitra:</strong> <?= e($program['partners']) ?></p><?php endif; ?>
  <?php if ($photos !== []): ?><section class="program-gallery"><h2>Dokumentasi kegiatan</h2><div class="program-gallery-grid"><?php foreach ($photos as $photo): ?><figure><img src="<?= e(url('/uploads/' . $photo['file_path'])) ?>" alt="<?= e($photo['caption'] ?: 'Dokumentasi ' . $program['title']) ?>" loading="lazy"><?php if (!empty($photo['caption'])): ?><figcaption><?= e($photo['caption']) ?></figcaption><?php endif; ?></figure><?php endforeach; ?></div></section><?php endif; ?>
  <a class="prog-link" href="<?= url('/program') ?>">← Kembali ke semua program</a>
</div></section>
