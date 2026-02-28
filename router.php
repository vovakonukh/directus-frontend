<?php
$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);

// Если запрашивается реальный файл (css, js, картинки) — отдаём его напрямую
if ($path !== '/' && file_exists(__DIR__ . $path)) {
    return false;
}

// Всё остальное — через index.php
require __DIR__ . '/index.php';