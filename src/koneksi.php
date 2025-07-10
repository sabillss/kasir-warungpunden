<?php
$host = 'gondola.proxy.rlwy.net'; // ini host dari public network
$port = 13262;
$user = 'root';
$pass = 'qyRNBxUsirmKdrSQLYdAzJqHQIGhWkJP';  // dari MYSQL_ROOT_PASSWORD
$db   = 'railway';

$koneksi = new mysqli($host, $user, $pass, $db, $port);

if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

$koneksi->set_charset("utf8");
date_default_timezone_set('Asia/Jakarta');
?>
