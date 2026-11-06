<?php

class HomeController extends Controller
{
    public function __construct()
    {
        $this->requireLogin();
    }

    public function index(): void
    {
        $this->authorize('dashboard.view');
        $item = $this->model('Item');
        $transaction = $this->model('Transaction');

        //Ambil data untuk grafik transaksi harian selama 7 hari terakhir
        $daily = $transaction->dailyTotals(7);
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

            //tambahkan data baru 
            'chartLabels' => array_column($daily, 'label'),
            'chartIn' => array_column($daily, 'in'),
            'chartOut' => array_column($daily, 'out'),
            'topItems' => $item->topStock(5),
        ]);
    }
}
