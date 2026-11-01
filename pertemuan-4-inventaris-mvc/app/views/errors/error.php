<?php require ROOT_PATH . '/app/views/layouts/header.php'; ?>
<div class="text-center" style="margin-top: 10vh;">
    <h1 class="display-3 fw-bold text-danger"><?= e($status ?? 500) ?></h1>
    <h4 class="mb-3"><?= e($title ?? 'Terjadi kesalahan') ?></h4>
    <p class="text-muted"><?= e($message ?? 'Permintaan tidak dapat diproses.') ?></p>
    <a href="<?= BASEURL ?>" class="btn btn-primary mt-2">
        <i class="bi bi-house"></i> Kembali ke Dashboard
    </a>
</div>
<?php require ROOT_PATH . '/app/views/layouts/footer.php'; ?>