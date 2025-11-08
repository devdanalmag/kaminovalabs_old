<?php
// Simple .env loader and helpers

function env_load($path)
{
    if (!file_exists($path)) {
        error_log("Env file not found: " . $path);
        return;
    }
    
    // Check if file is readable
    if (!is_readable($path)) {
        error_log("Env file not readable: " . $path);
        return;
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
        $key = trim($key);
        $value = trim($value);
        if ($key === '') continue;
        // Unquote values
        if ((str_starts_with($value, '"') && str_ends_with($value, '"')) || (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
            $value = substr($value, 1, -1);
        }
        putenv("{$key}={$value}");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

function env($key, $default = null)
{
    // Check in this order: $_ENV, getenv(), $_SERVER
    if (isset($_ENV[$key])) {
        return $_ENV[$key];
    }
    
    $val = getenv($key);
    if ($val !== false && $val !== '') {
        return $val;
    }
    
    if (isset($_SERVER[$key])) {
        return $_SERVER[$key];
    }
    
    return $default;
}

// Load from /home/kaminova/.env (outside public_html and config)
$homePath = '/home/kaminova/.env';

if (file_exists($homePath) && is_readable($homePath)) {
    env_load($homePath);
    error_log("Env file loaded from: " . $homePath);
} else {
    // Fallback: Try to construct path dynamically
    $fallbackPath = dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . '.env';
    if (file_exists($fallbackPath) && is_readable($fallbackPath)) {
        env_load($fallbackPath);
        error_log("Env file loaded from fallback: " . $fallbackPath);
    } else {
        error_log("Warning: No .env file found at: " . $homePath . " or " . $fallbackPath);
    }
}

//Validate mandatory variables and apply defaults
$required = [
    'DB_HOST' => 'localhost',
    'DB_NAME' => 'kaminova_labs',
    'DB_USER' => 'root',
    'DB_PASS' => ''
];

// $required = [
//     'DB_HOST' => 'localhost',
//     'DB_NAME' => 'kaminova_DB',
//     'DB_USER' => 'kaminova_DB_ADMIN',
//     'DB_PASS' => '*Kaminovadbadmin2025#'
// ];

foreach ($required as $k => $def) {
    if (env($k, null) === null) {
        $_ENV[$k] = $def;
        putenv("{$k}={$def}");
        $_SERVER[$k] = $def;
    }
}

?>