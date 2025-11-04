<?php
// Simple .env loader and helpers

function env_load($path)
{
    if (!file_exists($path)) {
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
    }
}

function env($key, $default = null)
{
    $val = getenv($key);
    if ($val === false || $val === '') {
        return $default;
    }
    return $val;
}

// Load from project root .env
env_load(dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env');

// Validate mandatory variables and apply defaults
$required = [
    'DB_HOST' => 'localhost',
    'DB_NAME' => 'kaminova_labs',
    'DB_USER' => 'root',
    'DB_PASS' => ''
];
foreach ($required as $k => $def) {
    if (env($k, null) === null) {
        $_ENV[$k] = $def;
        putenv("{$k}={$def}");
    }
}

?>