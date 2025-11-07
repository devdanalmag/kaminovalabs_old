<?php
session_start();
require_once '../config/database.php';

class Auth {
    
    public static function login($username, $password) {
        $sql = "SELECT * FROM admin_users WHERE username = :username OR email = :email";
        $user = fetchOne($sql, ['username' => $username, 'email' => $username]);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['admin_email'] = $user['email'];
            return true;
        }
        
        return false;
    }
    
    public static function logout() {
        session_destroy();
        header('Location: login.php');
        exit();
    }
    
    public static function isLoggedIn() {
        return isset($_SESSION['admin_id']);
    }
    
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header('Location: login.php');
            exit();
        }
    }
    
    public static function getCurrentUser() {
        if (self::isLoggedIn()) {
            return [
                'id' => $_SESSION['admin_id'],
                'username' => $_SESSION['admin_username'],
                'email' => $_SESSION['admin_email']
            ];
        }
        return null;
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    Auth::logout();
}
?>