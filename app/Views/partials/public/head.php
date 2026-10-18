<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Papua Future Project bergerak dalam pendidikan dan kesehatan anak di Papua Barat. Every Child Matters.">
<title><?= e($title ?? 'Papua Future Project') ?></title>
<script src="<?= asset('js/js-flag.js') ?>"></script>
<script src="<?= asset('js/tailwind.config.js') ?>"></script>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= asset('css/tokens.css') ?>">
<link rel="stylesheet" href="<?= asset('css/base.css') ?>">
<link rel="stylesheet" href="<?= asset('css/layout.css') ?>">
<link rel="stylesheet" href="<?= asset('css/components.css') ?>">
<link rel="stylesheet" href="<?= asset('css/inline-overrides.css') ?>">
<?php if (!empty($stylesheet)): ?>
    <link rel="stylesheet" href="<?= asset($stylesheet) ?>">
<?php endif; ?>
