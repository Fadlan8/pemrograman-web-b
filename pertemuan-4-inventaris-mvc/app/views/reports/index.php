<?php
$exportQuery = http_build_query([
    'search'    => $filters['search'],
    'category'  => $filters['category'],
    'low_stock' => $filters['low_stock'] ? 1 : 0,
    'from'      => $filters['from'],
    'to'        => $filters['to'],
    'type'      => $filters['type'],
]);
require ROOT_PATH . '/app/views/layouts/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 fw-bold">Laporan</h3>
        <small class="text-muted">Rekap stok dan pergerakan barang</small>
    </div>
    <?php if (can('reports.export')): ?>
        <div class="d-flex gap-2">
            <a href="<?= BASEURL ?>/reports/exportItems?<?= e($exportQuery) ?>" class="btn btn-outline
success">
                <i class="bi bi-filetype-csv"></i> Export Stok
            </a>
            <a href="<?= BASEURL ?>/reports/exportTransactions?<?= e($exportQuery) ?>" class="btn btn
outline-success">
                <i class="bi bi-filetype-csv"></i> Export Transaksi
            </a>
        </div>
    <?php endif; ?>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form action="<?= BASEURL ?>/reports" method="get" class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label small mb-1">Dari Tanggal</label>
                <input type="date" name="from" class="form-control" value="<?= e($filters['from']) ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Sampai Tanggal</label>
                <input type="date" name="to" class="form-control" value="<?= e($filters['to']) ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Tipe Transaksi</label>
                <select name="type" class="form-select">
                    <option value="">Semua</option>
                    <option value="IN" <?= $filters['type'] === 'IN' ? 'selected' : '' ?>>Masuk</option>
                    <option value="OUT" <?= $filters['type'] === 'OUT' ? 'selected' : '' ?>>Keluar</option>
                </select>
            </div>
             <div class="col-md-3">
                <label class="form-label small mb-1">Cari Barang</label>
                <input type="text" name="search" class="form-control"
                       placeholder="Kode / nama barang" value="<?= e($filters['search']) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Kategori</label>
                <select name="category" class="form-select">
                    <option value="">- Semua Kategori -</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= e($c['id']) ?>" <?= (string) $filters['category'] === (string) $c['id'] ? 'selected' : '' ?>>
                            <?= e($c['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <div class="form-check mt-4 pt-2">
                    <input class="form-check-input" type="checkbox" name="low_stock" value="1"
                           id="low_stock" <?= $filters['low_stock'] ? 'checked' : '' ?>>
                    <label class="form-check-label" for="low_stock">Hanya stok menipis (≤ 5)</label>
                </div>
            </div>
            <div class="col-md-9 d-flex gap-2">
                <button class="btn btn-primary"><i class="bi bi-funnel"></i> Terapkan</button>
                <a href="<?= BASEURL ?>/reports" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Transaksi (periode)</div>
                <div class="fs-4 fw-bold"><?= e($summary['total_rows']) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Total Masuk</div>
                <div class="fs-4 fw-bold text-success">+<?= e($summary['total_in']) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Total Keluar</div>
                <div class="fs-4 fw-bold text-danger">-<?= e($summary['total_out']) ?></div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span><i class="bi bi-box text-primary"></i> Rekap Stok</span>
        <span class="badge bg-secondary"><?= count($items) ?> barang</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Kode</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th class="text-center">Stok</th>
                    <th class="text-end">Harga</th>
                    <th class="text-end">Nilai Persediaan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Tidak ada data sesuai filter.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($items as $i => $row): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><code><?= e($row['code']) ?></code></td>
                            <td><?= e($row['name']) ?></td>
                            <td><?= e($row['category_name'] ?? '-') ?></td>
                            <td class="text-center <?= (int) $row['quantity'] <= 5 ? 'text-danger fw-semibold' : '' ?>">
                                <?= e($row['quantity']) ?> <?= e($row['unit']) ?>
                            </td>
                            <td class="text-end"><?= rupiah($row['price']) ?></td>
                            <td class="text-end"><?= rupiah((float) $row['price'] * (int) $row['quantity']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span><i class="bi bi-arrow-left-right text-info"></i> Transaksi Terbaru (periode)</span>
        <a href="<?= BASEURL ?>/transactions" class="btn btn-sm btn-outline-secondary">Riwayat Lengkap</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Waktu</th>
                    <th>Kode</th>
                    <th>Nama Barang</th>
                    <th class="text-center">Tipe</th>
                    <th class="text-center">Qty</th>
                    <th>Petugas</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($transactions['data'])): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Tidak ada transaksi pada periode ini.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($transactions['data'] as $i => $trx): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= tanggal($trx['created_at'], true) ?></td>
                            <td><code><?= e($trx['item_code'] ?? '-') ?></code></td>
                            <td><?= e($trx['item_name'] ?? '-') ?></td>
                            <td class="text-center">
                                <?php if ($trx['type'] === 'IN'): ?>
                                    <span class="badge bg-success">Masuk</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Keluar</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center"><?= e($trx['quantity']) ?></td>
                            <td><?= e($trx['user_name'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require ROOT_PATH . '/app/views/layouts/footer.php'; ?>


