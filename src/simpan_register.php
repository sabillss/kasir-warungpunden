<?php
include 'koneksi.php'; // koneksi ke database

$username = $_POST['username'];
$password = $_POST['password'];
$confirm  = $_POST['confirm_password'];

// Validasi konfirmasi password
if ($password !== $confirm) {
    echo "<script>alert('Konfirmasi password tidak cocok!'); history.back();</script>";
    exit;
}

// Cek apakah username sudah digunakan
$cek = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username'");
if (mysqli_num_rows($cek) > 0) {
    echo "<script>alert('Username sudah terdaftar!'); history.back();</script>";
    exit;
}

// Simpan akun baru (default role = kasir)
$simpan = mysqli_query($koneksi, "INSERT INTO users (username, password, role) VALUES ('$username', '$password', 'kasir')");

if ($simpan) {
    echo "<script>alert('Pendaftaran berhasil! Silakan login.'); window.location='login.php';</script>";
} else {
    echo "<script>alert('Pendaftaran gagal!'); history.back();</script>";
}
?>
