<?php require ROOT_PATH . '/app/views/layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 fw-bold">Dashboard</h3>
        <small class="text-muted">Ringkasan data inventaris barang</small>
    </div>
</div>

//baris kartu statistik
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Jenis Barang</div>
                    <div class="fs-4 fw-bold"><?= e($totalItems) ?></div>
                </div>
                <i class="bi bi-box text-primary stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Kategori</div>
                    <div class="fs-4 fw-bold"><?= e($totalCategories) ?></div>
                </div>
                <i class="bi bi-tags text-success stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Total Stok</div>
                    <div class="fs-4 fw-bold"><?= e($totalStock) ?></div>
                </div>
                <i class="bi bi-stack text-warning stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Nilai Aset</div>
                    <div class="fs-6 fw-bold"><?= rupiah($totalAssetValue) ?></div>
                </div>
                <i class="bi bi-cash-coin text-danger stat-icon"></i>
            </div>
        </div>
    </div>
</div>

//baris grafik & stok terbanyak
<div class="row g-3 mb-3">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <i class="bi bi-bar-chart-line text-primary me-1"></i>
                <strong>Pergerakan Stok 7 Hari</strong>
            </div>
            <div class="card-body">
                <div style="position: relative; height: 260px;">
                    <canvas id="stockChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <i class="bi bi-stack text-warning me-1"></i> <strong>Stok Terbanyak</strong>
            </div>
            <ul class="list-group list-group-flush">
                <?php if (empty($topItems)): ?>
                    <li class="list-group-item text-center text-muted py-4">Belum ada data.</li>
                <?php else: ?>
                    <?php foreach ($topItems as $row): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-truncate me-2"><?= e($row['name']) ?></span>
                            <span class="badge bg-primary rounded-pill"><?= e($row['quantity']) ?></span>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

//3. Baris stok menipis &transaksi
<div class="row g-3">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span><i class="bi bi-exclamation-triangle text-warning me-1"></i> <strong>Stok Menipis (&le; 5)</strong></span>
                <span class="badge bg-warning text-dark"><?= e($lowStockCount) ?></span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Kode</th><th>Nama Barang</th><th>Kategori</th><th class="text-center">Stok</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($lowStockItems)): ?>
                            <tr><td colspan="4" class="text-center text-muted py-4">Semua stok barang masih aman.</td></tr>
                        <?php else: ?>
                            <?php foreach ($lowStockItems as $row): ?>
                                <tr>
                                    <td><code><?= e($row['code']) ?></code></td>
                                    <td><?= e($row['name']) ?></td>
                                    <td><?= e($row['category_name'] ?? '-') ?></td>
                                    <td class="text-center"><span class="badge bg-danger rounded-pill"><?= e($row['quantity']) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white text-end">
                <a href="<?= BASEURL ?>/items" class="btn btn-sm btn-outline-primary">Kelola Barang</a>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clock-history text-info me-1"></i> <strong>Transaksi Terbaru</strong></span>
                <span class="badge bg-info text-dark">Hari ini: <?= e($todayTransactions) ?></span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Waktu</th><th>Barang</th><th class="text-center">Tipe</th><th class="text-center">Qty</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentTransactions)): ?>
                            <tr><td colspan="4" class="text-center text-muted py-4">Belum ada transaksi.</td></tr>
                        <?php else: ?>
                            <?php foreach ($recentTransactions as $trx): ?>
                                <tr>
                                    <td><small><?= e(date('d/m H:i', strtotime($trx['created_at']))) ?></small></td>
                                    <td><?= e($trx['item_name'] ?? '-') ?></td>
                                    <td class="text-center">
                                        <?php if ($trx['type'] === 'IN'): ?>
                                            <span class="badge bg-success">Masuk</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Keluar</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center"><?= e($trx['quantity']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white text-end">
                <a href="<?= BASEURL ?>/transactions" class="btn btn-sm btn-outline-info">Lihat Semua</a>
            </div>
        </div>
    </div>
</div>

//4.script untuk grafik
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const stockChartData = <?= json_encode([
        'labels' => $chartLabels,
        'in' => $chartIn,
        'out' => $chartOut,
    ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

    new Chart(document.getElementById('stockChart'), {
        type: 'bar',
        data: {
            labels: stockChartData.labels,
            datasets: [
                {
                    label: 'Masuk',
                    data: stockChartData.in,
                    backgroundColor: 'rgba(25, 135, 84, .75)',
                    borderRadius: 4
                },
                {
                    label: 'Keluar',
                    data: stockChartData.out,
                    backgroundColor: 'rgba(220, 53, 69, .75)',
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
            plugins: { legend: { position: 'top' } }
        }
    });
</script>

<?php require ROOT_PATH . '/app/views/layouts/footer.php'; ?>
