<?php
/**
 * Audit trail viewer (administrators only).
 */
class LogsController extends Controller
{
    public function __construct()
    {
        $this->requireLogin();
    }
    public function index(): void
    {
        $this->authorize('logs.view');
        $page = max(1, (int) query_param('page', '1'));
        $action = query_param('action');
        $this->view('logs/index', [
            'title'  => 'Log Aktivitas',
            'logs'   => $this->model('ActivityLog')->paginate(30, $page, $action),
            'action' => $action,
        ]);
    }
}