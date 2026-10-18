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
                <?php if ($batchMembers === []): ?>
                    <p class="pfp-body">Informasi anggota batch ini akan diperbarui kemudian.</p>
                <?php else: ?>
                    <div class="team-grid">
                    <?php foreach ($batchMembers as $member): ?>
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
                            <?php if (!empty($member['university'])): ?><p class="team-desc"><?= e($member['university']) ?></p><?php endif; ?>
                            <div class="team-role"><?= e($batch['name']) ?></div>
                            <p class="team-desc"><?= e($member['role']) ?></p>
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
