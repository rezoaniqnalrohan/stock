<?php

// Set storage directory to /tmp for Vercel serverless environment
$_ENV['APP_STORAGE'] = '/tmp/storage';
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
$_ENV['APP_SERVICES_CACHE'] = '/tmp/storage/framework/services.php';
$_ENV['APP_PACKAGES_CACHE'] = '/tmp/storage/framework/packages.php';
$_ENV['APP_CONFIG_CACHE'] = '/tmp/storage/framework/config.php';
$_ENV['APP_ROUTES_CACHE'] = '/tmp/storage/framework/routes.php';

// Create required temporary directories
$dirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Forward request to Laravel entry point
require __DIR__ . '/../public/index.php';
