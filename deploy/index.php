<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Path ke folder Laravel (sejajar dengan folder subdomain)
// Struktur server:
//   /home/diantor2/tiktok-live-manager/    <- Folder Laravel
//   /home/diantor2/live.groovy-media.com/  <- Document root subdomain (file ini di sini)

$laravelPath = __DIR__ . '/../tiktok-live-manager';

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = $laravelPath . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require $laravelPath . '/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once $laravelPath . '/bootstrap/app.php')
    ->handleRequest(Request::capture());
