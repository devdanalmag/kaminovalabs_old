<?php
// Global bootstrap: session, env, security defaults

require_once __DIR__ . '/env.php';

// Secure session cookies; only set 'secure' if HTTPS
$https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || env('FORCE_HTTPS', 'false') === 'true';
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => $https,
    'httponly' => true,
    'samesite' => 'Strict'
]);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Error visibility based on env
if (env('APP_ENV', 'production') === 'production') {
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
}

?>