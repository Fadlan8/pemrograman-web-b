<?php

class CategoriesController extends Controller
{
    public function __construct()
    {
        $this->requireLogin();
    }

    public function index(): void
    {
        $this->authorize('categories.view');
        $this->view('categories/index', [
            'title' => 'Data Kategori',
            'categories' => $this->model('Category')->all(),
        ]);
    }

    public function create(): void
    {
        $this->authorize('categories.manage');
        $this->view('categories/create', [
            'title' => 'Tambah Kategori',
            'category' => [],
        ]);
    }

    public function store(): void
    {
        $this->authorize('categories.manage');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('categories');
        }
        $input = array_map('trim', $_POST);
        if (empty($input['name'])) {
            $this->flash('danger', 'Nama kategori wajib diisi.');
            $this->redirect('categories/create');
        }
        $this->model('Category')->create([
            'name' => $input['name'],
            'description' => $input['description'] ?? '',
        ]);
        $this->flash('success', 'Kategori berhasil ditambahkan.');
        $this->redirect('categories');
    }

    public function edit($id): void
    {
        $this->authorize('categories.manage');
        $category = $this->model('Category')->find((int) $id);
        if (!$category) {
            $this->flash('danger', 'Kategori tidak ditemukan.');
            $this->redirect('categories');
        }
        $this->view('categories/edit', [
            'title' => 'Edit Kategori',
            'category' => $category,
        ]);
    }

    public function update(): void
    {
        $this->authorize('categories.manage');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('categories');
        }
        $input = array_map('trim', $_POST);
        $id = (int) ($input['id'] ?? 0);
        if ($id <= 0 || empty($input['name'])) {
            $this->flash('danger', 'Data kategori tidak valid.');
            $this->redirect('categories');
        }
        $this->model('Category')->update($id, [
            'name' => $input['name'],
            'description' => $input['description'] ?? '',
        ]);
        $this->flash('success', 'Kategori berhasil diperbarui.');
        $this->redirect('categories');
    }

    public function delete($id): void
    {
        $this->authorize('categories.manage');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('categories');
        }
        $categoryModel = $this->model('Category');
        $category = $categoryModel->find((int) $id);

        if ($category && $categoryModel->softDelete((int) $id)) {
            $this->flash('success', 'Kategori dipindahkan ke sampah (soft delete).');
        } else {
            $this->flash('danger', 'Kategori gagal dihapus.');
        }
            $this->redirect('categories');
    }
}
