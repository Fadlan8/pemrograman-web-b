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
            Security::loginUser($user);
            $this->model('User')->touchLogin((int) $user['id']);
            $this->flash('success', 'Selamat datang, ' . $user['full_name'] . '!');
            $this->redirect('dashboard');
        }
        $this->flash('danger', 'Username atau password salah.');
        $this->redirect('auth/login');
    }

    public function logout(): void
    {
        Security::logout();
        header('Location: ' . BASEURL . '/auth/login');
        exit;
    }
}
