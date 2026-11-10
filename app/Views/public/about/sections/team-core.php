<section class="pfp-section" id="tim">
<div class="pfp-container">
<span class="pfp-tag">Core Team</span>
<h2 class="pfp-title">Orang-orang yang menggerakkan Papua Future Project.</h2>
<div class="pfp-divider"></div>
<div class="team-grid team-core-grid">
    <?php foreach ($coreMembers as $member): ?>
        <article class="team-card team-profile-card team-core-card">
            <?php if (!empty($member['photo_path'])): ?>
                <?php $photoUrl = str_starts_with($member['photo_path'], 'img/') ? asset($member['photo_path']) : url('/uploads/' . $member['photo_path']); ?>
                <div class="team-avatar team-profile-photo"><img alt="<?= e($member['name']) ?>" src="<?= e($photoUrl) ?>" loading="lazy"></div>
            <?php else: ?>
                <div class="team-avatar team-profile-photo team-profile-photo-empty" aria-hidden="true"></div>
            <?php endif; ?>
            <div class="team-name team-profile-name"><?= e($member['name']) ?></div>
            <div class="team-member-division team-core-role team-profile-role"><?= e($member['role']) ?></div>
            <?php if (trim((string) ($member['bio'] ?? '')) !== ''): ?><p class="team-desc"><?= e($member['bio']) ?></p><?php endif; ?>
        </article>
    <?php endforeach; ?>
</div>
</div>
</section>
