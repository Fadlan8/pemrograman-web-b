<?php $value = fn(string $key, $default = '') => e($category[$key] ?? $default); ?>
    <div class="mb-3">
        <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
         <input type="text" id="name" name="name" class="form-control" maxlength="100" required
            value="<?=$value('name') ?>">
    </div>
    <div class="mb-3">
            <label for="description" class="form-label">Deskripsi</label>
          <textarea id="description" name="description" class="form-control" rows="3"><?= e($category['description'] ?? '') ?></textarea>
    </div>