<?php
namespace App\Utils;

class CSRF
{
    protected const KEY = 'csrf_token';

    public static function ensureSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function generateToken(): string
    {
        self::ensureSession();
        if (empty($_SESSION[self::KEY])) {
            $_SESSION[self::KEY] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::KEY];
    }

    public static function getToken(): string
    {
        self::ensureSession();
        return $_SESSION[self::KEY] ?? self::generateToken();
    }

    public static function inputField(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(self::getToken(), ENT_QUOTES, 'UTF-8') . '">';
    }

    public static function validate(?string $token): bool
    {
        self::ensureSession();
        if (empty($token) || empty($_SESSION[self::KEY])) {
            return false;
        }
        return hash_equals((string)$_SESSION[self::KEY], (string)$token);
    }

    public static function resetToken(): void
    {
        self::ensureSession();
        unset($_SESSION[self::KEY]);
    }
}