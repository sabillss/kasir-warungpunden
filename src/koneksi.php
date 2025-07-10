<?php
// Konfigurasi koneksi ke MySQL Railway
$host = 'containers-us-west-65.railway.app'; // Host dari MYSQLHOST
$port = 13262;                                // Port dari MYSQLPORT
$user = 'root';                               // Username dari MYSQLUSER
$pass = 'qyRNBxUsirmkdrSQLYdAzJqHQlGhWkJP';   // Password dari MYSQL_ROOT_PASSWORD
$db   = 'railway';                            // Nama database dari MYSQLDATABASE

// Inisialisasi objek mysqli
$koneksi = new mysqli();
$koneksi->options(MYSQLI_OPT_CONNECT_TIMEOUT, 10); // Set timeout koneksi 10 detik
$koneksi->real_connect($host, $user, $pass, $db, $port);

// Cek koneksi
if ($koneksi->connect_error) {
    error_log("Koneksi MySQL gagal: " . $koneksi->connect_error);
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Set UTF-8 dan timezone
$koneksi->set_charset("utf8");
date_default_timezone_set('Asia/Jakarta');
?>

