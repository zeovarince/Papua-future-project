<?php

declare(strict_types=1);

/**
 * Partial for rendering a status pill according to DESIGN-ADMIN.md section 4.6
 * Expects $status (string) or $type (string) and optional $label (string).
 */
$raw = strtolower(trim((string) ($status ?? $type ?? '')));
$label = (string) ($label ?? match ($raw) {
    'new', 'baru' => 'Baru',
    'reviewed', 'ditinjau' => 'Ditinjau',
    'accepted', 'diterima' => 'Diterima',
    'rejected', 'ditolak' => 'Ditolak',
    'open', 'dibuka' => 'Dibuka',
    'closed', 'ditutup' => 'Ditutup',
    'draft', 'draf' => 'Draf',
    'published', 'terbit' => 'Terbit',
    'unread', 'belum dibaca' => 'Belum dibaca',
    'read', 'sudah dibaca' => 'Sudah dibaca',
    default => ucfirst($raw),
});

$modifier = match ($raw) {
    'new', 'baru' => 'new',
    'reviewed', 'ditinjau' => 'reviewed',
    'accepted', 'diterima' => 'accepted',
    'rejected', 'ditolak' => 'rejected',
    'open', 'dibuka' => 'open',
    'closed', 'ditutup' => 'closed',
    'draft', 'draf' => 'draft',
    'published', 'terbit' => 'published',
    'unread', 'belum dibaca' => 'unread',
    'read', 'sudah dibaca' => 'read',
    default => 'neutral',
};
?>
<span class="status-pill status-pill--<?= e($modifier) ?>"><?= e($label) ?></span>

