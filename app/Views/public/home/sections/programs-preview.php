<?php declare(strict_types=1); ?>
<section class="pfp-section" id="program">
<div class="pfp-container">
<span class="pfp-tag">Yang kami lakukan</span>
<h2 class="pfp-title">Program yang dirancang bersama, bukan untuk kampung.</h2>
<div class="pfp-divider"></div>
<p class="pfp-body">Setiap program lahir dari kebutuhan nyata yang kami dengar langsung dari warga dan anak-anak di kampung.</p>
<div class="prog-grid">
<?php foreach ($programs as $program): ?><article class="prog-card">
<div class="prog-img program-photo"><?php if (!empty($program['cover_image'])): ?><img src="<?=e(url('/uploads/'.$program['cover_image']))?>" alt="" loading="lazy"><?php endif; ?><span class="prog-badge"><?=e($program['category_label'])?></span></div>
<div class="prog-body"><h3><?=e($program['title'])?></h3><p><?=e($program['summary'])?></p><a class="prog-link" href="<?=url('/program/'.rawurlencode($program['slug']))?>">Lihat program <span aria-hidden="true">→</span></a></div>
</article><?php endforeach; ?>
<?php if ($programs === []): ?><p>Informasi program akan segera tersedia.</p><?php endif; ?>
</div></div></section>
