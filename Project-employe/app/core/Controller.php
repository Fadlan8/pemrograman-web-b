<?php

class Controller
{
    protected function view(string $viewPath, array $data = []): void
    {
        extract($data);
        $file = __DIR__ . '/../views/' . $viewPath . '.php';

        if (!file_exists($file)) {
            die("View file not found: " . htmlspecialchars($viewPath));
        }
        require $file;
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . BASE_URL . $path);
        exit;
    }
}
    

