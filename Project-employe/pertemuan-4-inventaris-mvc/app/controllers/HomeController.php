<?php

class HomeController extends Controller
{
    public function __construct()
    {
        $this->requireLogin();
    }

    public function index(): void
    {
        $item = $this->model('Item');
        $transaction = $this->model('Transaction');
        $this->view('dashboard/index', [
            'title' => 'Dashboard',
            'totalItems' => $item->totalItems(),
            'totalCategories' => $this->model('Category')->count(),
            'totalStock' => $item->totalStock(),
            'totalAssetValue' => $item->totalAssetValue(),
            'lowStockCount' => $item->lowStockCount(),
            'lowStockItems' => $item->lowStockItems(),
            'recentTransactions' => $transaction->recent(5),
            'todayTransactions' => $transaction->todayCount(),
        ]);
    }
}
