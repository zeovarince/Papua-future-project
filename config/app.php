<?php

declare(strict_types=1);

return [
    'name' => getenv('APP_NAME') ?: 'Papua Future Project',
    'timezone' => getenv('APP_TIMEZONE') ?: 'Asia/Jayapura',
    'environment' => getenv('APP_ENV') ?: 'local',
    'debug' => filter_var(getenv('APP_DEBUG') ?: 'false', FILTER_VALIDATE_BOOL),
];
