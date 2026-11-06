<?php
/**
 * CSV export helper with formula-injection protection.
 */
class Csv
{
    public static function download(string $filename, array $headers, array $rows): void
    {
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . self::filename($filename) . '"');
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        $out = fopen('php://output', 'wb');
        // BOM UTF-8 agar Excel membaca karakter dengan benar.
        fwrite($out, "\xEF\xBB\xBF");
        fputcsv($out, $headers, ';', '"', '');
        foreach ($rows as $row) {
            fputcsv($out, array_map([self::class, 'safe'], array_values($row)), ';', '"', '');
        }
        fclose($out);
        exit;
    }
    public static function safe($value): string
    {
        $value = (string) $value;
        if ($value !== '' && in_array($value[0], ['=', '+', '-', '@', "\t", "\r"], true)) {
            return "'" . $value;
        }
        return $value;
    }
    private static function filename(string $filename): string
    {
        $filename = preg_replace('/[^A-Za-z0-9._-]/', '-', $filename);
        return $filename !== '' ? $filename : 'export.csv';
    }
}