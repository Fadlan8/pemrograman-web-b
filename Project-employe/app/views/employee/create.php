<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Karyawan</title>
    <style>
        body { font-family : Arial, sans-serif; margin: 30px;}
        .form-group { margin-bottom: 12px; }
        label { display: block; font-weight: bold; margin-bottom: 4px;}
        input[type="text"], input[type="number"] {width: 320px; padding: 6px; }
        .errors { background: #f8d7da; color: #721c24; padding: 10 px; border: 1px solid #f5c6cb; margin-bottom: 15px; width: 34px; }
        .btn {padding: 6px 12 px; text-decoration: none; border: 1px solid #888; background: #eee; color: #000; cursor: pointer; }
    </style>
</head>
<body>
    <h2>Tambah Data Karyawan</h2>
    
    <?php if (!empty($errors)): ?>
        <div class="errors">
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?= htmlspecialchars(BASE_URL . '/employees/store') ?>" method="POST">
        <div class="form-group">
            <label for="nip">NIP</label>
            <input type="text" id="nip" name="nip" value="<?= htmlspecialchars($old['nip'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="name">Nama</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($old['name'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="department">Department</label>
            <input type="text" id="department" name="department" value="<?= htmlspecialchars($old['department'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="position">Position</label>
            <input type="text" id="position" name="position" value="<?= htmlspecialchars($old['position'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="salary">Salary</label>
            <input type="number" id="salary" name="salary" step="1" min="0" value="<?= htmlspecialchars($old['salary'] ?? '') ?>">
        </div>

        <button type="sumbit" class="btn">Simpan</button>
        <a href="<?= htmlspecialchars(BASE_URL . '/employees') ?>" class="btn">Batal</a>
    </form>
</body>
</html>
