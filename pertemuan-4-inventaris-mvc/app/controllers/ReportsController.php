<?php
/**
 * Reports: filterable stock recap + transaction recap + CSV export.
 */
class ReportsController extends Controller
{
    public function __construct()
    {
        $this->requireLogin();
    }
    public function index(): void
    {
        $this->authorize('reports.view');
        $filters = $this->filters();
        $itemModel = $this->model('Item');
        $transactionModel = $this->model('Transaction');
        $this->view('reports/index', [
            'title'        => 'Laporan',
            'filters'      => $filters,
            'categories'   => $this->model('Category')->all(),
            'items'        => $itemModel->report($filters),
            'transactions' => $transactionModel->paginate(10, 1, [
                'type' => $filters['type'],
                'from' => $filters['from'],
                'to'   => $filters['to'],
            ]),
            'summary'      => $transactionModel->summary($filters['from'], $filters['to']),
        ]);
    }

    public function exportItems(): void
    {
        $this->authorize('reports.export');
        $filters = $this->filters();
        $rows = $this->model('Item')->report($filters);
        $csv = array_map(static fn(array $row): array => [
            $row['code'],
            $row['name'],
            $row['category_name'] ?? '-',
            $row['quantity'],
            $row['unit'],
            $row['price'],
            $row['entry_date'] ?? '-',
        ], $rows);
        Csv::download(
            'laporan-stok-' . date('Ymd-His') . '.csv',
            ['Kode', 'Nama Barang', 'Kategori', 'Stok', 'Satuan', 'Harga', 'Tanggal Masuk'],
            $csv
        );
    }

    public function exportTransactions(): void
    {
        $this->authorize('reports.export');
        $filters = $this->filters();
        $rows = $this->model('Transaction')->report(
            $filters['from'],
            $filters['to'],
            $filters['type']
        );
        $csv = array_map(static fn(array $row): array => [
            $row['created_at'],
            $row['item_code'] ?? '-',
            $row['item_name'] ?? '-',
            $row['type'] === 'IN' ? 'Masuk' : 'Keluar',
            $row['quantity'],
            $row['user_name'] ?? '-',
            $row['notes'] ?? '-',
        ], $rows);
        Csv::download(
            'laporan-transaksi-' . date('Ymd-His') . '.csv',
            ['Waktu', 'Kode Barang', 'Nama Barang', 'Tipe', 'Qty', 'Petugas', 'Catatan'],
            $csv
        );
    }

    private function filters(): array
    {
        $from = query_param('from');
        $to = query_param('to');
        $type = query_param('type');
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $from)) {
            $from = date('Y-m-01');
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)) {
            $to = date('Y-m-d');
        }
        if ($from > $to) {
            [$from, $to] = [$to, $from];
        }
        return [
             'search'    => query_param('search'),
            'category'  => query_param('category'),
            'low_stock' => !empty($_GET['low_stock']),
            'threshold' => 5,
            'from'      => $from,
            'to'        => $to,
            'type'      => in_array($type, ['IN', 'OUT'], true) ? $type : '',
        ];
    }
}
