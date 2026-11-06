<?php

declare(strict_types=1);

use App\Support\AdminMenu;

$menuGroups = AdminMenu::items($_SERVER['REQUEST_URI'] ?? '/admin', $page ?? null);
$adminUser = $admin ?? \App\Core\Session::get('admin', []);
$adminName = (string) ($adminUser['name'] ?? 'Administrator');
$adminInitial = mb_strtoupper(mb_substr($adminName, 0, 1));
?>

<!-- Desktop Rail (>= 768px) -->
<aside class="admin-rail" id="adminRail" aria-label="Navigasi Utama Admin" data-admin-rail>
    <div class="rail-top">
        <a href="<?= url('/admin') ?>" class="rail-logo" title="Papua Future Project" aria-label="Papua Future Project Admin">
            <img src="<?= asset('img/logo.png') ?>" alt="" aria-hidden="true" width="28" height="28">
        </a>

        <!-- Quick Add "+" Button (DESIGN-ADMIN.md section 3.5) -->
        <div class="rail-item-wrap" data-flyout-trigger="quick-add">
            <button type="button" class="rail-quick-add" aria-label="Tambah Cepat" aria-haspopup="menu" aria-expanded="false" title="Tambah Cepat">
                <?= icon('plus', '', 20) ?>
            </button>
            <div class="admin-flyout" role="menu" id="flyout-quick-add">
                <div class="flyout-header">
                    <div class="flyout-header-icon"><?= icon('plus', '', 18) ?></div>
                    <div class="flyout-header-info">
                        <h4 class="flyout-header-title">Tambah Data</h4>
                        <p class="flyout-header-desc">Pintasan tambah konten dan anggota.</p>
                    </div>
                </div>
                <ul class="flyout-items-list">
                    <li role="none">
                        <a href="<?= url('/admin/program/tambah') ?>" class="flyout-link" role="menuitem">
                            <span class="flyout-link-icon"><?= icon('book-open', '', 18) ?></span>
                            <span class="flyout-link-text">
                                <span class="flyout-link-name">Tambah Program</span>
                                <span class="flyout-link-desc">Program kerja baru</span>
                            </span>
                        </a>
                    </li>
                    <li role="none">
                        <a href="<?= url('/admin/berita/tambah') ?>" class="flyout-link" role="menuitem">
                            <span class="flyout-link-icon"><?= icon('newspaper', '', 18) ?></span>
                            <span class="flyout-link-text">
                                <span class="flyout-link-name">Tambah Berita</span>
                                <span class="flyout-link-desc">Artikel atau kegiatan</span>
                            </span>
                        </a>
                    </li>
                    <li role="none">
                        <a href="<?= url('/admin/tim/penggerak/tambah') ?>" class="flyout-link" role="menuitem">
                            <span class="flyout-link-icon"><?= icon('users', '', 18) ?></span>
                            <span class="flyout-link-text">
                                <span class="flyout-link-name">Tambah Anggota Tim</span>
                                <span class="flyout-link-desc">Profil pengurus inti</span>
                            </span>
                        </a>
                    </li>
                    <li role="none">
                        <a href="<?= url('/admin/faq/tambah') ?>" class="flyout-link" role="menuitem">
                            <span class="flyout-link-icon"><?= icon('help-circle', '', 18) ?></span>
                            <span class="flyout-link-text">
                                <span class="flyout-link-name">Tambah FAQ</span>
                                <span class="flyout-link-desc">Pertanyaan umum baru</span>
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="rail-divider" aria-hidden="true"></div>
    </div>

    <!-- Middle Menu Items -->
    <nav class="rail-nav" aria-label="Menu Admin">
        <?php foreach ($menuGroups as $group): ?>
            <?php
                $groupId = $group['id'];
                $hasBadge = !empty($group['badge']) && $group['badge'] > 0;
                $isActiveGroup = !empty($group['is_active']);
                $firstRoute = url($group['route']);
            ?>
            <div class="rail-item-wrap" data-flyout-trigger="<?= e($groupId) ?>">
                <a href="<?= $firstRoute ?>"
                   class="rail-item-btn <?= $isActiveGroup ? 'is-active' : '' ?>"
                   aria-label="<?= e($group['label']) ?>"
                   aria-haspopup="menu"
                   aria-expanded="false"
                   title="<?= e($group['label']) ?>">
                    <?= icon($group['icon'], '', 20) ?>
                    <?php if ($hasBadge): ?>
                        <span class="rail-badge" aria-label="<?= (int) $group['badge'] ?> item baru"><?= (int) $group['badge'] ?></span>
                    <?php endif; ?>
                </a>

                <!-- Flyout Menu (Hover / Focus) -->
                <div class="admin-flyout" role="menu" id="flyout-<?= e($groupId) ?>">
                    <div class="flyout-header">
                        <div class="flyout-header-icon"><?= icon($group['icon'], '', 18) ?></div>
                        <div class="flyout-header-info">
                            <h4 class="flyout-header-title"><?= e($group['label']) ?></h4>
                            <p class="flyout-header-desc"><?= e($group['description']) ?></p>
                        </div>
                    </div>

                    <ul class="flyout-items-list">
                        <?php foreach ($group['items'] as $item): ?>
                            <?php
                                $itemIsActive = !empty($item['is_active']);
                                $itemBadge = (int) ($item['badge'] ?? 0);
                            ?>
                            <li role="none">
                                <a href="<?= url($item['route']) ?>"
                                   class="flyout-link <?= $itemIsActive ? 'is-active' : '' ?>"
                                   role="menuitem">
                                    <span class="flyout-link-icon"><?= icon($item['icon'], '', 18) ?></span>
                                    <span class="flyout-link-text">
                                        <span class="flyout-link-name"><?= e($item['label']) ?></span>
                                        <span class="flyout-link-desc"><?= e($item['description']) ?></span>
                                    </span>
                                    <?php if ($itemBadge > 0): ?>
                                        <span class="flyout-badge"><?= $itemBadge ?></span>
                                    <?php endif; ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endforeach; ?>
    </nav>

    <!-- Rail Bottom / Admin Avatar Profile -->
    <div class="rail-bottom">
        <div class="rail-divider" aria-hidden="true"></div>
        <div class="rail-item-wrap" data-flyout-trigger="profile">
            <button type="button" class="rail-avatar-btn" aria-label="Profil Admin" aria-haspopup="menu" aria-expanded="false" title="<?= e($adminName) ?>">
                <?= e($adminInitial) ?>
            </button>
            <div class="admin-flyout flyout-profile" role="menu" id="flyout-profile">
                <div class="flyout-header">
                    <div class="flyout-header-icon"><?= icon('user-check', '', 18) ?></div>
                    <div class="flyout-header-info">
                        <h4 class="flyout-header-title"><?= e($adminName) ?></h4>
                        <p class="flyout-header-desc">Administrator</p>
                    </div>
                </div>
                <div class="flyout-profile-actions">
                    <a href="<?= url('/admin/akun') ?>" class="flyout-action-btn" role="menuitem">
                        <?= icon('users', '', 18) ?>
                        <span>Pengelola Admin</span>
                    </a>
                    <a href="<?= url('/admin/akun/password') ?>" class="flyout-action-btn" role="menuitem">
                        <?= icon('lock', '', 18) ?>
                        <span>Ubah Kata Sandi</span>
                    </a>
                    <a href="<?= url('/') ?>" target="_blank" rel="noopener noreferrer" class="flyout-action-btn" role="menuitem">
                        <?= icon('external-link', '', 18) ?>
                        <span>Lihat Website</span>
                    </a>
                    <form action="<?= url('/admin/logout') ?>" method="post">
                        <?= csrf_field() ?>
                        <button type="submit" class="flyout-action-btn is-danger" role="menuitem">
                            <?= icon('log-out', '', 18) ?>
                            <span>Keluar</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</aside>

