<?php
session_start();
if ($_SESSION['role'] !== 'pemilik') {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal = $_POST['tanggal'];
    $kategori = $_POST['kategori'];
    $keterangan = $_POST['keterangan'];
    $nominal = $_POST['nominal'];

    $stmt = $koneksi->prepare("INSERT INTO pengeluaran (tanggal, kategori, keterangan, nominal) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $tanggal, $kategori, $keterangan, $nominal);

    if ($stmt->execute()) {
        echo "<script>alert('Pengeluaran berhasil ditambahkan!'); window.location.href='laporan-pengeluaran.php';</script>";
    } else {
        echo "Gagal menyimpan: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Pengeluaran | Warung Punden</title>
    <?php include 'pwa-setup.php'; ?>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(to bottom right, #f8ede3, #e9d5c0);
            margin: 0;
            padding: 30px 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background: #fff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 100%;
        }
        h2 {
            font-family: 'Playfair Display', serif;
            color: #4e342e;
            text-align: center;
            margin-bottom: 30px;
        }
        label {
            font-weight: 600;
            color: #5d4037;
            display: block;
            margin-top: 15px;
        }
        input[type="text"],
        input[type="date"],
        input[type="number"] {
            width: 100%;
            padding: 10px 14px;
            margin-top: 5px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 14px;
            box-sizing: border-box;
        }
        input[type="submit"],
        a.button {
            background-color: #6d4c41;
            color: white;
            border: none;
            padding: 10px 20px;
            margin-top: 25px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        input[type="submit"]:hover,
        a.button:hover {
            background-color: #4e342e;
        }
        .button-group {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }
        footer {
            text-align: center;
            margin-top: 40px;
            color: #6d4c41;
            font-size: 14px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Tambah Pengeluaran</h2>
    <form method="post">
        <label for="tanggal">Tanggal:</label>
        <input type="date" name="tanggal" required>

        <label for="kategori">Kategori:</label>
        <input type="text" name="kategori" placeholder="Contoh: Belanja Bahan" required>

        <label for="keterangan">Keterangan:</label>
        <input type="text" name="keterangan" placeholder="Contoh: Minyak goreng, cabe, dll" required>

        <label for="nominal">Nominal (Rp):</label>
        <input type="number" name="nominal" placeholder="Contoh: 50000" required>

        <div class="button-group">
            <input type="submit" value="💾 Simpan">
            <a href="laporan-pengeluaran.php" class="button">← Kembali</a>
        </div>
    </form>
</div>

<footer>
    <p>&copy; <?= date('Y') ?> Warung Punden. All rights reserved.</p>
</footer>
</body>
</html>
