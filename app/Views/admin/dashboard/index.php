<?php

declare(strict_types=1);

// Page header custom controls for Dashboard
$periodOptions = [
    '30d' => '30 hari',
    '90d' => '90 hari',
    '12m' => '12 bulan',
];

$periodDropdownHtml = '<div class="period-select-wrap">'
    . '<select class="form-control form-control-sm period-select" data-period-select data-period-base="' . url('/admin') . '" aria-label="Pilih Periode">'
    . implode('', array_map(static fn($key, $label) => '<option value="' . $key . '" ' . ($period === $key ? 'selected' : '') . '>' . $label . '</option>', array_keys($periodOptions), $periodOptions))
    . '</select>'
    . '</div>';

$extraActionsHtml = $periodDropdownHtml;

// SVG Chart calculation
$points = $trend['points'] ?? [];
$chartTotal = (int) ($trend['total'] ?? 0);
$maxCount = 5;
foreach ($points as $pt) {
    if ($pt['count'] > $maxCount) {
        $maxCount = $pt['count'];
    }
}

// Chart viewport dimensions
$svgW = 700;
$svgH = 220;
$padL = 40;
$padR = 20;
$padT = 20;
$padB = 30;
$plotW = $svgW - $padL - $padR;
$plotH = $svgH - $padT - $padB;

$numPoints = count($points);
$coords = [];
if ($numPoints > 1) {
    foreach ($points as $idx => $pt) {
        $cx = $padL + ($idx / ($numPoints - 1)) * $plotW;
        $cy = $padT + (1 - ($pt['count'] / $maxCount)) * $plotH;
        $coords[] = ['x' => $cx, 'y' => $cy, 'val' => $pt['count'], 'lbl' => $pt['label']];
    }
} elseif ($numPoints === 1) {
    $cx = $padL + ($plotW / 2);
    $cy = $padT + (1 - ($points[0]['count'] / $maxCount)) * $plotH;
    $coords[] = ['x' => $cx, 'y' => $cy, 'val' => $points[0]['count'], 'lbl' => $points[0]['label']];
}

$pathLine = '';
$pathArea = '';
if ($coords !== []) {
    $lineSegments = [];
    foreach ($coords as $i => $c) {
        $lineSegments[] = ($i === 0 ? 'M ' : 'L ') . round($c['x'], 1) . ' ' . round($c['y'], 1);
    }
    $pathLine = implode(' ', $lineSegments);
    $baselineY = $svgH - $padB;
    $pathArea = $pathLine . ' L ' . round(end($coords)['x'], 1) . ' ' . $baselineY . ' L ' . round($coords[0]['x'], 1) . ' ' . $baselineY . ' Z';
}
?>

