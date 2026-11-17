<?php
declare(strict_types=1);

$pilihan = 2;
switch ($pilihan){
    case 1;
        echo "Lihat saldo\n";
        break;
    case 2;
        echo "Transfer\n";
        break;
    case 3;
        echo "Bayar tagihan\n";
        break;
    default;
        echo "Pilihan tidak valid.\n";
}

//Fall-through yang disengaja
$jawab = 'y'":
switch ($jawab) {
    case 'y':
    case 'Y':
        echo "Anda menjawab YA\n";
        break;
    case 'n':
    case 'N':
        echo "Anda menjawab TIDAK\n";
        break;
    default:
        echo "Pilihan tidak dikenali.\n";
}

//Fall-through yang tidak disengaja (bug) karena lupa break
$k=1;
echo "Tanpa Break :\n";
switch ($k) {
    case 1: echo "Satu";
    case 2: echo "Dua";
    case 3: echo "Tiga";
}
echo "\n";