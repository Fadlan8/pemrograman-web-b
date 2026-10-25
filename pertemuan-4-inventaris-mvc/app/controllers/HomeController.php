<?php
class HomeController extends Controller{
    public function __construct() {
        $this->requireLogin();
    }
   public function index(): void {
        $item = $this->model('Item');
        $transaction = $this->model('Transaction');

        $this->view('dashboard/index', [
            'title' => 'Dashboard',
            'totalItems' => $item->totalItems(),
            'totalStock' => $item->totalStock(),
            'totalAssetValue' => $item->totalAssetValue(),
            'lowStockCount' => $item->lowStockCount(),
            'lowStockItems' => $item->lowStockItems(),
            'lowStockThreshold' => Item::LOW_STOCK_THRESHOLD,
            'todayCount' => $transaction->todayCount(),
            'recentTransactions' => $transaction->recent(5),
        ]);
    }

}