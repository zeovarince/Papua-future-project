<?php

declare(strict_types=1);

if (PHP_SAPI === 'cli-server') {
    $requestPath = parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?: '/';
    if ($requestPath !== '/' && is_file(__DIR__ . $requestPath)) {
        return false;
    }
}

$candidates = [];
$configuredAppRoot = trim((string) (getenv('PFP_APP_ROOT') ?: ''));
if ($configuredAppRoot !== '') {
    $candidates[] = $configuredAppRoot;
}

// Local checkout: the application root is the parent of public/.
$candidates[] = dirname(__DIR__);

// DirectAdmin: allow a private pfp-app/ folder alongside public_html/.
$documentRoot = realpath((string) ($_SERVER['DOCUMENT_ROOT'] ?? ''));
if ($documentRoot !== false) {
    $ancestor = $documentRoot;
    for ($depth = 0; $depth < 7; $depth++) {
        $candidates[] = rtrim($ancestor, '/\\') . DIRECTORY_SEPARATOR . 'pfp-app';
        $parent = dirname($ancestor);
        if ($parent === $ancestor) {
            break;
        }
        $ancestor = $parent;
    }
}

$appRoot = null;
foreach (array_unique($candidates) as $candidate) {
    $resolvedCandidate = realpath($candidate);
    if ($resolvedCandidate !== false
        && is_file($resolvedCandidate . '/bootstrap.php')
        && is_file($resolvedCandidate . '/app/Core/App.php')) {
        $appRoot = $resolvedCandidate;
        break;
    }
}

if ($appRoot === null) {
    http_response_code(500);
    exit('Aplikasi belum terpasang dengan benar.');
}

define('APP_ROOT', $appRoot);
define('PUBLIC_ROOT', realpath(__DIR__) ?: __DIR__);
require APP_ROOT . '/bootstrap.php';

(new \App\Core\App())->run();
