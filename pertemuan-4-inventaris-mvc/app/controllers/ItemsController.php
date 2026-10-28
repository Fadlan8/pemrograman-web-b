<?php

class ItemsController extends Controller
{
    public function __construct()
    {
        $this->requireLogin();
    }

    public function index(): void
    {
        $limit = (int) ($_GET['limit'] ?? 10);
        if (!in_array($limit, [5, 10, 20, 50, 100], true)) {
            $limit = 10;
        }
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $search = trim($_GET['search'] ?? '');
        $category = trim($_GET['category'] ?? '');

        $this->view('items/index', [
            'title' => 'Data Barang',
            'items' => $this->model('Item')->paginate($limit, $page, $search, $category),
            'categories' => $this->model('Category')->all(),
            'search' => $search,
            'category' => $category,
            'currentLimit' => $limit,
        ]);
    }

    public function create(): void
    {
        $this->view('items/create', [
            'title' => 'Tambah Barang',
            'item' => [],
            'code' => $this->model('Item')->generateCode(),
            'categories' => $this->model('Category')->all(),
        ]);
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('items');
        }
        $input = array_map('trim', $_POST);
        if (empty($input['code']) || empty($input['name'])) {
            $this->flash('danger', 'Kode dan nama barang wajib diisi.');
            $this->redirect('items/create');
        }
        $this->model('Item')->create($this->payload($input));
        $this->flash('success', 'Barang berhasil ditambahkan.');
        $this->redirect('items');
    }

    public function edit($id): void
    {
        $item = $this->model('Item')->find((int) $id);
        if (!$item) {
            $this->flash('danger', 'Barang tidak ditemukan.');
            $this->redirect('items');
        }
        $this->view('items/edit', [
            'title' => 'Edit Barang',
            'item' => $item,
            'categories' => $this->model('Category')->all(),
        ]);
    }

    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('items');
        }
        $input = array_map('trim', $_POST);
        $id = (int) ($input['id'] ?? 0);
        if ($id <= 0 || empty($input['code']) || empty($input['name'])) {
            $this->flash('danger', 'Data barang tidak valid.');
            $this->redirect('items');
        }
        $this->model('Item')->update($id, $this->payload($input));
        $this->flash('success', 'Barang berhasil diperbarui.');
        $this->redirect('items');
    }

    public function delete($id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('items');
        }
        $this->model('Item')->delete((int) $id);
        $this->flash('success', 'Barang berhasil dihapus.');
        $this->redirect('items');
    }

    private function payload(array $input): array
    {
        return [
            'code' => $input['code'],
            'name' => $input['name'],
            'category_id' => ($input['category_id'] ?? '') !== '' ? (int) $input['category_id'] : null,
            'quantity' => (int) ($input['quantity'] ?? 0),
            'unit' => $input['unit'] ?: 'unit',
            'price' => (float) ($input['price'] ?? 0),
            'entry_date' => ($input['entry_date'] ?? '') !== '' ? $input['entry_date'] : null,
            'description' => $input['description'] ?? '',
        ];
    }
}
