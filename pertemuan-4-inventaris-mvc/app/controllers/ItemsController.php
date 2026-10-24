<?php
class ItemsController extends Controller{
    public function __construct() {
        $this->requireLogin();
    }
    public function index(): void{
        //kita siapkan data dasar dulu untuk index
        $search = $_GET['search'] ?? '';
        $category = $_GET['category']  ?? '';
        $currentLimit = (int)($_GET['limit'] ?? 10);

        $this->view('items/index', [
            'title' => 'Data Barang',
            'items' => ['data' => $this->model('Item')->all(), 'total_pages' => 1, 'current_page' => 1, 'total_data' => 0], //sederhana dulu sebelum painasi di langkah 11
            'categories' => $this->model('Category')->all(),
            'search' => $search,
            'category' => $category,
            'currentLimit' => $currentLimit
        ]);
    }

    public function create(): void{
        $this->view('items/create', [
            'title' => 'Tambah Barang',
            'code' => $this->model('Item')->generateCode(),
            'categories' => $this->model('Category')->all()
        ]);
    }
    private function payload(array $input): array {
        return [
        'code' => $input['code'],
        'name' => $input['name'],
        'category_id' => ($input['category_id'] ?? '') !== '' ? (int)$input['category_id'] : null,
            'quantity'    => (int)($input['quantity'] ?? 0),
            'unit'        => $input['unit'] ?: 'unit',
            'price'       => (float)($input['price'] ?? 0),
            'entry_date'  => ($input['entry_date'] ?? '') !== '' ? $input['entry_date'] : null,
            'description' => $input['description'] ?? '',
        ];
    }
    public function store(): void{
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('items');
        $input = array_map('trim', $_POST);

        $this->model('Item')->create($this->payload($input));
        $this->flash('success', 'Barang berhasil ditambahkan.');
        $this->redirect('items');
    }
    public function edit(int $id): void {
        $item = $this->model('Item')->find($id);
        if (!$item) $this->redirect('items');
        $this->view('items/edit', [
            'title' => 'Edit Barang',
            'item' => $item,
            'categories' => $this->model('Category')->all()
        ]);
    }

    public function update(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('items');
        $id = (int)$_POST['id'];
        $input = array_map('trim', $_POST);
        
        $this->model('Item')->update($id, $this->payload($input));
        $this->flash('success', 'Barang berhasil diperbarui.');
        $this->redirect('items');
    }
    
    public function delete(int $id): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model('Item')->delete($id);
            $this->flash('success', 'Barang berhasil dihapus.');
        }
        $this->redirect('items');
    }
}
