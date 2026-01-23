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
        if (strpos(trim($line), '#') === 0)
            continue;
        [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
        $key = trim($key);
        $value = trim($value);
        if ($key === '')
            continue;
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

// Try multiple .env locations in order of priority
$envLoaded = false;

// 1. Production: /home/kaminova/.env (outside public_html)
$productionPath = '/home/kaminova/.env';

// 2. Project root: one directory up from config (for local dev like XAMPP)
$projectRootPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env';

// 3. Legacy fallback: three directories up
$legacyFallbackPath = dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . '.env';

// Try each path in order
$envPaths = [$productionPath, $projectRootPath, $legacyFallbackPath];

foreach ($envPaths as $envPath) {
    if (file_exists($envPath) && is_readable($envPath)) {
        env_load($envPath);
        error_log("Env file loaded from: " . $envPath);
        $envLoaded = true;
        break;
    }
}

if (!$envLoaded) {
    error_log("Warning: No .env file found. Checked paths: " . implode(', ', $envPaths));
}

// Validate mandatory database variables from .env file
// SECURITY: No credentials are hardcoded - all values must come from .env

$requiredDbVars = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS'];
$missingVars = [];

foreach ($requiredDbVars as $var) {
    if (env($var, null) === null) {
        $missingVars[] = $var;
    }
}

// Fail securely if any required database variables are missing
if (!empty($missingVars)) {
    error_log("CRITICAL: Missing required environment variables: " . implode(', ', $missingVars));
    // In production, you may want to show a generic error page instead
    if (env('APP_ENV', 'production') !== 'production') {
        die("Configuration error: Missing required environment variables: " . implode(', ', $missingVars));
    } else {
        die("Configuration error. Please contact the administrator.");
    }
}

?>