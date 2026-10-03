<?php
declare(strict_types=1);

$sudahLogin = true;
$peran = 'admin';

if ($sudahLogin){
    if ($peran === 'admin') {
        echo "Selamat datang, admin. Akses penuh.\n";
    } elseif ($peran === 'operator') {
        echo "Selamat datang, operator. Akses terbatas.\n";
    } else {
        echo "Peran tidak dikenal.\n";
    }
}else {
    echo "Silakan login terlebih dahulu.\n";
}

//alterntif daftar dengan &&
$terverifikasi = true;
$saldo = 120000;
if ($sudahLogin && $terverifikasi && $saldo >= 120000) {
    echo "Transaksi besar di izinkan.\n";
}