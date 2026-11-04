<?php require ROOT_PATH . '/app/views/layouts/header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 fw-bold">Detail Barang</h3>
        <small class="text-muted">Informasi lengkap dan riwayat pergerakan stok</small>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= BASEURL ?>/items" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
         <?php if (can('items.update')): ?>
            <a href="<?= BASEURL ?>/items/edit/<?= e($item['id']) ?>" class="btn btn-primary">
                <i class="bi bi-pencil"></i> Edit
            </a>
        <?php endif; ?>
    </div>
</div>
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <?php if (!empty($item['photo'])): ?>
                  
                    <img src="<?= BASEURL ?>/<?= e($item['photo']) ?>" alt="<?= e($item['name']) ?>"
                         class="img-fluid rounded mb-3" style="max-height: 220px; object-fit: cover;">
                <?php else: ?>
                    <div class="bg-light rounded d-flex align-items-center justify-content-center mb-3"
                         style="height: 180px;">
                        <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                    </div>
                <?php endif; ?>
                <h5 class="fw-bold mb-1"><?= e($item['name']) ?></h5>
                <code><?= e($item['code']) ?></code>
                <div class="mt-3">
                    <?php if ((int) $item['quantity'] <= 5): ?>
                        <span class="badge bg-danger">Stok menipis</span>
                    <?php else: ?>
                        <span class="badge bg-success">Stok aman</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
     <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-info-circle text-primary"></i> Informasi Barang
            </div>
            <div class="card-body">
                <table class="table table-sm align-middle mb-0">
                    <tr><th style="width:180px;">Kategori</th><td><?= e($item['category_name'] ?? '-') ?></td></tr>
                    <tr><th>Stok</th><td><?= e($item['quantity']) ?> <?= e($item['unit']) ?></td></tr>
                    <tr><th>Harga Satuan</th><td><?= rupiah($item['price']) ?></td></tr>
                    <tr><th>Nilai Persediaan</th><td><?= rupiah((float) $item['price'] * (int) $item['quantity']) ?></td></tr>
                    <tr><th>Tanggal Masuk</th><td><?= tanggal($item['entry_date']) ?></td></tr>
                    <tr><th>Ditambahkan</th><td><?= tanggal($item['created_at'], true) ?></td></tr>
                    <tr><th>Keterangan</th><td><?= e($item['description'] ?: '-') ?></td></tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clock-history text-info"></i> Riwayat Stok (20 terakhir)</span>
                <a href="<?= BASEURL ?>/transactions" class="btn btn-sm btn-outline-secondary">Semua Transaksi</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Waktu</th>
                            <th class="text-center">Tipe</th>
                            <th class="text-center">Qty</th>
                            <th>Petugas</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($transactions)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Belum ada pergerakan stok untuk barang ini.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($transactions as $i => $trx): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= tanggal($trx['created_at'], true) ?></td>
                                    <td class="text-center">
                                        <?php if ($trx['type'] === 'IN'): ?>
                                            <span class="badge bg-success">Masuk</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Keluar</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center"><?= e($trx['quantity']) ?></td>
                                    <td><?= e($trx['user_name'] ?? '-') ?></td>
                                    <td class="text-muted"><?= e($trx['notes'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php require ROOT_PATH . '/app/views/layouts/footer.php'; ?>