<?php
class AuthController extends Controller{
    public function authenticate(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('auth/login');
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $user = $this->model('User')->login($username, $password);
        if ($user) {
            $_SESSION['user'] = $user;
            $this->flash('success', 'Selamat datang, ' . $user['full_name'] . '!');
            $this->redirect('dashboard');
        }
        $this->flash('danger', 'Username atau password salah.');
        $this->redirect('auth/login');
}
}