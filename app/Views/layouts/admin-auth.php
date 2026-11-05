<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <meta name="description" content="Masuk ke Panel Admin Papua Future Project.">
    <link rel="icon" type="image/png" sizes="192x192" href="<?= asset('img/favicon.png') ?>">
    <title><?= e($title ?? 'Admin') ?> — Papua Future Project</title>
    <link rel="stylesheet" href="<?= asset('css/admin/admin-base.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/admin/admin-login.css') ?>">
</head>
<body class="admin-auth-page">
    <?= $content ?>
    <script src="<?= asset('js/admin/admin-login.js') ?>" defer></script>
</body>
</html>
