<?php
class TransactionsController extends Controller {
   public function index(): void {
        // Tangkap limit dari URL atau beri nilai default 10
        $currentLimit = $_GET['limit'] ?? 10;
        
        // Ambil semua data transaksi
        $data = $this->model('Transaction')->all();

        $this->view('transactions/index', [
            'title' => 'Riwayat Transaksi',
            // Bungkus data array agar sesuai dengan ekspektasi view paginasi
            'transactions' => [
                'data' => $data,
                'total_pages' => 1,
                'total_data' => count($data),
                'current_page' => 1
            ],
            'currentLimit' => $currentLimit
        ]);
    }

    public function create(): void {
        $this->view('transactions/create', [
            'title' => 'Catat Transaksi',
            'items' => $this->model('Item')->all()
        ]);
    }

    public function store(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('transactions');
        
        $item_id = (int)$_POST['item_id'];
        $type = $_POST['type'];
        $quantity = (int)$_POST['quantity'];
        
        // Validasi khusus Barang Keluar (Stok tidak boleh minus)
        if ($type === 'OUT') {
            $item = $this->model('Item')->find($item_id);
            if ($item['quantity'] < $quantity) {
                $this->flash('danger', 'Gagal! Stok barang tidak mencukupi untuk dikeluarkan.');
                $this->redirect('transactions/create');
                return;
            }
        }

        $data = [
            'item_id' => $item_id,
            'user_id' => 1, // Hardcode ID admin sementara sampai Langkah 10 selesai
            'type' => $type,
            'quantity' => $quantity,
            'notes' => trim($_POST['notes'])
        ];

        try {
            $this->model('Transaction')->createTransaction($data);
            $this->flash('success', 'Transaksi berhasil disimpan dan stok otomatis diperbarui.');
        } catch (Exception $e) {
            $this->flash('danger', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
        
        $this->redirect('transactions');
    }
}