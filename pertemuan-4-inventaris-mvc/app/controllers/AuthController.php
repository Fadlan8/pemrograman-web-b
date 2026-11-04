<?php

class AuthController extends Controller
{
    public function index() {
        // Tampilkan view login atau proses default
        echo "Halaman Login";
    }
    public function login(): void
    {
        $this->view('auth/login', ['title' => 'Login']);
    }

    public function authenticate(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('auth/login');
        }
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        $user = $this->model('User')->login($username, $password);
        if ($user) {
            //Simpan data user ke session
            Security::loginUser($user);
            // Update last login timestamp
            $this->model('User')->touchLogin((int) $user['id']);
            // Log aktivitas login
            $this->logActivity('auth.login', 'user', (int) $user['id'], 'Pengguna Berhasil login');
            $this->flash('success', 'Selamat datang, ' . $user['full_name'] . '!');
            $this->redirect('dashboard');
        }else {
            // Log aktivitas login gagal
            $this->logActivity('auth.login_failed', 'user', null, 'Gagal login dengan username: ' . $username);
        }
        $this->flash('danger', 'Username atau password salah.');
        $this->redirect('auth/login');
    }

    public function logout(): void
    {
        Security::logout();
        // Log aktivitas logout
        $this->logActivity('auth.logout', 'user', isset($_SESSION['user']['id']) ? (int) $_SESSION['user']['id'] : null, 'Pengguna Logout');
        header('Location: ' . BASEURL . '/auth/login');
        exit;
    }
}
