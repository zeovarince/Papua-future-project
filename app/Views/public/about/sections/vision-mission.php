<section class="pfp-section pfp-section-alt" id="visimisi">
<div class="pfp-container">
    <div class="about-section-heading">
        <span class="pfp-tag">Arah &amp; Tujuan</span>
        <h2 class="pfp-title">Yang kami perjuangkan setiap harinya.</h2>
        <div class="pfp-divider"></div>
    </div>
    <div class="visimisi-grid">
        <article class="visi-card">
            <p class="vision-label">Visi</p>
            <h3><?= e($about['vision'] ?? '') ?></h3>
            <p>Kami percaya bahwa investasi terbaik adalah investasi pada generasi muda Papua karena mereka yang akan menentukan masa depan tanah ini.</p>
        </article>
        <article class="misi-card">
            <p class="mission-label">Misi</p>
            <h3>Misi Kami</h3>
            <?php foreach (preg_split('/\R/', trim((string) ($about['mission'] ?? ''))) ?: [] as $mission): ?>
                <?php if (trim($mission) !== ''): ?>
                    <div class="misi-item">
                        <div class="misi-check" aria-hidden="true">✓</div>
                        <p><?= e(trim($mission)) ?></p>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </article>
    </div>
</div>
</section>
