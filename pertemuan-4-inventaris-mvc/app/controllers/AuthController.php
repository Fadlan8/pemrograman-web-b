<?php
class AuthController extends Controller {
    public function index(): void {
        if (isset($_SESSION['user_id'])) $this->redirect('home');
        require ROOT_PATH . '/app/views/auth/login.php';
    }

    public function process(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('auth');
        
        $user = $this->model('User')->login($_POST['username'], $_POST['password']);
        
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            $this->redirect('home');
        } else {
            $this->flash('danger', 'Username atau password salah.');
            $this->redirect('auth');
        }
    }

    public function logout(): void {
        session_destroy();
        header('Location: ' . BASEURL . '/auth');
        exit;
    }
}