<?php

declare(strict_types=1);

$successMessage = \App\Core\Flash::get('success');
$errorMessage = \App\Core\Flash::get('error');
$infoMessage = \App\Core\Flash::get('info');
?>
<div class="toast-container" id="toastContainer" aria-live="polite" aria-atomic="true">
    <?php if ($successMessage): ?>
        <div class="toast toast-success" role="status" data-toast>
            <div class="toast-icon"><?= icon('check', '', 18) ?></div>
            <div class="toast-body"><?= e($successMessage) ?></div>
            <button type="button" class="toast-close" aria-label="Tutup notifikasi" data-toast-close>&times;</button>
        </div>
    <?php endif; ?>
    <?php if ($errorMessage): ?>
        <div class="toast toast-error" role="alert" data-toast>
            <div class="toast-icon"><?= icon('alert-triangle', '', 18) ?></div>
            <div class="toast-body"><?= e($errorMessage) ?></div>
            <button type="button" class="toast-close" aria-label="Tutup notifikasi" data-toast-close>&times;</button>
        </div>
    <?php endif; ?>
    <?php if ($infoMessage): ?>
        <div class="toast toast-info" role="status" data-toast>
            <div class="toast-icon"><?= icon('info', '', 18) ?></div>
            <div class="toast-body"><?= e($infoMessage) ?></div>
            <button type="button" class="toast-close" aria-label="Tutup notifikasi" data-toast-close>&times;</button>
        </div>
    <?php endif; ?>
</div>

