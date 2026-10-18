<?php require ROOT_PATH . '/app/views/layouts/header.php'; ?>
<div class="row justify-content-center" style="margin-top: 8vh;">
    <div class="col-md-5 col-lg-4">
        <div class="text-center mb-4">
            <i class="bi bi-box-seam text-primary" style="font-size: 3rem;"></i>
            <h3 class="fw-bold mt-2 mb-0"><?= APP_NAME ?></h3>
            <small class="text-muted">Sistem Inventaris Barang</small>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h5 class="mb-4 text-center">Login ke Akun Anda</h5>
                <form method="post" action="<?= BASEURL ?>/auth/authenticate">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" name="username" class="form-control"
                                   placeholder="Masukkan username" required autofocus>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" class="form-control"
                                   placeholder="Masukkan password" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                           <i class="bi bi-box-arrow-in-right"></i> Masuk
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require ROOT_PATH . '/app/views/layouts/footer.php'; ?>