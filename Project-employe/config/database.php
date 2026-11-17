<?php
$host = '127.0.0.1';
$dbname = 'db_employee_data';
$username = 'root';
$password = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try{
    return new PDO($dsn, $username, $password, $options);
}catch(PDOException $e){
    die ('Koneksi Database Gagal: ' . htmlspecialchars($e->getMessage()));
}