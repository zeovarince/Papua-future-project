<section class="welcome-panel">
    <p class="eyebrow">Papua Future Project</p>
    <h2>Selamat datang, <?= e($admin['name'] ?? 'Admin') ?></h2>
    <p>Fondasi panel admin sudah aktif. Ringkasan statistik akan muncul saat modul data terkait selesai dibangun.</p>
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
        <p>Akun admin, autentikasi, sesi, token CSRF, dan koneksi database sudah menjadi fondasi panel ini.</p>
    </div>
</section>
