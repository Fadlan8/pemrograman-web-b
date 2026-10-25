<?php
class AuthController extends Controller {
     public function login(): void
    {
        $this->view('auth/login', ['title' => 'Login']);
    }


    public function authenticate(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('auth/login');
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $user = $this->model('User')->login($_POST['username'], $_POST['password']);
        
        if ($user) {
             $_SESSION['user'] = $user;
            $this->flash('success', 'Selamat datang, ' . $user['full_name'] . '!');
            $this->redirect('dashboard');
        } else {
            $this->flash('danger', 'Username atau password salah.');
            $this->redirect('auth/login');
        }
    }

    public function logout(): void {
        $_SESSION = [];
        session_regenerate_id(true);
        $this->flash('success', 'Anda telah keluar.');
        $this->redirect('auth/login');
    }
}