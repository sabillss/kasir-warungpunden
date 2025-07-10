<?php
$host = 'mysql';       // sesuai nama container di docker-compose
$user = 'user';
$pass = 'pass';
$db   = 'kasir';

$koneksi = new mysqli($host, $user, $pass, $db);

if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Set character set to UTF-8
$koneksi->set_charset("utf8");

// Set timezone ke Asia/Jakarta
date_default_timezone_set('Asia/Jakarta');
?>
