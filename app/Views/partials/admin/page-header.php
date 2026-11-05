<?php

declare(strict_types=1);

/**
 * Reusable Page Header component (DESIGN-ADMIN.md section 3.7)
 *
 * Parameters available:
 * - $title: string
 * - $subtitle: ?string
 * - $breadcrumb: array<string|array{label: string, url?: string}>
 * - $primaryAction: ?array{label: string, url: string, icon?: string}
 * - $extraActionsHtml: ?string (e.g. Period dropdown on Dashboard)
 * - $hideViewWebsite: bool
 */
$breadcrumbs = $breadcrumb ?? ['Admin'];
?>
<div class="page-header-wrapper">
    <div class="page-header-row page-header-row-top">
        <button class="mobile-menu-toggle" type="button" aria-label="Buka menu navigasi" aria-expanded="false" aria-controls="mobileDrawer" data-drawer-open>
            <span class="hamburger-lines" aria-hidden="true">
                <span></span><span></span><span></span>
            </span>
        </button>
        <nav class="breadcrumb-nav" aria-label="Breadcrumb">
            <ol class="breadcrumb-list">
                <?php foreach ($breadcrumbs as $index => $crumb): ?>
                    <?php
                        $isLast = ($index === count($breadcrumbs) - 1);
                        $label = is_array($crumb) ? ($crumb['label'] ?? '') : (string) $crumb;
                        $crumbUrl = is_array($crumb) ? ($crumb['url'] ?? null) : null;
                    ?>
                    <li class="breadcrumb-item <?= $isLast ? 'is-active' : '' ?>" <?= $isLast ? 'aria-current="page"' : '' ?>>
                        <?php if (!$isLast && $crumbUrl): ?>
                            <a href="<?= e($crumbUrl) ?>"><?= e($label) ?></a>
                        <?php else: ?>
                            <span><?= e($label) ?></span>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ol>
        </nav>
    </div>

    <div class="page-header-row page-header-row-main">
        <div class="page-title-group">
            <h1 class="page-title"><?= e($title ?? 'Admin') ?></h1>
            <?php if (!empty($subtitle)): ?>
                <p class="page-subtitle"><?= e($subtitle) ?></p>
            <?php endif; ?>
        </div>

        <div class="page-header-actions">
            <?php if (!empty($extraActionsHtml)): ?>
                <?= $extraActionsHtml ?>
            <?php endif; ?>

            <?php if (!empty($primaryAction)): ?>
                <a href="<?= e($primaryAction['url']) ?>" class="btn btn-primary">
                    <?= icon($primaryAction['icon'] ?? 'plus', 'btn-icon', 18) ?>
                    <span><?= e($primaryAction['label']) ?></span>
                </a>
            <?php endif; ?>

            <?php if (empty($hideViewWebsite)): ?>
                <a href="<?= url('/') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-icon-only-mobile" title="Lihat Website">
                    <?= icon('external-link', 'btn-icon', 16) ?>
                    <span class="btn-text-desktop">Lihat Website</span>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

