<?php
$host = 'containers-us-west-65.railway.app'; // ✅ ganti ke host publik dari MYSQLHOST
$port = 13262;                               // ✅ dari MYSQLPORT
$user = 'root';                              // dari MYSQLUSER
$pass = 'qyRNBxUsirmkdrSQLYdAzJqHQlGhWkJP';  // ✅ perhatikan huruf besar kecil
$db   = 'railway';                           // dari MYSQLDATABASE

$koneksi = new mysqli($host, $user, $pass, $db, $port);

if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

$koneksi->set_charset("utf8");
date_default_timezone_set('Asia/Jakarta');
?>
