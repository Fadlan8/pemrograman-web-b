<?php

class CategoriesController extends Controller
{
    public function __construct()
    {
        $this->requireLogin();
    }

    public function index(): void
    {
        $this->view('categories/index', [
            'title' => 'Data Kategori',
            'categories' => $this->model('Category')->all(),
        ]);
    }

    public function create(): void
    {
        $this->view('categories/create', [
            'title' => 'Tambah Kategori',
            'category' => [],
        ]);
    }

    public function store(): void
    {
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
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('categories');
        }
        $this->model('Category')->delete((int) $id);
        $this->flash('success', 'Kategori berhasil dihapus.');
        $this->redirect('categories');
    }
}
