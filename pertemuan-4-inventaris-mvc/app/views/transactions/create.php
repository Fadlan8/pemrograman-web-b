<?php require ROOT_PATH . '/app/views/layouts/header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 fw-bold">Catat Transaksi</h3>
        <small class="text-muted">Barang masuk atau keluar</small>
    </div>
    <a href="<?= BASEURL ?>/transactions" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="<?= BASEURL ?>/transactions/store" method="post">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Pilih Barang <span class="text-danger">*</span></label>
                    <select name="item_id" class="form-select" required>
                        <option value="">- Pilih Barang -</option>
                        <?php foreach ($items as $item): ?>
                            <option value="<?= e($item['id']) ?>">
                                [<?= e($item['code']) ?>] <?= e($item['name']) ?> (Stok: <?= e($item['quantity']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jenis Transaksi <span class="text-danger">*</span></label>
                    <select name="type" class="form-select" required>
                        <option value="IN">Masuk (Penambahan Stok)</option>
                        <option value="OUT">Keluar (Pengurangan Stok)</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                    <input type="number" name="quantity" class="form-control" min="1" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Catatan</label>
                    <textarea name="notes" rows="2" class="form-control" placeholder="Misal: Pembelian dari supplier X atau Diambil oleh divisi Y"></textarea>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Transaksi</button>
            </div>
        </form>
    </div>
</div>
<?php require ROOT_PATH . '/app/views/layouts/footer.php'; ?>