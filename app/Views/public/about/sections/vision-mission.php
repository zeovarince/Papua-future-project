<?php
$visionText = trim((string) ($about['vision'] ?? ''));
$missionItems = array_values(array_filter(
    array_map('trim', preg_split('/\R/', trim((string) ($about['mission'] ?? ''))) ?: []),
    static fn (string $mission): bool => $mission !== ''
));
?>
<?php if ($visionText !== '' || $missionItems !== []): ?>
<section class="pfp-section pfp-section-alt" id="visimisi">
<div class="pfp-container">
    <div class="about-section-heading">
        <span class="pfp-tag">Arah &amp; Tujuan</span>
        <h2 class="pfp-title">Yang kami perjuangkan setiap harinya.</h2>
        <div class="pfp-divider"></div>
    </div>
    <div class="visimisi-grid<?= $visionText === '' || $missionItems === [] ? ' visimisi-grid-single' : '' ?>">
        <?php if ($visionText !== ''): ?>
        <article class="visi-card">
            <p class="vision-label">Visi</p>
            <h3><?= e($visionText) ?></h3>
            <p>Kami percaya bahwa investasi terbaik adalah investasi pada generasi muda Papua karena mereka yang akan menentukan masa depan tanah ini.</p>
        </article>
        <?php endif; ?>
        <?php if ($missionItems !== []): ?>
        <article class="misi-card">
            <p class="mission-label">Misi</p>
            <h3>Misi Kami</h3>
            <?php foreach ($missionItems as $mission): ?>
                <div class="misi-item">
                    <div class="misi-check" aria-hidden="true">✓</div>
                    <p><?= e($mission) ?></p>
                </div>
            <?php endforeach; ?>
        </article>
        <?php endif; ?>
    </div>
</div>
</section>
<?php endif; ?>
