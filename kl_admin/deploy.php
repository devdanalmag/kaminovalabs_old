<?php
// === CONFIG ===
$repoDir = '/home/kaminova/public_html'; // path to your repo
$branch = 'main'; // or 'master'
$secret = 'kaminovalabs@2025'; // same secret you set in GitHub webhook

// === VERIFY GITHUB SIGNATURE ===
$headers = getallheaders();
$hubSignature = $headers['X-Hub-Signature-256'] ?? '';

$payload = file_get_contents('php://input');
$hash = 'sha256=' . hash_hmac('sha256', $payload, $secret);

if (!hash_equals($hash, $hubSignature)) {
    http_response_code(403);
    exit('Access denied: invalid signature');
}

// === DEPLOY ===
chdir($repoDir);
$output = shell_exec("git pull origin $branch 2>&1");

// === LOG ===
file_put_contents(
    'deploy.log',
    date('Y-m-d H:i:s') . "\n" . $output . "\n\n",
    FILE_APPEND
);

echo "Deployment done:\n" . nl2br($output);
