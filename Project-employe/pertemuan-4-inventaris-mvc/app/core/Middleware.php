<?php

class Middleware
{
    public static function isLoggedIn(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return !empty($_SESSION['user']);
    }

    public static function requireLogin(): void
    {
        if (!self::isLoggedIn()) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Silakan login terlebih dahulu.'];
            header('Location: ' . BASEURL . '/auth/login');
            exit;
        }
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }
}
