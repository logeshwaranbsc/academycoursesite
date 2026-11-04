<?php

declare(strict_types=1);

define('APP_START', microtime(true));
define('ROOT_PATH', dirname(__DIR__));

// 1. Autoloading
if (file_exists(ROOT_PATH . '/vendor/autoload.php')) {
    require ROOT_PATH . '/vendor/autoload.php';
} else {
    require ROOT_PATH . '/app/Core/Autoloader.php';
    \App\Core\Autoloader::register();
    require ROOT_PATH . '/app/Helpers/functions.php';
    require ROOT_PATH . '/app/Helpers/response.php';
    require ROOT_PATH . '/app/Helpers/upload.php';
}

// 2. Load Environment Variables
\App\Core\Env::load(ROOT_PATH . '/.env');

// 3. Configuration & Error Handling
$debug = filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN);
if ($debug) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

date_default_timezone_set('Asia/Kolkata');

// 4. Session Start
if (session_status() === PHP_SESSION_NONE) {
    $sessionPath = ROOT_PATH . '/storage/sessions';
    if (!is_dir($sessionPath)) {
        @mkdir($sessionPath, 0777, true);
    }
    @session_save_path($sessionPath);

    $lifetime = (int)($_ENV['SESSION_LIFETIME'] ?? 7200);
    session_set_cookie_params([
        'lifetime' => $lifetime,
        'path' => '/',
        'domain' => '',
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// 5. Initialize Core Services & Router
try {
    $request = new \App\Core\Request();
    $router = new \App\Core\Router();

    // Load Routes
    require ROOT_PATH . '/routes/web.php';
    require ROOT_PATH . '/routes/admin.php';

    // Dispatch
    $response = $router->dispatch($request);
    $response->send();
} catch (\Throwable $e) {
    error_log("Unhandled Application Error: " . $e->getMessage() . "\n" . $e->getTraceAsString());

    if ($debug) {
        http_response_code(500);
        echo "<div style='font-family:sans-serif;padding:30px;background:#fff1f2;border:1px solid #fecdd3;color:#9f1239;margin:20px;border-radius:8px;'>";
        echo "<h2>Application Error</h2>";
        echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . " (Line " . $e->getLine() . ")</p>";
        echo "<pre style='background:#fff;padding:15px;overflow:auto;border:1px solid #fda4af;border-radius:4px;font-size:12px;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        echo "</div>";
    } else {
        http_response_code(500);
        echo "<!DOCTYPE html><html><head><title>500 Internal Server Error</title><style>body{font-family:sans-serif;text-align:center;padding:60px;color:#333;}h1{color:#10243E;}</style></head><body><h1>Something Went Wrong</h1><p>We are experiencing a temporary error. Please try again shortly or contact support.</p></body></html>";
    }
}
