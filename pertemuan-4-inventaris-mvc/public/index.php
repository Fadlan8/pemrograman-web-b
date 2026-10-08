<?php
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/core/helpers.php';

spl_autoload_register(function ($class) {
    foreach (['core', 'controllers', 'models'] as $dir){
        $file = ROOT_PATH . '/app/' . $dir . '/' .$class . '.php';
        if (file_exists($file)){ require_once $file;return;}
    }
});

// Fallback jika rewrite .htaccess tidak tersedia (PHP built-in server),
// sekaligus menangani subfolder, mis. http://localhost/inventaris-mvc/public/.
if (!isset($_GET['url'])){
    $url = parse_url($_SERVER['REQUEST_URI']?? '/', PHP_URL_PATH);
    $basePath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']??")); // untuk Windows
    if ($basePath !== '/' && $basePath !== "&& str_starts_with($url, $basePath)){
        $url = substr($url, strlen($basePath));
    }
    $_GET['url'] = trim($url, '/');
}

session_start();
$app = new App();