<?php
session_start();
include 'koneksi.php'; // file koneksi yang sudah kamu buat

if ($_SESSION['role'] === 'kasir' && isset($_SESSION['jam_login'])) {
    $username = $_SESSION['username'];
    $jam_masuk = date("H:i:s", $_SESSION['jam_login']);
    $jam_keluar = date("H:i:s");
    $tanggal = date("Y-m-d");

    $durasiDetik = time() - $_SESSION['jam_login'];
    $durasiJam = round($durasiDetik / 3600, 2);

    $jam = (int)date("H", $_SESSION['jam_login']);
    if ($jam >= 10 && $jam < 18) {
        $shift = "Shift 1";
    } elseif ($jam >= 18 && $jam <= 23) {
        $shift = "Shift 2";
    } else {
        $shift = "Luar Shift";
    }

    $query = "INSERT INTO log_shift_kasir 
        (username, tanggal, jam_masuk, jam_keluar, durasi_jam, shift)
        VALUES 
        ('$username', '$tanggal', '$jam_masuk', '$jam_keluar', '$durasiJam', '$shift')";

    if (!$koneksi->query($query)) {
        echo "Error saat insert log shift: " . $koneksi->error;
    }
}

session_destroy();
header("Location: login.php");
exit;
?>

