<?php
$host = 'mysql.railway.internal';  // Ganti sesuai MYSQLHOST
$port = '3306';                              // Ganti sesuai MYSQLPORT
$user = 'root';                               // Ganti sesuai MYSQLUSER
$pass = 'qyRNBxUsirmKdrSQLYdAzJqHQIGhWkJP';    // Ganti sesuai MYSQL_ROOT_PASSWORD
$db   = 'railway';                            // Ganti sesuai MYSQLDATABASE

$koneksi = new mysqli($host, $user, $pass, $db, $port);

if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

$koneksi->set_charset("utf8");
date_default_timezone_set('Asia/Jakarta');
?>
