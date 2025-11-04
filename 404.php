<?php
http_response_code(404);
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found</title>
    <style>
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; margin: 0; padding: 0; display: grid; place-items: center; min-height: 100vh; background: #0b0e12; color: #eaeef2; }
        .box { max-width: 640px; padding: 2rem; text-align: center; }
        a { color: #61dafb; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
    <meta name="robots" content="noindex">
</head>
<body>
    <div class="box">
        <h1>404 — Page Not Found</h1>
        <p>Sorry, the page you’re looking for doesn’t exist or has been moved.</p>
        <p><a href="/">Go back home</a></p>
    </div>
    </body>
    </html>