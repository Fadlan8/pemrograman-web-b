<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= isset($title) ? e($title) . ' - ' : '' ?><?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { min-height: 100vh; }
        .navbar-brand { font-weight: 700; }
        .stat-icon { font-size: 2rem; opacity: .85; }
    </style>
</head>
<body class="bg-light">
<?php if ($loggedIn): ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="<?= BASEURL ?>">
                <i class="bi bi-box-seam"></i> <?= APP_NAME ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <?php foreach ($navItems as $key => $nav): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= $current === $key ? 'active' : '' ?>" href="<?= $nav['url'] === '' ? BASEURL : BASEURL . '/' . $nav['url'] ?>">
                                <i class="bi bi-<?= $nav['icon'] ?>"></i> <?= $nav['label'] ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <span class="navbar-text text-white me-3">
                    <i class="bi bi-person-circle"></i> <?= e($user['full_name'] ?? $user['username'] ?? 'Pengguna') ?>
                    <span class="badge bg-light text-primary ms-1"><?= e($user['role'] ?? '') ?></span>
                </span>
                <a href="<?= BASEURL ?>/auth/logout" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </div>
    </nav>
<?php endif; ?>
<main class="<?= $loggedIn ? 'container py-4' : 'container' ?>">
    <?php if ($flash): ?>
        <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show shadow-sm <?= $loggedIn ? 'mt-4' : '' ?>">
            <?= e($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
