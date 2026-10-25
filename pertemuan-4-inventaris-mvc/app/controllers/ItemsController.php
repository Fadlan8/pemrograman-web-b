<?php
class ItemsController extends Controller{
    private const LIMITS = [5, 10, 20, 50, 100];
    public function __construct() {
        $this->requireLogin();
    }
    public function index(): void
    {
        $limit = (int) $this->queryParam('limit');
        if (!in_array($limit, self::LIMITS, true)) {
            $limit = 10;
        }
        $page = max(1, (int) $this->queryParam('page'));
        $search = $this->queryParam('search');
        $category = $this->queryParam('category');

        $result = $this->model('Item')->paginate($limit, $page, $search, $category);

        $this->view('items/index', [
            'title' => 'Data Barang',
            'items' => $result['data'],
            'page' => $result['current_page'],
            'totalPages' => $result['total_pages'],
            'totalData' => $result['total_data'],
            'limit' => $limit,
            'limits' => self::LIMITS,
            'search' => $search,
            'category' => $category,
            'categories' => $this->model('Category')->all(),
        ]);
    }

    public function create(): void
    {
        $this->view('items/create', [
            'title' => 'Tambah Barang',
            'item' => ['code' => $this->model('Item')->generateCode()],
            'categories' => $this->model('Category')->all(),
        ]);
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('items');
        $input = array_map('trim', $_POST);
        $error = $this->validationError($input);
        if ($error) {
            $this->flash('danger', $error);
            $this->redirect('items/create');
        }
        try {
            $this->model('Item')->create($this->payload($input));
        } catch (PDOException $e) {
            $this->flash('danger', 'Gagal menyimpan. Kode barang mungkin sudah dipakai.');
            $this->redirect('items/create');
        }
        $this->flash('success', 'Barang berhasil ditambahkan.');
        $this->redirect('items');
    }

    public function edit($id = 0): void
    {
        $item = $this->model('Item')->find((int) $id);
        if (!$item) {
            $this->flash('danger', 'Barang tidak ditemukan.');
            $this->redirect('items');
        }
        $this->view('items/edit', [
            'title' => 'Ubah Barang',
            'item' => $item,
            'categories' => $this->model('Category')->all(),
        ]);
    }

    public function update($id = 0): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('items');
        $id = (int) $id;
        $input = array_map('trim', $_POST);
        $error = $this->validationError($input);
        if ($error) {
            $this->flash('danger', $error);
            $this->redirect('items/edit/' . $id);
        }
        try {
            $this->model('Item')->update($id, $this->payload($input));
        } catch (PDOException $e) {
            $this->flash('danger', 'Gagal menyimpan. Kode barang mungkin sudah dipakai.');
            $this->redirect('items/edit/' . $id);
        }
        $this->flash('success', 'Barang berhasil diperbarui.');
        $this->redirect('items');
    }

    public function delete($id = 0): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('items');
        $this->model('Item')->delete((int) $id);
        $this->flash('success', 'Barang berhasil dihapus.');
        $this->redirect('items');
    }

    private function validationError(array $input): ?string
    {
        if (($input['code'] ?? '') === '' || ($input['name'] ?? '') === '') {
            return 'Kode dan nama barang wajib diisi.';
        }
        if ((int) ($input['quantity'] ?? 0) < 0 || (float) ($input['price'] ?? 0) < 0) {
            return 'Stok dan harga tidak boleh negatif.';
        }
        return null;
    }

    private function payload(array $input): array
    {
        return [
            'code' => $input['code'],
            'name' => $input['name'],
            'category_id' => ($input['category_id'] ?? '') !== '' ? (int)$input['category_id'] : null,
            'quantity' => (int)($input['quantity'] ?? 0),
            'unit' => $input['unit'] ?: 'unit',
            'price' => (float)($input['price'] ?? 0),
            'entry_date' => ($input['entry_date'] ?? '') !== '' ? $input['entry_date'] : null,
            'description' => $input['description'] ?? '',
        ];
    }

    private function queryParam(string $key): string
    {
        $value = $_GET[$key] ?? '';
        return is_string($value) ? trim($value) : '';
    }
}