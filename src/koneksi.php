<?php
$host = 'mysql.railway.internal';
$port = 3306;
$user = 'root';
$pass = 'qyRNBxUsirmKdrSQLYdAzJqHQlGhWkJP';
$db   = 'railway';

$koneksi = new mysqli();
$koneksi->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5); // waktu timeout dikurangi agar ga lemot
$koneksi->real_connect($host, $user, $pass, $db, $port);

if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

$koneksi->set_charset("utf8");
date_default_timezone_set('Asia/Jakarta');
?>