<!-- 4 Metric Cards (DESIGN-ADMIN.md section 4.12 & 5.1) -->
<section class="metric-grid" aria-label="Metrik Ringkasan">
    <!-- 1. Pendaftar Baru -->
    <a href="<?= e($metrics['new_applications']['url']) ?>" class="card metric-card card-clickable">
        <div class="metric-card-top">
            <span class="metric-card-label"><?= e($metrics['new_applications']['label']) ?></span>
            <?php if (!empty($metrics['new_applications']['badge'])): ?>
                <span class="status-pill status-pill--new"><?= e($metrics['new_applications']['badge']) ?></span>
            <?php endif; ?>
        </div>
        <div class="metric-card-bottom">
            <div>
                <div class="metric-card-number"><?= (int) $metrics['new_applications']['value'] ?></div>
                <div class="metric-card-note"><?= e($metrics['new_applications']['note']) ?></div>
            </div>
            <!-- Sparkline mini chart -->
            <svg class="metric-sparkline" viewBox="0 0 80 32" fill="none" aria-hidden="true">
                <path d="M 2 26 Q 20 22 35 15 T 60 12 T 78 6" stroke="#3770c1" stroke-width="2" stroke-linecap="round" fill="none"/>
            </svg>
        </div>
    </a>

    <!-- 2. Pesan Belum Dibaca -->
    <a href="<?= e($metrics['unread_messages']['url']) ?>" class="card metric-card card-clickable">
        <div class="metric-card-top">
            <span class="metric-card-label"><?= e($metrics['unread_messages']['label']) ?></span>
            <?php if ((int) $metrics['unread_messages']['value'] > 0): ?>
                <span class="status-pill status-pill--rejected">Belum dibaca</span>
            <?php endif; ?>
        </div>
        <div class="metric-card-bottom">
            <div>
                <div class="metric-card-number"><?= (int) $metrics['unread_messages']['value'] ?></div>
                <div class="metric-card-note"><?= e($metrics['unread_messages']['note']) ?></div>
            </div>
            <svg class="metric-sparkline" viewBox="0 0 80 32" fill="none" aria-hidden="true">
                <path d="M 2 24 Q 25 20 40 18 T 65 10 T 78 14" stroke="#40b8fd" stroke-width="2" stroke-linecap="round" fill="none"/>
            </svg>
        </div>
    </a>

    <!-- 3. Batch Sedang Dibuka -->
    <a href="<?= e($metrics['open_batches']['url']) ?>" class="card metric-card card-clickable">
        <div class="metric-card-top">
            <span class="metric-card-label"><?= e($metrics['open_batches']['label']) ?></span>
            <?php if ((int) $metrics['open_batches']['value'] > 0): ?>
                <span class="status-pill status-pill--open">Dibuka</span>
            <?php endif; ?>
        </div>
        <div class="metric-card-bottom">
            <div>
                <div class="metric-card-number"><?= (int) $metrics['open_batches']['value'] ?></div>
                <div class="metric-card-note"><?= e($metrics['open_batches']['note']) ?></div>
            </div>
            <svg class="metric-sparkline" viewBox="0 0 80 32" fill="none" aria-hidden="true">
                <path d="M 2 28 Q 20 18 42 16 T 62 10 T 78 8" stroke="#10b981" stroke-width="2" stroke-linecap="round" fill="none"/>
            </svg>
        </div>
    </a>

    <!-- 4. Berita Terbit -->
    <a href="<?= e($metrics['published_posts']['url']) ?>" class="card metric-card card-clickable">
        <div class="metric-card-top">
            <span class="metric-card-label"><?= e($metrics['published_posts']['label']) ?></span>
        </div>
        <div class="metric-card-bottom">
            <div>
                <div class="metric-card-number"><?= (int) $metrics['published_posts']['value'] ?></div>
                <div class="metric-card-note"><?= e($metrics['published_posts']['note']) ?></div>
            </div>
            <svg class="metric-sparkline" viewBox="0 0 80 32" fill="none" aria-hidden="true">
                <path d="M 2 22 Q 22 25 40 16 T 60 12 T 78 6" stroke="#3770c1" stroke-width="2" stroke-linecap="round" fill="none"/>
            </svg>
        </div>
    </a>
</section>

