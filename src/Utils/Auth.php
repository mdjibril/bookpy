<?php
namespace App\Utils;

class Auth
{
    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
    }

    public static function login(string $username, string $password): bool
    {
        // Try getenv first
        $adminUser = getenv('ADMIN_USERNAME') ?: null;
        $adminPass = getenv('ADMIN_PASSWORD') ?: null;

        // If env vars not set, try to read project .env file as fallback
        if (empty($adminUser) || empty($adminPass)) {
            $envPath = __DIR__ . '/../../.env';
            if (is_readable($envPath)) {
                $pairs = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($pairs as $line) {
                    $line = trim($line);
                    if ($line === '' || strpos($line, '#') === 0) continue;
                    if (strpos($line, '=') === false) continue;
                    [$k, $v] = array_map('trim', explode('=', $line, 2));
                    // remove optional surrounding quotes
                    $v = preg_replace('/^"(.*)"$/', '$1', $v);
                    $v = preg_replace("/^'(.*)'$/", '$1', $v);
                    if ($k === 'ADMIN_USERNAME' && empty($adminUser)) $adminUser = $v;
                    if ($k === 'ADMIN_PASSWORD' && empty($adminPass)) $adminPass = $v;
                }
            }
        }

        if ($adminUser === null || $adminPass === null) {
            error_log("Admin login failed: admin credentials not configured");
            return false;
        }

        // Compare username and password (timing-safe for password)
        if ($username === $adminUser && hash_equals((string)$adminPass, (string)$password)) {
            $_SESSION['admin_id'] = 1;
            $_SESSION['admin_username'] = $username;
            error_log("Admin login successful: {$username}");
            return true;
        }

        error_log("Admin login failed: invalid credentials for {$username}");
        return false;
    }

    public static function logout(): void
    {
        // Clear session safely
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        error_log("Admin logged out");
    }

    public static function requireLogin(): void
    {
        if (!self::isLoggedIn()) {
            header('Location: /admin/login');
            exit;
        }
    }

    public static function getUsername(): ?string
    {
        return $_SESSION['admin_username'] ?? null;
    }
}