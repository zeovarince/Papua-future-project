<?php

declare(strict_types=1);

/**
 * Reusable empty state component (DESIGN-ADMIN.md section 6)
 *
 * Parameters:
 * - $icon: Lucide icon name (default: 'inbox')
 * - $title: Title string (e.g. "Belum ada program")
 * - $description: Helper/call to action string
 * - $actionUrl: Optional URL for primary action button
 * - $actionLabel: Optional button text (e.g. "+ Tambah Program")
 */
$iconName = $icon ?? 'inbox';
$stateTitle = $emptyTitle ?? $title ?? 'Belum ada data';
?>
<div class="empty-state">
    <div class="empty-state-icon">
        <?= icon($iconName, 'empty-icon-svg', 40) ?>
    </div>
    <h3 class="empty-state-title"><?= e($stateTitle) ?></h3>
    <?php if (!empty($description)): ?>
        <p class="empty-state-desc"><?= e($description) ?></p>
    <?php endif; ?>
    <?php if (!empty($actionUrl) && !empty($actionLabel)): ?>
        <div class="empty-state-action">
            <a href="<?= e($actionUrl) ?>" class="btn btn-primary">
                <?= icon('plus', 'btn-icon', 16) ?>
                <span><?= e($actionLabel) ?></span>
            </a>
        </div>
    <?php endif; ?>
</div>

