<?php
$host = 'containers-us-west-65.railway.app';  // public host dari MySQL di Railway
$port = 13262;
$user = 'root';
$pass = 'qyRNBxUsirmKdrSQLYdAzJqHQIGhWkJP';  // pastikan sesuai persis (huruf besar/kecil sensitif)
$db   = 'railway';

$koneksi = new mysqli($host, $user, $pass, $db, $port);

if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

$koneksi->set_charset("utf8");
date_default_timezone_set('Asia/Jakarta');
?>
