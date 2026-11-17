<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Employee Data - Daftar Karyawan</title>
    <style>
        body { font-family : Arial, sans-serif; margin: 30px;}
        table { border-collaps: collapse; width: 100%; margin-top: 15px; }
        th,td { border: 1px solid #acc; padding: 8px 12px; text-align: left; }
        .btn  {padding: 6px 12px; text-decoration: none; border: 1px solid #888; background: #eee; color: #000; cursor: pointer; display: inline-block}
        .btn-danger { background : #f8d7da; border-color: #dc3545; color: #721c24; }
        form.inline { display: inline; }
    </style>
</head>
<body>
    <h2>Daftar Data Karyawan</h2>
    <a href="<?= htmlspecialchars(BASE_URL . '/employees/create') ?>" class="btn">+ Tambah Karyawan</a>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>NIP</th>
                    <th>Nama</th>
                    <th>Departement</th>
                    <th>Position</th>
                    <th>Salary</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($employees)): ?>
                    <tr>
                        <td colspan="7">Belum ada data karyawan.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($employees as $i => $emp): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($emp['nip']) ?></td>
                            <td><?= htmlspecialchars($emp['name']) ?></td>
                            <td><?= htmlspecialchars($emp['department']) ?></td>
                            <td><?= htmlspecialchars($emp['position']) ?></td>
                            <td>Rp <?= number_format((float) $emp['salary'], 0, ',', '.') ?></td>
                            <td>
                                <a href="<?= htmlspecialchars(BASE_URL . '/employees/edit?id=' . $emp['id']) ?>" class="btn">ubah</a>
                                <form action="<?= htmlspecialchars(BASE_URL . '/employees/delete') ?>" method= "POST" class="inline"
                                    onsubmit="return confirm('Yakin ingin menghapus karyawan <?= htmlspecialchars($emp['name'], ENT_QUOTES) ?> (<?= htmlspecialchars($emp['nip'], ENT_QUOTES) ?>)?');">
                                    <input type="hidden" name="id" value="<?= (int) $emp['id'] ?>">
                                    <button type="submit" class="btn btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </body>
</html>
