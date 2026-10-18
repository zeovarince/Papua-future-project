<main class="login-card">
    <a class="login-brand" href="/">
        <span class="admin-brand-mark" aria-hidden="true">P</span>
        <span><strong>Papua Future Project</strong><small>Admin Panel</small></span>
    </a>
    <div class="login-intro">
        <p class="eyebrow">Area pengelola</p>
        <h1>Masuk ke dashboard</h1>
        <p>Gunakan akun admin yang sudah terdaftar untuk mengelola website.</p>
    </div>
    <?php if (!empty($error)): ?>
        <div class="form-alert" role="alert"><?= e($error) ?></div>
    <?php endif; ?>
    <form action="/admin/login" method="post" class="login-form">
        <?= csrf_field() ?>
        <label for="email">Email</label>
        <input id="email" name="email" type="email" autocomplete="username" required maxlength="190" value="<?= e($email ?? '') ?>">
        <label for="password">Kata sandi</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required maxlength="4096">
        <button type="submit" class="button-primary">Masuk</button>
    </form>
    <a class="back-home" href="/">← Kembali ke situs publik</a>
</main>
