<?php

class ItemsController extends Controller
{
    private $item;
    private $category;

    public function __construct()
    {
        
        $this->requireLogin();
        $this->item = $this->model('Item');
        $this->category = $this->model('Category');
    }

    public function index(): void
    {
        $this->authorize('items.view');
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
         $this->authorize('items.create');
        $this->view('items/create', [
            'title' => 'Tambah Barang',
            'item' => [],
            'code' => $this->model('Item')->generateCode(),
            'categories' => $this->model('Category')->all(),
        ]);
    }

    public function store(): void
    {
         $this->authorize('items.create');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('items');
        }
        $input = array_map('trim', $_POST);

        if (empty($input['code']) || empty($input['name'])) {
            $this->flash('danger', 'Kode dan nama barang wajib diisi.');
            $this->redirect('items/create');
        }

        $data = $this->payload($input);

        if (!empty($_FILES['photo']['name'])) {
            try {
                $data['photo'] = Upload::image($_FILES['photo']);
            } catch (RuntimeException $e) {
                $this->flash('danger', 'Foto gagal diunggah: ' . $e->getMessage());
                $this->redirect('items/create');
            }
        }
        try {
            $this->item->create($data);
        } catch (PDOException $e) {
            Upload::remove($data['photo']);
            Logger::error('Create item failed', ['error' => $e->getMessage()]);
            $this->flash('danger', 'Barang gagal disimpan. Pastikan kode barang belum dipakai.');
            $this->redirect('items/create');
        }
         // ACTIVITY LOG: Barang berhasil ditambahkan
        $this->logActivity(
            'item.create',
            'item',
            null,
            $data['code'] . ' - ' . $data['name']
        );

        $this->flash('success', 'Barang berhasil ditambahkan.');
        $this->redirect('items');
    }

    public function edit($id): void
    {
        $this->authorize('items.update');
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
        $this->authorize('items.update');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('items');
        }
        $input = array_map('trim', $_POST);
        $id = (int) ($input['id'] ?? 0);

        if ($id <= 0 || empty($input['code']) || empty($input['name'])) {
            $this->flash('danger', 'Data barang tidak valid.');
            $this->redirect('items');
        }

        //ambil data lama 
        $current = $this->item->find($id);
        $data = $this->payload($input);
        $data['photo'] = $current['photo'];//penanganan foto
        $oldPhoto = $current['photo'];
        $newPhoto = null;
        if (!empty($_FILES['photo']['name'])) {
            try {
                $newPhoto = Upload::image($_FILES['photo']);
                $data['photo'] = $newPhoto;
            } catch (RuntimeException $e) {
                $this->flash('danger', 'Foto gagal diunggah: ' . $e->getMessage());
                $this->redirect('items/edit/' . $id);
            }
        } elseif (!empty($input['remove_photo'])) {
            $data['photo'] = null;//hapus foto jika checkbox di centang
        }
        //simpan DB dan rollback jika gagal
        try {
            $this->item->update($id, $data);
        } catch (PDOException $e) {
            Upload::remove($newPhoto);
            $this->flash('danger', 'Barang gagal diperbarui.');
            $this->redirect('items/edit/' . $id);
        }
        //bersihkan foto lama jika ada perubahan
        if ($data['photo'] !== $oldPhoto) {
            Upload::remove($oldPhoto);
        }
        // ACTIVITY LOG: Barang berhasil diperbarui
        $this->logActivity(
            'item.update',
            'item',
            $id,
            $data['code'] . ' - ' . $data['name']
        );

        $this->flash('success', 'Barang berhasil diperbarui.');
        $this->redirect('items');
    }

    /**
     * Soft-delete an item (data stays recoverable).
     */
    public function delete($id = null): void
    {
        $this->authorize('items.delete');
        if (!$id) {
            $this->redirect('items');
        }

        $item = $this->item->find((int) $id);
        
        if ($item && $this->item->softDelete((int) $id)) {
            
            // ACTIVITY LOG: Barang berhasil dihapus (soft delete)
            $this->logActivity(
                'item.delete',
                'item',
                (int) $id,
                $item['code'] . ' - ' . $item['name']
            );

            $this->flash('success', 'Barang dipindahkan ke sampah (soft delete).');
        } else {
            $this->flash('danger', 'Barang gagal dihapus.');
        }
        $this->redirect('items');
    }
    /**
     * Trash bin (administrators only may restore)./daftar sampah barang
     */
    public function trash(): void
    {
        $this->authorize('items.restore');
        $search = query_param('search');
        $categoryId = query_param('category');
        $page = max(1, (int) query_param('page', '1'));
        $limit = $this->resolveLimit();
        $this->view('items/trash', [
            'title'        => 'Sampah Barang',
            'items'        => $this->item->paginate($limit, $page, $search, $categoryId, true),
            'categories'   => $this->category->all(),
            'search'       => $search,
            'category'     => $categoryId,
            'currentLimit' => $limit,
        ]);
    }
    /**
     * Restore a soft-deleted item.
     */
    public function restore($id = null): void
    {
        $this->authorize('items.restore');

        if (!$id || !$this->item->find((int) $id, true)) {
            $this->flash('danger', 'Barang tidak ditemukan di sampah.');
            $this->redirect('items/trash');
        }
        if ($this->item->restore((int) $id)) {
            
            // ACTIVITY LOG: Barang berhasil dipulihkan
            $this->logActivity(
                'item.restore',
                'item',
                (int) $id,
                 'Barang dipulihkan');

            $this->flash('success', 'Barang berhasil dipulihkan.');
        } else {
            $this->flash('danger', 'Barang gagal dipulihkan.');
        }
        $this->redirect('items/trash');
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
            'photo' => $input['photo'] ?? null,
        ];
    }
 /**
     * Item detail with its stock movement history.
     */
      public function show($id = null): void
    {
        $this->authorize('items.view');
        $item = $id ? $this->item->find((int) $id) : null;
        if (!$item) {
            Security::abort(404, 'Barang tidak ditemukan', 'Barang yang Anda cari tidak ada atau sudah dihapus.');
        }
        $this->view('items/show', [
            'title'        => 'Detail Barang',
            'item'         => $item,
            'transactions' => $this->model('Transaction')->forItem((int) $item['id'], 20),
        ]);
    }
}
