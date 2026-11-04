<?php
/**
 * Safe image uploads (type, size, and file-name validation).
 */
class Upload
{
    private const ALLOWED = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];
    public static function image(array $file, string $subdir = 'items'): string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            throw new RuntimeException('Tidak ada berkas yang diunggah.');
        }
        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Berkas gagal diunggah (kode ' . (int) $file['error'] . ').');
        }
        if (($file['size'] ?? 0) > UPLOAD_MAX_BYTES) {
            throw new RuntimeException(
                'Ukuran berkas melebihi batas ' . (UPLOAD_MAX_BYTES / 1024 / 1024) . ' MB.'
            );
        }
        if (!is_uploaded_file($file['tmp_name'] ?? '')) {
            throw new RuntimeException('Berkas tidak valid.');
        }
        if (!class_exists('finfo')) {
            throw new RuntimeException('Ekstensi fileinfo belum aktif di server.');
        }
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime  = (string) $finfo->file($file['tmp_name']);
        if (!isset(self::ALLOWED[$mime])) {
            throw new RuntimeException('Format berkas harus JPG, PNG, atau WEBP.');
        }
        if (@getimagesize($file['tmp_name']) === false) {
            throw new RuntimeException('Berkas bukan gambar yang sah.');
        }
        $extension = self::ALLOWED[$mime];
        $filename  = bin2hex(random_bytes(16)) . '.' . $extension;
        $relative  = 'uploads/' . trim($subdir, '/') . '/' . $filename;
        $targetDir = ROOT_PATH . '/public/uploads/' . trim($subdir, '/');
        if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
            throw new RuntimeException('Folder unggahan tidak dapat dibuat.');
        }
        if (!move_uploaded_file($file['tmp_name'], $targetDir . '/' . $filename)) {
            throw new RuntimeException('Gagal menyimpan berkas unggahan.');
        }
        @chmod($targetDir . '/' . $filename, 0644);
        return $relative;
    }
     public static function remove(?string $relative): void
    {
        if (!$relative) {
            return;
        }
        $path = ROOT_PATH . '/public/' . ltrim($relative, '/');
        $real = realpath($path);
        $base = realpath(ROOT_PATH . '/public/uploads');
        if ($real && $base && str_starts_with($real, $base) && is_file($real)) {
            @unlink($real);
        }
    }
}