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

    return [
        'flash' => take_flash(),
        'loggedIn' => Middleware::isLoggedIn(),
        'user' => Middleware::user(),
        'current' => $current,
        'navItems' => [
            'home' => ['label' => 'Dashboard', 'icon' => 'speedometer2', 'url' => ''],
            'items' => ['label' => 'Barang', 'icon' => 'box', 'url' => 'items'],
            'categories' => ['label' => 'Kategori', 'icon' => 'tags', 'url' => 'categories'],
            'transactions' => ['label' => 'Transaksi', 'icon' => 'arrow-left-right', 'url' => 'transactions'],
            'users' => ['label' => 'Pengguna', 'icon' => 'people', 'url' => 'users'],
        ],
    ];
}
