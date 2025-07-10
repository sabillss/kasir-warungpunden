<?php 
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role'] === 'pemilik') {
    header("Location: dashboard-pemilik.php");
    exit;
} elseif ($_SESSION['role'] === 'kasir') {
    header("Location: dashboard-kasir.php");
    exit;
} else {
    // Role tidak dikenal
    echo "Akses tidak valid.";
    exit;
}
?>
