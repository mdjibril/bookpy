<?php
namespace App\Controllers;

use App\Utils\Auth;

class AuthController
{
    public function showLogin(): void
    {
        // If already logged in, redirect to admin dashboard
        if (Auth::isLoggedIn()) {
            header('Location: /admin');
            exit;
        }

        $error = $_SESSION['login_error'] ?? null;
        unset($_SESSION['login_error']);

        include __DIR__ . '/../../src/views/auth/login.php';
    }

    public function handleLogin(): void
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        error_log("Attempting login with Username: $username, Password: $password");

        if (Auth::login($username, $password)) {
            header('Location: /admin');
            exit;
        }

        $_SESSION['login_error'] = 'Invalid username or password';
        header('Location: /admin/login');
        exit;
    }

    public function logout(): void
    {
        Auth::logout();
        header('Location: /');
        exit;
    }
}