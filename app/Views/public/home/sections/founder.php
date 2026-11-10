<?php
$founderName = trim((string) ($founder['name'] ?? ''));
$founderRole = trim((string) ($founder['role'] ?? ''));
$founderPhoto = trim((string) (($founder['home_photo_path'] ?? '') ?: ($founder['photo_path'] ?? '')));
$founderPhotoUrl = $founderPhoto === '' ? '' : (str_starts_with($founderPhoto, 'img/') ? asset($founderPhoto) : url('/uploads/' . $founderPhoto));
$founderBio = trim((string) ($founder['long_bio'] ?? ''));
$showFounder = $founder !== null && ($founderName !== '' || $founderRole !== '' || $founderPhoto !== '' || $founderBio !== '');
?>
<?php if ($showFounder): ?>
<section class="founder-section" id="founder">
    <div class="founder-section-bg" id="founderBg"></div>
    <div class="founder-section-overlay"></div>
    <div class="pfp-container">
        <div class="founder-grid<?= $founderPhotoUrl === '' ? ' founder-grid-no-photo' : '' ?>">
            <?php if ($founderPhotoUrl !== ''): ?><div class="founder-photo-wrap">
                <img alt="<?= e($founderName) ?> — <?= e($founderRole) ?>" src="<?= e($founderPhotoUrl) ?>" loading="lazy">
                <div class="founder-photo-overlay"><div>
                    <?php if ($founderName !== ''): ?><div class="founder-photo-name"><?= e($founderName) ?></div><?php endif; ?>
                    <?php if ($founderRole !== ''): ?><div class="founder-photo-role"><?= e($founderRole) ?></div><?php endif; ?>
                </div></div>
            </div><?php endif; ?>
            <div class="founder-story">
                <span class="pfp-tag">FOUNDER PAPUA FUTURE PROJECT</span>
                <?php if ($founderName !== ''): ?><h2 class="pfp-title"><?= e($founderName) ?></h2><?php endif; ?>
                <?php if ($founderPhotoUrl === '' && $founderRole !== ''): ?><p class="founder-story-role"><?= e($founderRole) ?></p><?php endif; ?>
                <?php if ($founderName !== '' || $founderBio !== ''): ?><div class="pfp-divider"></div><?php endif; ?>
                <?php foreach (preg_split('/\R\s*\R/', $founderBio) ?: [] as $paragraph): ?>
                    <?php if (trim($paragraph) !== ''): ?><p><?= nl2br(e(trim($paragraph))) ?></p><?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>
