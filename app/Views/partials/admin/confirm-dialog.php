<div class="confirm-dialog-backdrop" id="confirmDialog" hidden role="dialog" aria-modal="true" aria-labelledby="confirmDialogTitle" aria-describedby="confirmDialogDesc">
    <div class="confirm-dialog-card">
        <div class="confirm-dialog-icon" aria-hidden="true">
            <?= icon('alert-triangle', 'confirm-icon-svg', 24) ?>
        </div>
        <h3 class="confirm-dialog-title" id="confirmDialogTitle">Konfirmasi Hapus</h3>
        <p class="confirm-dialog-desc" id="confirmDialogDesc">Apakah kamu yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.</p>
        <div class="confirm-dialog-actions">
            <button type="button" class="btn btn-secondary" data-confirm-cancel>Batal</button>
            <button type="button" class="btn btn-danger" data-confirm-proceed>Hapus</button>
        </div>
    </div>
</div>

