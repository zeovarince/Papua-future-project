<section class="welcome-panel">
    <p class="eyebrow">Papua Future Project</p>
    <h2>Selamat datang, <?= e($admin['name'] ?? 'Admin') ?></h2>
    <p>Kelola konten situs dan tinjau pendaftar dari menu samping. Angka ringkasan mengikuti data yang tersimpan.</p>
</section>

<section class="metric-grid" aria-label="Ringkasan statistik">
    <?php foreach ($metrics as $metric): ?>
        <article class="metric-card">
            <p><?= e($metric['label']) ?></p>
            <strong><?= e($metric['value']) ?></strong>
            <small><?= e($metric['note']) ?></small>
        </article>
    <?php endforeach; ?>
</section>

<section class="dashboard-empty card-surface">
    <div class="empty-icon" aria-hidden="true">✓</div>
    <div>
        <h2>Dashboard siap digunakan</h2>
        <p>Kelola konten publik, pantau pendaftar, dan tindak lanjuti pesan masuk dari panel ini.</p>
    </div>
</section>
