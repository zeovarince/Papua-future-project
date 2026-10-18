<?php
$founderName = $founder['name'] ?? 'Bhrisco Jordy Dudi Padatu';
$founderRole = $founder['role'] ?? 'Founder Papua Future Project';
$founderPhoto = $founder['photo_path'] ?? 'img/team/Founder.jpg';
$founderPhotoUrl = str_starts_with($founderPhoto, 'img/') ? asset($founderPhoto) : url('/uploads/' . $founderPhoto);
$founderBio = trim((string) ($founder['long_bio'] ?? ''));
if ($founderBio === '') {
    $founderBio = 'Saat pertama kali menginjakkan kaki di Pulau Mansinam, Jordy melihat anak-anak yang semangat belajar tetapi tidak punya akses. Papua Future Project lahir dari kepedulian itu pada 2020 dan terus bergerak bersama relawan untuk mendukung masa depan anak-anak Papua.';
}
?>
<section class="founder-section" id="founder">
    <div class="founder-section-bg" id="founderBg"></div>
    <div class="founder-section-overlay"></div>
    <div class="pfp-container">
        <div class="founder-grid">
            <div class="founder-photo-wrap">
                <img alt="<?= e($founderName) ?> — <?= e($founderRole) ?>" src="<?= e($founderPhotoUrl) ?>" loading="lazy">
                <div class="founder-photo-overlay"><div>
                    <div class="founder-photo-name"><?= e($founderName) ?></div>
                    <div class="founder-photo-role"><?= e($founderRole) ?></div>
                </div></div>
            </div>
            <div class="founder-story">
                <span class="pfp-tag">FOUNDER PAPUA FUTURE PROJECT</span>
                <h2 class="pfp-title"><?= e($founderName) ?></h2>
                <div class="pfp-divider"></div>
                <?php foreach (preg_split('/\R\s*\R/', $founderBio) ?: [] as $paragraph): ?>
                    <?php if (trim($paragraph) !== ''): ?><p><?= nl2br(e(trim($paragraph))) ?></p><?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