<!-- Mobile Drawer (< 768px) -->
<div class="mobile-drawer-backdrop" id="mobileDrawerBackdrop" data-drawer-backdrop hidden></div>

<aside class="mobile-drawer" id="mobileDrawer" aria-label="Menu Admin Mobile" data-mobile-drawer>
    <div class="mobile-drawer-header">
        <a href="<?= url('/admin') ?>" class="mobile-drawer-brand">
            <span class="mobile-drawer-logo">
                <img src="<?= asset('img/logo.png') ?>" alt="" aria-hidden="true" width="28" height="28">
            </span>
            <span class="mobile-drawer-brand-text">
                <strong>Papua Future Project</strong>
                <small>Admin Panel</small>
            </span>
        </a>
        <button type="button" class="mobile-drawer-close" aria-label="Tutup menu" data-drawer-close>
            <?= icon('x', '', 20) ?>
        </button>
    </div>

    <nav class="mobile-drawer-nav">
        <?php foreach ($menuGroups as $group): ?>
            <?php
                $groupId = $group['id'];
                $isSingle = !empty($group['is_single']);
                $isActiveGroup = !empty($group['is_active']);
            ?>
            <?php if ($isSingle): ?>
                <a href="<?= url($group['route']) ?>" class="drawer-link <?= $isActiveGroup ? 'is-active' : '' ?>">
                    <span class="drawer-link-icon"><?= icon($group['icon'], '', 20) ?></span>
                    <span><?= e($group['label']) ?></span>
                </a>
            <?php else: ?>
                <div class="drawer-accordion-group">
                    <button type="button" class="drawer-accordion-btn" aria-expanded="<?= $isActiveGroup ? 'true' : 'false' ?>" data-accordion-trigger>
                        <span style="display: flex; align-items: center; gap: 10px;">
                            <span class="drawer-link-icon"><?= icon($group['icon'], '', 20) ?></span>
                            <span><?= e($group['label']) ?></span>
                        </span>
                        <?= icon('chevron-down', 'drawer-accordion-arrow', 16) ?>
                    </button>
                    <div class="drawer-accordion-content <?= $isActiveGroup ? 'is-open' : '' ?>">
                        <?php foreach ($group['items'] as $item): ?>
                            <?php
                                $itemIsActive = !empty($item['is_active']);
                                $itemBadge = (int) ($item['badge'] ?? 0);
                            ?>
                            <a href="<?= url($item['route']) ?>" class="drawer-link <?= $itemIsActive ? 'is-active' : '' ?>">
                                <span class="drawer-link-icon"><?= icon($item['icon'], '', 18) ?></span>
                                <span style="flex: 1;"><?= e($item['label']) ?></span>
                                <?php if ($itemBadge > 0): ?>
                                    <span class="flyout-badge"><?= $itemBadge ?></span>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </nav>

    <div class="mobile-drawer-footer">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px; padding: 0 4px;">
            <div class="rail-avatar-btn" style="width: 32px; height: 32px; font-size: 13px;">
                <?= e($adminInitial) ?>
            </div>
            <div>
                <strong style="color: #ffffff; font-size: 13px; display: block; line-height: 1.2;"><?= e($adminName) ?></strong>
                <small style="color: rgba(255,255,255,0.7); font-size: 11px;">Administrator</small>
            </div>
        </div>
        <form action="<?= url('/admin/logout') ?>" method="post">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-secondary btn-sm" style="width: 100%; border-color: rgba(255,255,255,0.4); color: var(--admin-sidebar); background: #ffffff;">
                <?= icon('log-out', '', 16) ?>
                <span>Keluar</span>
            </button>
        </form>
    </div>
</aside>
