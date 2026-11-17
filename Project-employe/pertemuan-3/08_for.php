<?php
declare(strict_types=1);

//cetak 1..5
for ($i = 1; $i <= 5; $i++) {
    echo "Iterasi ke-$i\n";
}

//jumlah deret 1...100
$total = 0;
for ($i = 1; $i <= 100; $i++) {
    $total += $i;
}
echo "Jumlah deret 1..100 = $total\n";

//faktorial 5
$faktorial = 1;
for ($i = 1; $i <= 5; $i++) {
    $faktorial *= $i;
}
echo "!5 = $faktorial\n";