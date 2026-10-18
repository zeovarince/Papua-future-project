<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <title><?= e($title ?? 'Admin') ?> — Papua Future Project</title>
    <link rel="stylesheet" href="<?= asset('css/admin/admin-base.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/admin/admin-layout.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/admin/admin-components.css') ?>">
</head>
<body class="admin-shell" data-page="<?= e($page ?? 'admin') ?>">
    <?php require APP_ROOT . '/app/Views/partials/admin/sidebar.php'; ?>
    <div class="admin-main">
        <header class="admin-page-header">
            <button class="sidebar-toggle" type="button" aria-label="Buka menu" aria-expanded="false" aria-controls="admin-sidebar" data-sidebar-toggle>
                <span aria-hidden="true">☰</span>
            </button>
            <div class="page-heading">
                <p class="breadcrumb"><?= e(implode(' / ', $breadcrumb ?? ['Admin'])) ?></p>
                <h1><?= e($title ?? 'Dashboard') ?></h1>
            </div>
        </header>
        <?php if ($message = \App\Core\Flash::get('success')): ?>
            <div class="flash-message flash-success" role="status"><?= e($message) ?></div>
        <?php endif; ?>
        <?php if ($message = \App\Core\Flash::get('error')): ?>
            <div class="flash-message flash-error" role="alert"><?= e($message) ?></div>
        <?php endif; ?>
        <main class="admin-content">
            <?= $content ?>
        </main>
    </div>
    <div class="sidebar-overlay" data-sidebar-overlay hidden></div>
    <script src="<?= asset('js/admin/admin-ui.js') ?>" defer></script>
</body>
</html>
