<?php

class TransactionsController extends Controller
{
    public function __construct()
    {
        $this->requireLogin();
    }

    public function index(): void
    {
        $this->authorize('transactions.view');

        // 1. Baca parameter filter tipe dari URL
        $type = query_param('type');

        // Ambil parameter dari URL untuk paginasi
        $limit = (int) ($_GET['limit'] ?? 10);
        if (!in_array($limit, [5, 10, 20, 50, 100], true)) {
            $limit = 10;
        }
        $page = max(1, (int) query_param('page', '1'));

        $this->view('transactions/index', [
            'title' => 'Riwayat Transaksi',

            // 2. Teruskan array filter berisi 'type' ke parameter ke-3 fungsi paginate()
            'transactions' => $this->model('Transaction')->paginate($limit, $page, ['type' => $type]),
            'currentLimit' => $limit,

           // 3. Teruskan variabel type ke view agar dropdown pilihan tidak kereset
            'type' => $type,
        ]);
    }

    public function stockIn(): void
    {
        $this->authorize('transactions.create');
        $this->store('IN', 'Barang masuk berhasil dicatat!');
    }

    public function stockOut(): void
    {
        $this->authorize('transactions.create');
        $this->store('OUT', 'Barang keluar berhasil dicatat!');
    }

    private function store(string $type, string $msg): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('transactions');
        }
        $itemId = (int) ($_POST['item_id'] ?? 0);
        $qty = (int) ($_POST['quantity'] ?? 0);
        if ($itemId <= 0 || $qty <= 0) {
            $this->flash('danger', 'Data tidak valid.');
            $this->redirect('items');
        }
        $ok = $this->model('Transaction')->create([
            'item_id' => $itemId,
            'user_id' => $_SESSION['user']['id'],
            'type' => $type,
            'quantity' => $qty,
            'notes' => trim($_POST['notes'] ?? '') ?: null,
        ]);

        // ACTIVITY LOG: Catatan transaksi berhasil dibuat
        if ($ok) {
            $this->logActivity(
                'stock.' . strtolower($type),
                'item',
                $itemId,
                $type . ' ' . $quantity . ' unit'
            );

             // 2. Set pesan sukses (warna hijau)
            $msg = ($type === 'IN') ? 'Stok berhasil ditambahkan.' : 'Stok berhasil dikurangi.';
            $this->flash('success', $msg);
        }else {
            $this->flash('danger','Gagal. Stok tidak mencukupi.');
        }
        $this->redirect('items');
    }
}
