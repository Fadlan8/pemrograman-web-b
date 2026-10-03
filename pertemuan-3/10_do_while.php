<?php
declare(strict_types=1);

$i = 10;
do {
    echo "Dijalankan sekali walau i = $i\n";
    $i++;
} while ($i <= 5);

//simulasi validasi:ulangi sampai nilai memenuhi syarat
$percobaan = 0;
do {
    $percobaan++;
    $nilai = 30 + $percobaan * 20; //simulasi input berubah
} while ($nilai < 80);
echo "Diperoleh nilai $nilai setelah $percobaan percobaannya\n";