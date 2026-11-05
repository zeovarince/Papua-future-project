<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <link rel="icon" type="image/png" sizes="192x192" href="<?= asset('img/favicon.png') ?>">
    <title><?= e($title ?? 'Admin') ?> — Papua Future Project</title>
    <link rel="stylesheet" href="<?= asset('css/admin/admin-base.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/admin/admin-layout.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/admin/admin-components.css') ?>">
</head>
<body class="admin-shell" data-page="<?= e($page ?? 'admin') ?>">
    <?php require APP_ROOT . '/app/Views/partials/admin/sidebar.php'; ?>

    <div class="admin-main">
        <?php if (!isset($customHeader) || !$customHeader): ?>
            <?php require APP_ROOT . '/app/Views/partials/admin/page-header.php'; ?>
        <?php endif; ?>

        <main class="admin-content <?= !empty($hasStickyActionBar) ? 'has-sticky-bar' : '' ?>">
            <?= $content ?>
        </main>
    </div>

    <?php require APP_ROOT . '/app/Views/partials/admin/confirm-dialog.php'; ?>
    <?php require APP_ROOT . '/app/Views/partials/admin/toast.php'; ?>

    <script src="<?= asset('js/admin/admin-ui.js') ?>" defer></script>
</body>
</html>
