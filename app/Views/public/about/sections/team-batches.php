<?php
$renderBatchSection = static function (string $title, string $label, array $batches, array $members): void {
    if ($batches === []) {
        return;
    }
    ?>
    <section class="pfp-section pfp-section-alt team-batches-section">
    <div class="pfp-container">
        <span class="pfp-tag"><?= e($label) ?></span>
        <h2 class="pfp-title"><?= e($title) ?></h2>
        <div class="pfp-divider"></div>
        <?php foreach ($batches as $batch): ?>
            <?php $batchMembers = array_values(array_filter($members, static fn (array $member): bool => (int) $member['batch_id'] === (int) $batch['id'])); ?>
            <div class="team-batch-group">
                <div class="team-batch-heading">
                    <h3><?= e($batch['name']) ?></h3>
                    <span class="team-role"><?= $label ?> · <?= count($batchMembers) ?> anggota</span>
                </div>
                <?php if ($batchMembers !== []): ?>
                    <div class="team-grid">
                    <?php foreach ($batchMembers as $member): ?>
                        <article class="team-card team-profile-card <?= $label === 'Intern' ? 'team-profile-card-internship' : 'team-profile-card-volunteer' ?>">
                            <?php if (!empty($member['photo_path'])): ?>
                                <?php $photoUrl = str_starts_with($member['photo_path'], 'img/') ? asset($member['photo_path']) : url('/uploads/' . $member['photo_path']); ?>
                                <div class="team-avatar team-profile-photo"><img alt="<?= e($member['name']) ?>" src="<?= e($photoUrl) ?>" loading="lazy"></div>
                            <?php else: ?>
                                <div class="team-avatar team-profile-photo team-profile-photo-empty" aria-hidden="true"></div>
                            <?php endif; ?>
                            <div class="team-name team-profile-name"><?= e($member['name']) ?></div>
                            <?php if (trim((string) ($member['university'] ?? '')) !== ''): ?>
                                <p class="team-desc team-member-university"><?= e($member['university']) ?></p>
                            <?php endif; ?>
                            <div class="team-member-division team-profile-role"><?= e($member['role']) ?></div>
                            <?php if ($label !== 'Intern' && trim((string) ($member['bio'] ?? '')) !== ''): ?>
                                <p class="team-desc team-member-bio"><?= e($member['bio']) ?></p>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    </section>
    <?php
};

$renderBatchSection('Internship', 'Intern', $internBatches, $internMembers);
$renderBatchSection('Volunteer / Relawan', 'Volunteer', $volunteerBatches, $volunteerMembers);
?>
