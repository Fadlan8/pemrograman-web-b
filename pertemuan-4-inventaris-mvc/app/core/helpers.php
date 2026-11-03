<?php

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function rupiah($value): string
{
    return 'Rp ' . number_format((float) $value, 0, ',', '.');
}

function base_url(string $path = ''): string
{
    return BASEURL . '/' . ltrim($path, '/');
}

function take_flash(): ?array
{
    if (empty($_SESSION['flash'])) {
        return null;
    }
    $f = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $f;
}

/**
 * Data bersama yang dibutuhkan setiap view (layout).
 */
function view_shared_data(): array
{
    $current = strtolower(explode('/', trim($_GET['url'] ?? 'home', '/'))[0] ?? 'home');
    if ($current === '' || $current === 'dashboard') {
        $current = 'home';
    }

    $navItems = [
        'home'         => ['label' => 'Dashboard', 'icon' => 'speedometer2',           'url' => ''],
        'items'        => ['label' => 'Barang',    'icon' => 'box',                    'url' => 'items'],  
        'categories'   => ['label' => 'Kategori',  'icon' => 'tags',                   'url' => 'categories'],           
        'transactions' => ['label' => 'Transaksi', 'icon' => 'arrow-left-right',       'url' => 'transactions'],
        'reports'      => ['label' => 'Laporan',   'icon' => 'file-earmark-bar-graph', 'url' => 'reports', 'permission' => 'reports.view'],
        'users'        => ['label' => 'Pengguna',  'icon' => 'people',                 'url' => 'users', 'permission' => 'users.view'],
        'logs'         => ['label' => 'Log',       'icon' => 'journal-text',          'url' => 'logs', 'permission' => 'logs.view'],
    ];

    $navItems = array_filter(
        $navItems,
        static fn(array $nav): bool => !isset($nav['permission']) || Middleware::can($nav['permission'])
    );

    return [
        'flash'    => take_flash(),
        'loggedIn' => Middleware::isLoggedIn(),
        'user'     => Middleware::user(),
        'current'  => $current,
        'navItems' => $navItems,
    ];
}

function csrf_field(): string
{
    return Security::csrfField();
}

function asset(?string $path): string
{
    return $path ? BASEURL . '/' . ltrim($path, '/') : '';
}

function tanggal($value, bool $withTime = false): string
{
    if (!$value) {
        return '-';
    }
    $timestamp = strtotime((string) $value);
    if ($timestamp === false) {
        return '-';
    }
    return date($withTime ? 'd/m/Y H:i' : 'd/m/Y', $timestamp);
}

function can(string $permission): bool
{
    return Middleware::can($permission);
}

function query_param(string $key, string $default = ''): string
{
    $value = $_GET[$key] ?? $default;
    return is_string($value) ? trim($value) : $default;
}

function post_input(): array
{
    return array_map(
        static fn($value) => is_string($value) ? trim($value) : $value,
        $_POST
    );
}