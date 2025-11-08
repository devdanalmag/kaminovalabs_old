<?php
require_once 'auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (Auth::login($username, $password)) {
        // Set a one-time flag to show the welcome GIF after login
        $_SESSION['login_gif'] = true;
        header('Location: dashboard.php');
        exit();
    } else {
        $error = 'Invalid username or password';
    }
}

// Redirect if already logged in
if (Auth::isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Kaminova Labs</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="shortcut icon" href="/img/logo-tp.png" type="image/x-icon">
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body>
    <div class="login-page">
    <div class="login-container">
        <div class="logo">
            <h1>Kaminova Labs</h1>
            <p>Admin Panel</p>
        </div>
        <!-- Removed inline GIF; it will appear after successful login on the dashboard -->
        <?php if ($error): ?>
            <div class="error">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="username">Username or Email</label>
                <div class="input-icon">
                    <i class="fas fa-user"></i>
                    <input type="text" id="username" name="username" required>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>

        <div class="login-hint">Default: admin / admin123</div>
    </div>
    </div>
</body>
</html>