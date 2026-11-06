<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" type="image/png" sizes="192x192" href="<?= asset('img/favicon.png') ?>">
<meta name="description" content="<?= e($seo['description'] ?? '') ?>">
<meta name="robots" content="<?= e($seo['robots'] ?? 'noindex,nofollow') ?>">
<link rel="canonical" href="<?= e($seo['canonical'] ?? '') ?>">
<meta property="og:locale" content="id_ID">
<meta property="og:type" content="<?= e($seo['type'] ?? 'website') ?>">
<meta property="og:site_name" content="Papua Future Project">
<meta property="og:title" content="<?= e($seo['title'] ?? $title ?? 'Papua Future Project') ?>">
<meta property="og:description" content="<?= e($seo['description'] ?? '') ?>">
<meta property="og:url" content="<?= e($seo['canonical'] ?? '') ?>">
<meta property="og:image" content="<?= e($seo['image'] ?? '') ?>">
<meta property="og:image:alt" content="Papua Future Project">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= e($seo['title'] ?? $title ?? 'Papua Future Project') ?>">
<meta name="twitter:description" content="<?= e($seo['description'] ?? '') ?>">
<meta name="twitter:image" content="<?= e($seo['image'] ?? '') ?>">
<title><?= e($seo['title'] ?? $title ?? 'Papua Future Project') ?></title>
<?php foreach (($seo['schemas'] ?? []) as $schema): ?>
    <script type="application/ld+json"><?= json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
<?php endforeach; ?>
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
<link rel="stylesheet" href="<?= asset('css/responsive.css') ?>">
