<?php
session_start();
include 'koneksi.php';

$username = $_POST['username'];
$password = $_POST['password'];

// Langsung cek tanpa hashing
$sql = "SELECT * FROM users WHERE username=? AND password=?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("ss", $username, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];

    // Redirect sesuai role
    if ($user['role'] === 'pemilik') {
        header("Location: dashboard-pemilik.php");
        exit;
    } else {
        header("Location: dashboard-kasir.php");
        exit;
    }
} else {
    echo "<script>alert('Login gagal! Username atau password salah.'); window.location='login.php';</script>";
    exit;
}
?>