<!-- 12-Column Layout (Section 5.1): 8 cols Left, 4 cols Right -->
<div class="grid-12">
    <!-- ======================================================================
         Left Column (8 of 12 cols): Tren Pendaftar, Pendaftar Terbaru, Pesan Terbaru
         ====================================================================== -->
    <div class="col-8">
        <!-- 1. Tren Pendaftar (DESIGN-ADMIN.md section 4.13) -->
        <article class="card trend-card">
            <div class="trend-card-header">
                <div class="trend-title-group">
                    <h2 class="card-title">Tren Pendaftar</h2>
                    <div class="trend-total-num"><?= $chartTotal ?></div>
                    <span class="trend-period-text"><?= e($periodLabel) ?></span>
                </div>
                <div class="trend-legend" aria-label="Legenda Status">
                    <span class="legend-pill" title="Status Baru">
                        <span class="legend-dot legend-dot-new"></span>
                        <span>Baru</span>
                        <strong><?= (int) ($trend['counts']['NEW'] ?? 0) ?></strong>
                    </span>
                    <span class="legend-pill" title="Status Ditinjau">
                        <span class="legend-dot legend-dot-reviewed"></span>
                        <span>Ditinjau</span>
                        <strong><?= (int) ($trend['counts']['REVIEWED'] ?? 0) ?></strong>
                    </span>
                    <span class="legend-pill" title="Status Diterima">
                        <span class="legend-dot legend-dot-accepted"></span>
                        <span>Diterima</span>
                        <strong><?= (int) ($trend['counts']['ACCEPTED'] ?? 0) ?></strong>
                    </span>
                    <span class="legend-pill" title="Status Ditolak">
                        <span class="legend-dot legend-dot-rejected"></span>
                        <span>Ditolak</span>
                        <strong><?= (int) ($trend['counts']['REJECTED'] ?? 0) ?></strong>
                    </span>
                </div>
            </div>

            <div class="trend-chart-container">
                <?php if ($chartTotal === 0 && $numPoints === 0): ?>
                    <div class="empty-state empty-state-compact" style="height: 100%;">
                        <p class="empty-state-desc">Belum ada pendaftar pada periode ini.</p>
                    </div>
                <?php else: ?>
                    <svg class="trend-svg-chart" viewBox="0 0 <?= $svgW ?> <?= $svgH ?>" preserveAspectRatio="none">
                        <defs>
                            <linearGradient id="areaGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                                <stop offset="0%" stop-color="#3770c1" stop-opacity="0.25"/>
                                <stop offset="100%" stop-color="#3770c1" stop-opacity="0.0"/>
                            </linearGradient>
                        </defs>

                        <!-- Horizontal Grid Lines (dashed) -->
                        <?php for ($g = 0; $g <= 3; $g++): ?>
                            <?php
                                $gy = $padT + ($g / 3) * $plotH;
                                $gVal = round($maxCount * (1 - $g / 3));
                            ?>
                            <line x1="<?= $padL ?>" y1="<?= $gy ?>" x2="<?= $svgW - $padR ?>" y2="<?= $gy ?>" stroke="#e2e8f0" stroke-width="1" stroke-dasharray="4,4" />
                            <text x="<?= $padL - 8 ?>" y="<?= $gy + 4 ?>" text-anchor="end" font-size="10" fill="#94a3b8"><?= $gVal ?></text>
                        <?php endfor; ?>

                        <!-- Area Fill -->
                        <?php if ($pathArea !== ''): ?>
                            <path d="<?= $pathArea ?>" fill="url(#areaGradient)"/>
                        <?php endif; ?>

                        <!-- Stroke Line -->
                        <?php if ($pathLine !== ''): ?>
                            <path d="<?= $pathLine ?>" stroke="#3770c1" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                        <?php endif; ?>

                        <!-- Last point marker -->
                        <?php if ($coords !== []): ?>
                            <?php $lastCoord = end($coords); ?>
                            <circle cx="<?= round($lastCoord['x'], 1) ?>" cy="<?= round($lastCoord['y'], 1) ?>" r="5" fill="#3770c1" stroke="#ffffff" stroke-width="2.5" />
                        <?php endif; ?>

                        <!-- X Axis Labels -->
                        <?php
                            $labelStep = max(1, (int) ceil(count($coords) / 6));
                            foreach ($coords as $i => $c):
                                if ($i % $labelStep === 0 || $i === count($coords) - 1):
                        ?>
                            <text x="<?= round($c['x'], 1) ?>" y="<?= $svgH - 10 ?>" text-anchor="middle" font-size="10" fill="#94a3b8"><?= e($c['lbl']) ?></text>
                        <?php endif; endforeach; ?>
                    </svg>
                <?php endif; ?>
            </div>
        </article>

        <!-- 2. Pendaftar Terbaru (DESIGN-ADMIN.md section 5.1) -->
        <article class="card">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Pendaftar Terbaru</h2>
                    <p class="card-subtitle">5 pendaftar terakhir yang masuk ke sistem.</p>
                </div>
                <a href="<?= url('/admin/pendaftar') ?>" class="card-view-all" style="padding: 0; border: none; margin: 0;">Lihat semua</a>
            </div>
            <div class="table-responsive">
                <?php if ($latestApplications === []): ?>
                    <?php require APP_ROOT . '/app/Views/partials/admin/empty-state.php'; ?>
                <?php else: ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Batch</th>
                                <th>Tipe</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th style="text-align: right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($latestApplications as $app): ?>
                                <tr>
                                    <td>
                                        <strong><?= e($app['full_name']) ?></strong>
                                    </td>
                                    <td><?= e($app['batch_name']) ?></td>
                                    <td>
                                        <span class="badge-pill"><?= $app['batch_type'] === 'INTERN' ? 'Intern' : 'Volunteer' ?></span>
                                    </td>
                                    <td>
                                        <?php $status = $app['status']; require APP_ROOT . '/app/Views/partials/admin/status-pill.php'; ?>
                                    </td>
                                    <td>
                                        <span style="color: var(--admin-text-muted); font-size: 12px;"><?= date('d M Y', strtotime($app['created_at'])) ?></span>
                                    </td>
                                    <td style="text-align: right;">
                                        <a href="<?= url('/admin/pendaftar/' . (int) $app['id']) ?>" class="btn btn-soft btn-xs">Lihat</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </article>

        <!-- 3. Pesan Terbaru (DESIGN-ADMIN.md section 5.1) -->
        <article class="card">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Pesan Terbaru</h2>
                    <p class="card-subtitle">Pesan yang masuk dari formulir kontak publik.</p>
                </div>
                <a href="<?= url('/admin/pesan') ?>" class="card-view-all" style="padding: 0; border: none; margin: 0;">Lihat semua</a>
            </div>
            <div class="table-responsive">
                <?php if ($latestMessages === []): ?>
                    <div class="empty-state empty-state-compact">
                        <h4 class="empty-state-title">Belum ada pesan</h4>
                        <p class="empty-state-desc">Pesan dari pengunjung akan muncul di sini.</p>
                    </div>
                <?php else: ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Pengirim</th>
                                <th>Email</th>
                                <th>Ringkasan</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th style="text-align: right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($latestMessages as $msg): ?>
                                <?php $isUnread = ((int) $msg['is_read'] === 0); ?>
                                <tr class="<?= $isUnread ? 'unread-row' : '' ?>">
                                    <td>
                                        <?php if ($isUnread): ?>
                                            <span class="unread-dot" title="Belum dibaca"></span>
                                        <?php endif; ?>
                                        <strong><?= e($msg['full_name']) ?></strong>
                                    </td>
                                    <td><?= e($msg['email']) ?></td>
                                    <td><?= e(mb_strimwidth($msg['message'], 0, 45, '…')) ?></td>
                                    <td>
                                        <span style="color: var(--admin-text-muted); font-size: 12px;"><?= date('d M Y', strtotime($msg['created_at'])) ?></span>
                                    </td>
                                    <td>
                                        <?php $status = $isUnread ? 'unread' : 'read'; require APP_ROOT . '/app/Views/partials/admin/status-pill.php'; ?>
                                    </td>
                                    <td style="text-align: right;">
                                        <a href="<?= url('/admin/pesan/' . (int) $msg['id']) ?>" class="btn btn-soft btn-xs">Lihat</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </article>
    </div>

    <!-- ======================================================================
         Right Column (4 of 12 cols): Kartu Aksi Cepat, Aktivitas Terbaru
         ====================================================================== -->
    <div class="col-4">
        <!-- 1. Kartu Aksi Cepat (DESIGN-ADMIN.md section 4.14) -->
        <article class="quick-action-card">
            <div class="quick-action-top">
                <p class="quick-action-eyebrow">Perlu Ditinjau</p>
                <div class="quick-action-number"><?= (int) $metrics['new_applications']['value'] ?></div>
                <p class="quick-action-desc">pendaftar menunggu tinjauan</p>
            </div>
            <div class="quick-action-buttons">
                <a href="<?= url('/admin/pendaftar?status=NEW') ?>" class="btn btn-qa-primary btn-sm">
                    <span>Tinjau Pendaftar</span>
                </a>
                <a href="<?= url('/admin/pesan') ?>" class="btn btn-qa-secondary btn-sm">
                    <span>Pesan Masuk</span>
                </a>
            </div>
        </article>

        <!-- 2. Aktivitas Terbaru (DESIGN-ADMIN.md section 4.15) -->
        <article class="card activity-card">
            <div class="card-header card-header-clean">
                <div>
                    <h2 class="card-title">Aktivitas Terbaru</h2>
                    <p class="card-subtitle">Pembaruan terkini</p>
                </div>
            </div>

            <?php if ($activities === []): ?>
                <div class="empty-state empty-state-compact">
                    <p class="empty-state-desc">Belum ada aktivitas baru.</p>
                </div>
            <?php else: ?>
                <ul class="activity-list">
                    <?php foreach ($activities as $act): ?>
                        <li>
                            <a href="<?= e($act['url']) ?>" class="activity-item">
                                <div class="activity-icon-wrap">
                                    <?= icon($act['icon'], '', 18) ?>
                                </div>
                                <div class="activity-details">
                                    <p class="activity-text">
                                        <?= e($act['title']) ?>: <strong><?= e($act['name']) ?></strong>
                                    </p>
                                    <span class="activity-time"><?= e(($formatRelativeTime)($act['time'])) ?></span>
                                </div>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <a href="<?= url('/admin/pendaftar') ?>" class="card-view-all">Lihat semua pendaftar</a>
            <?php endif; ?>
        </article>
    </div>
</div>
