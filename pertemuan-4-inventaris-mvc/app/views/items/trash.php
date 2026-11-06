<?php require ROOT_PATH . '/app/views/layouts/header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 fw-bold">Sampah Barang</h3>
        <small class="text-muted">Barang yang dihapus dapat dipulihkan</small>
    </div>
    <a href="<?= BASEURL ?>/items" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Kembali ke Barang
    </a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="<?= BASEURL ?>/items/trash" method="get" class="row g-2 mb-3">
            <div class="col-sm-5 col-md-4">
                <input type="text" name="search" class="form-control"
                       placeholder="Cari kode / nama..." value="<?= e($search) ?>">
            </div>
            <div class="col-sm-4 col-md-3">
                <select name="category" class="form-select">
                    <option value="">- Semua Kategori -</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= e($c['id']) ?>" <?= (string) $category === (string) $c['id'] ? 'selected' : '' ?>>
                            <?= e($c['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-primary"><i class="bi bi-search"></i> Cari</button>
                <?php if ($search !== '' || $category !== ''): ?>
                    <a href="<?= BASEURL ?>/items/trash" class="btn btn-outline-secondary">Reset</a>
                <?php endif; ?>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th class="text-center">Stok</th>
                        <th>Dihapus</th>
                        <th class="text-center" style="width:110px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($items['data'])): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Sampah kosong.</td>
                        </tr>
                    <?php else: ?>
                        <?php
                        $no = ($items['current_page'] - 1) * $currentLimit + 1;
                        foreach ($items['data'] as $row): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><code><?= e($row['code']) ?></code></td>
                                <td><?= e($row['name']) ?></td>
                                <td><?= e($row['category_name'] ?? '-') ?></td>
                                <td class="text-center"><?= e($row['quantity']) ?> <?= e($row['unit']) ?></td>
                                <td class="text-muted small"><?= tanggal($row['deleted_at'], true) ?></td>
                                <td class="text-center">
                                    <form action="<?= BASEURL ?>/items/restore/<?= e($row['id']) ?>" method="post"
                                          onsubmit="return confirm('Pulihkan barang ini?')">
                                        <?= csrf_field() ?>
                                        <button class="btn btn-sm btn-outline-success" title="Pulihkan">
                                            <i class="bi bi-arrow-counterclockwise"></i> Pulihkan
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if ($items['total_pages'] > 1):
            $q = ['search' => $search, 'category' => $category, 'limit' => $currentLimit];
        ?>
            <nav class="mt-3">
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item <?= $items['current_page'] <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="?<?= http_build_query($q + ['page' => $items['current_page'] - 1]) ?>">Previous</a>
                    </li>
                    <?php for ($i = 1; $i <= $items['total_pages']; $i++): ?>
                        <li class="page-item <?= $i === $items['current_page'] ? 'active' : '' ?>">
                            <a class="page-link" href="?<?= http_build_query($q + ['page' => $i]) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $items['current_page'] >= $items['total_pages'] ? 'disabled' : '' ?>">
                        <a class="page-link" href="?<?= http_build_query($q + ['page' => $items['current_page'] + 1]) ?>">Next</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>
<?php require ROOT_PATH . '/app/views/layouts/footer.php'; ?>