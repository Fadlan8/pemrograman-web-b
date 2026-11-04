<?php

class Controller
{
    public function view(string $view, array $data = []): void
    {
        $data = array_merge(view_shared_data(), $data);
        extract($data);
        require ROOT_PATH . '/app/views/' . $view . '.php';
    }

    public function model(string $model)
    {
        return new $model();
    }

    public function redirect(string $path): void
    {
        header('Location: ' . BASEURL . '/' . ltrim($path, '/'));
        exit;
    }

    public function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    protected function requireLogin(): void
    {
        Middleware::requireLogin();
    }
    protected function authorize(string $permission): void
    {
        Middleware::requirePermission($permission);
    }
    protected function requireAdmin(): void
    {
        Middleware::requireRole('admin');
    }
    protected function logActivity(
        string $action,
        ?string $entity = null,
        ?int $entityId = null,
        ?string $description = null
    ): void {
        $userId = isset($_SESSION['user']['id']) ? (int) $_SESSION['user']['id'] : null;
        $this->model('ActivityLog')->record($userId, $action, $entity, $entityId, $description);
    }
}
