<section class="pfp-section" id="tim">
<div class="pfp-container">
<span class="pfp-tag">Orang-orang di baliknya</span>
<h2 class="pfp-title">Tim yang menggerakkan Papua Future Project.</h2>
<div class="pfp-divider"></div>
<div class="team-grid">
<?php foreach ($coreMembers as $member): ?>
    <article class="team-card">
        <div class="team-avatar">
            <?php if (!empty($member['photo_path'])): ?>
                <?php $photoUrl = str_starts_with($member['photo_path'], 'img/') ? asset($member['photo_path']) : url('/uploads/' . $member['photo_path']); ?>
                <img alt="<?= e($member['name']) ?>" src="<?= e($photoUrl) ?>" loading="lazy">
            <?php else: ?>
                <span class="team-avatar-initial"><?= e(mb_strtoupper(mb_substr((string) $member['name'], 0, 2))) ?></span>
            <?php endif; ?>
        </div>
        <div class="team-name"><?= e($member['name']) ?></div>
        <div class="team-role"><?= e($member['role']) ?></div>
        <?php if (!empty($member['bio'])): ?><p class="team-desc"><?= e($member['bio']) ?></p><?php endif; ?>
    </article>
<?php endforeach; ?>
</div>
</div>
</section>
