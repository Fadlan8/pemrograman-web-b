<?php
class security
{
    private const CSRF_KEY = '_csrf_token';
    public static function startSession(): void
    {
        $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        ini_set('session.use_strict_mode', '1');
        session_start();

        $lastActivity = (int)($_SESSION['last_activity'] ?? 0);
      if($lastActivity > 0 && (time() - $lastActivity) > SESSION_IDLE_TIMEOUT) {
            $_SESSION = [];
            session_regenerate_id(true);
            $_SESSION['flash'] = [
                'type' => 'warning',
                'message' => 'Sesi Anda telah habis. Silakan masuk kembali.',
            ];
        }
        $_SESSION['last_activity'] = time();
    }
    public static function csrfToken(): string
    {
        if (empty($_SESSION[self::CSRF_KEY])) {
            $_SESSION[self::CSRF_KEY] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::CSRF_KEY];
    }
    public static function csrfField(): string
    {
        return '<input type="hidden" name="_csrf" value="' . e(self::csrfToken()) . '">';
    }
    public static function verifyCsrf(?string $token): bool
    {
        return is_string($token)
            && $token !== ''
            && hash_equals(self::csrfToken(), $token);
    }
    public static function requireCsrf(): void
    {
        if (self::verifyCsrf($_POST['_csrf'] ?? null)) {
            return;
        }
        self::abort(
            419,
            'Sesi formulir kedaluwarsa',
            'Muat ulang halaman, lalu kirim formulir kembali.'
        );
    }
    public static function rotateCsrfToken(): void
    {
        $_SESSION[self::CSRF_KEY] = bin2hex(random_bytes(32));
    }
    public static function loginUser(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user'] = $user;
        $_SESSION['last_activity'] = time();
        self::rotateCsrfToken();
    }
    public static function logout(): void
    {
        $_SESSION = [];
         if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', [
                'expires'  => time() - 42000,
                'path'     => $params['path'],
                'domain'   => $params['domain'],
                'secure'   => $params['secure'],
                'httponly' => $params['httponly'],
                'samesite' => $params['samesite'],
            ]);
        }
        session_destroy();
    }
    public static function abort(int $status, string $title, string $message): void
    {
        http_response_code($status);
        extract(array_merge(view_shared_data(), [
            'title'   => $title,
            'status'  => $status,
            'message' => $message,
        ]));
        require ROOT_PATH . '/app/views/errors/error.php';
        exit;
    }
    public static function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }
    public static function clientIp(): string
    {
        return substr((string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'), 0, 45);
    }
}