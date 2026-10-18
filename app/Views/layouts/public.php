<!DOCTYPE html>
<html lang="id">
<head>
    <?php require APP_ROOT . '/app/Views/partials/public/head.php'; ?>
</head>
<body data-page="<?= e($page ?? '') ?>">
    <?php require APP_ROOT . '/app/Views/partials/public/header.php'; ?>
    <main>
        <?= $content ?>
    </main>
    <?php require APP_ROOT . '/app/Views/partials/public/footer.php'; ?>
    <script src="<?= asset('js/main.js') ?>" defer></script>
    <?php if (!empty($script)): ?>
        <script src="<?= asset($script) ?>" defer></script>
    <?php endif; ?>
</body>
</html>
