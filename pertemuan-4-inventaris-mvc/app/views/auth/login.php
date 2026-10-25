<?php $flash = take_flash(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Masuk - <?= e(APP_NAME) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
<style>
:root { --accent: #1d4ed8; --accent-hover: #1e40af; }

body {
    font-family: 'Poppins', sans-serif;
    background: #f3f4f6;
    color: #1f2937;
}

.login-wrap {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px;
}
.login-box { width: 100%; max-width: 400px; }

.login-brand {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-bottom: 24px;
    font-size: 1.1rem;
    font-weight: 600;
    color: #111827;
}
.login-brand i { font-size: 28px; color: var(--accent); }

.login-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 32px;
    box-shadow: 0 1px 3px rgba(17, 24, 39, 0.06);
}
.login-card h1 { font-size: 1.25rem; font-weight: 600; color: #111827; margin: 0 0 4px; }
.login-card .sub { color: #6b7280; font-size: 0.875rem; margin-bottom: 24px; }
.login-card .form-label { font-size: 0.85rem; font-weight: 500; color: #374151; margin-bottom: 6px; }

.login-card .form-control { padding: 0.55rem 0.75rem; font-size: 0.925rem; }
.login-card .form-control:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 0.2rem rgba(29, 78, 216, 0.15);
}

/* Password + tombol tampilkan */
.login-card .input-group .form-control:focus { box-shadow: none; }
.login-card .input-group:focus-within {
    border-radius: 0.375rem;
    box-shadow: 0 0 0 0.2rem rgba(29, 78, 216, 0.15);
}
.login-card .input-group:focus-within > * { border-color: var(--accent); }
#password { border-right: 0; }
.btn-eye {
    background: #fff;
    border: 1px solid var(--bs-border-color);
    border-left: 0;
    color: #6b7280;
    font-size: 18px;
}
.btn-eye:hover { color: #111827; background: #fff; }

.btn-submit {
    padding: 0.6rem;
    font-weight: 500;
    background: var(--accent);
    border-color: var(--accent);
    color: #fff;
}
.btn-submit:hover, .btn-submit:focus { background: var(--accent-hover); border-color: var(--accent-hover); color: #fff; }

.login-footer { margin-top: 20px; text-align: center; font-size: 0.8rem; color: #6b7280; }
.login-footer p { margin: 0 0 4px; }
</style>
</head>
<body>
<main class="login-wrap">
    <div class="login-box">
        <div class="login-brand"><i class='bx bx-package'></i><span><?= e(APP_NAME) ?></span></div>

        <div class="login-card">
            <h1>Masuk</h1>
            <p class="sub">Gunakan akun yang diberikan administrator.</p>

            <?php if ($flash): ?>
            <div class="alert alert-<?= e($flash['type']) ?> py-2" role="alert"><?= e($flash['message']) ?></div>
            <?php endif; ?>

            <form method="post" action="<?= base_url('auth/authenticate') ?>">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" name="username" class="form-control" autocomplete="username" required autofocus>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <input type="password" id="password" name="password" class="form-control" autocomplete="current-password" required>
                        <button type="button" id="togglePassword" class="btn btn-eye" aria-label="Tampilkan password"><i class='bx bx-show'></i></button>
                    </div>
                </div>
                <button type="submit" class="btn btn-submit w-100">Masuk</button>
            </form>
        </div>

        <div class="login-footer">
            <p>Lupa password? Hubungi administrator.</p>
            <p>&copy; <?= date('Y') ?> <?= e(APP_NAME) ?></p>
        </div>
    </div>
</main>

<script>
document.getElementById('togglePassword').addEventListener('click', function () {
    var input = document.getElementById('password');
    var show = input.type === 'password';
    input.type = show ? 'text' : 'password';
    this.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
    this.querySelector('i').className = show ? 'bx bx-hide' : 'bx bx-show';
});
</script>
</body>
</html>