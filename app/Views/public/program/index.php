<?php declare(strict_types=1); ?>
<section class="hero-bg h-[95vh] relative flex flex-col justify-center items-center text-center px-4 pt-20">
    <div class="max-w-3xl relative z-10"><p data-inline-style="program-66">Papua Future Project</p><h1 data-inline-style="program-67">Program Kami</h1><p data-inline-style="program-68">Lima program aktif yang lahir dari kebutuhan nyata anak-anak dan komunitas di Papua Barat.</p></div>
    <div class="wave-divider"><svg preserveAspectRatio="none" viewBox="0 0 1200 120" xmlns="http://www.w3.org/2000/svg"><path class="shape-fill" d="M321.39 56.44c58-10.79 114.16-30.13 172-41.86 82.39-16.72 168.19-17.73 250.45-.39C823.78 31 906.67 72 985.66 92.83c70.05 18.48 146.53 26.09 214.34 3V120H0V95.8C59.71 118.08 130.83 123.1 192.27 110.15 237.49 100.35 280.9 82.6 321.39 56.44Z"/></svg></div>
</section>
<section class="pfp-section"><div class="pfp-container"><span class="pfp-tag">Yang kami lakukan</span><h2 class="pfp-title">Program yang dirancang bersama, bukan untuk kampung.</h2><div class="pfp-divider"></div>
    <div class="prog-grid-full">
    <?php foreach ($programs as $program): ?>
        <article class="prog-card">
            <div class="prog-img program-photo"><?php if (!empty($program['cover_image'])): ?><img src="<?= e(url('/uploads/' . $program['cover_image'])) ?>" alt="" loading="lazy"><?php endif; ?><span class="prog-badge"><?= e($program['category_label']) ?></span></div>
            <div class="prog-body"><h3><?= e($program['title']) ?></h3><p><?= e($program['summary']) ?></p>
                <?php if (!empty($program['stat1_value']) || !empty($program['stat2_value'])): ?><div class="stat-chips"><?php foreach (['1','2'] as $i): if (!empty($program['stat'.$i.'_value'])): ?><span class="stat-chip"><?= e($program['stat'.$i.'_value']) ?> <?= e($program['stat'.$i.'_label'] ?? '') ?></span><?php endif; endforeach; ?></div><?php endif; ?>
                <hr class="prog-divider"><a class="prog-link" href="<?= url('/program/' . rawurlencode($program['slug'])) ?>">Lihat detail program <span aria-hidden="true">→</span></a>
            </div>
        </article>
    <?php endforeach; ?>
    <?php if ($programs === []): ?><p>Informasi program akan segera tersedia.</p><?php endif; ?>
    </div>
</div></section>
