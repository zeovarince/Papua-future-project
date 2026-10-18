<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <title><?= e($title ?? 'Admin') ?> — Papua Future Project</title>
    <link rel="stylesheet" href="<?= asset('css/admin/admin-base.css') ?>">
</head>
<body class="admin-auth-page">
    <?= $content ?>
</body>
</html>
