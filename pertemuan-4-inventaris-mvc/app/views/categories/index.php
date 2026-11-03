<?php require ROOT_PATH . '/app/views/layouts/header.php'; ?>
<?= csrf_field() ?>
<?php $canManageCategories = can('categories.manage'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 fw-bold">Data Kategori</h3>
        <small class="text-muted">Kelola kategori barang</small>
    </div>
    <?php if ($canManageCategories): ?>
        <a href="<?= BASEURL ?>/categories/create" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Tambah Kategori
        </a>
    <?php endif; ?>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>#</th><th>Nama Kategori</th><th>Deskripsi</th><th class="text-center">Jumlah Barang</th><?php if ($canManageCategories): ?><th class="text-center" style="width:120px;">Aksi</th><?php endif; ?></tr>
                </thead>
                <tbody>
                    <?php if (empty($categories)): ?>
                        <tr><td colspan="<?= $canManageCategories ? 5 : 4 ?>" class="text-center text-muted py-4">Belum ada data kategori.</td></tr>
                    <?php else: ?>
                        <?php foreach ($categories as $i => $row): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td class="fw-semibold"><?= e($row['name']) ?></td>
                                <td class="text-muted"><?= e($row['description'] ?: '-') ?></td>
                                <td class="text-center"><span class="badge bg-secondary"><?= e($row['item_count']) ?></span></td>
                                <?php if ($canManageCategories): ?>
                                    <td class="text-center">
                                        <a href="<?= BASEURL ?>/categories/edit/<?= e($row['id']) ?>" class="btn btn-sm btn-outline-warning" title="Edit"><i class="bi bi-pencil"></i></a>
                                        <form action="<?= BASEURL ?>/categories/delete/<?= e($row['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus kategori ini?')">
                                            <?= Security::csrfField() ?>
                                            <button class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require ROOT_PATH . '/app/views/layouts/footer.php'; ?>
