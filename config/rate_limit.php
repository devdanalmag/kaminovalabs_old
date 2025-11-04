<?php
// Simple session-based rate limiter

function rate_limit($key, $maxAttempts, $windowSeconds)
{
    $now = time();
    if (!isset($_SESSION['rate_limit'])) {
        $_SESSION['rate_limit'] = [];
    }
    if (!isset($_SESSION['rate_limit'][$key])) {
        $_SESSION['rate_limit'][$key] = ['window_start' => $now, 'count' => 0];
    }
    $entry = &$_SESSION['rate_limit'][$key];
    if ($now - $entry['window_start'] > $windowSeconds) {
        $entry['window_start'] = $now;
        $entry['count'] = 0;
    }
    $entry['count']++;
    if ($entry['count'] > $maxAttempts) {
        return false;
    }
    return true;
}

?>