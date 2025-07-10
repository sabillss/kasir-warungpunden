<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'pemilik') {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

$tanggal = $_GET['tanggal'] ?? date('Y-m-d');

// Ambil data menu
$stmt1 = $koneksi->prepare("
  SELECT 
    m.nama AS nama_produk,
    'menu' AS jenis,
    SUM(d.qty) AS total_qty,
    SUM(d.subtotal) AS total_subtotal
  FROM penjualan_detail d
  JOIN transaksi_penjualan t ON d.transaksi_id = t.id
  JOIN menus m ON d.menu_id = m.id
  WHERE DATE(t.tanggal) = ? AND d.jenis = 'menu'
  GROUP BY d.menu_id
");
$stmt1->bind_param("s", $tanggal);
$stmt1->execute();
$result1 = $stmt1->get_result();

// Ambil data konsinyasi (perbaikan nama kolom jadi `nama_barang`)
$stmt2 = $koneksi->prepare("
  SELECT 
    k.nama_barang AS nama_produk,
    'konsinyasi' AS jenis,
    SUM(d.qty) AS total_qty,
    SUM(d.subtotal) AS total_subtotal
  FROM penjualan_detail d
  JOIN transaksi_penjualan t ON d.transaksi_id = t.id
  JOIN konsinyasi k ON d.menu_id = k.id
  WHERE DATE(t.tanggal) = ? AND d.jenis = 'konsinyasi'
  GROUP BY d.menu_id
");
$stmt2->bind_param("s", $tanggal);
$stmt2->execute();
$result2 = $stmt2->get_result();

// Gabungkan hasil dari menu dan konsinyasi
$rows = [];
while ($row = $result1->fetch_assoc()) $rows[] = $row;
while ($row = $result2->fetch_assoc()) $rows[] = $row;
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Detail Transaksi Harian</title>
  <?php include 'pwa-setup.php'; ?>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(to bottom right, #f8ede3, #e9d5c0);
      padding: 30px 20px;
      display: flex;
      justify-content: center;
    }
    .container {
      background: #fff;
      padding: 40px;
      border-radius: 16px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.1);
      max-width: 800px;
      width: 100%;
    }
    h2 {
      font-family: 'Playfair Display', serif;
      font-size: 26px;
      color: #4e342e;
      text-align: center;
      margin-bottom: 20px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      font-size: 14px;
    }
    th, td {
      padding: 12px;
      border: 1px solid #e5d3c5;
      text-align: left;
    }
    th {
      background-color: #f2e3d3;
      color: #4e342e;
    }
    td.right {
      text-align: right;
    }
    td.jenis {
      font-style: italic;
      font-size: 13px;
      color: #8b6b5c;
    }
    .back-button {
      display: block;
      text-align: center;
      margin-top: 30px;
      text-decoration: none;
      color: #6d4c41;
      font-weight: 600;
    }
    .back-button:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
<div class="container">
  <h2>Detail Transaksi - <?= date('l, d M Y', strtotime($tanggal)) ?></h2>

  <table>
    <thead>
      <tr>
        <th>Nama Produk</th>
        <th>Jenis</th>
        <th>Qty</th>
        <th>Subtotal</th>
      </tr>
    </thead>
    <tbody>
      <?php if (count($rows) > 0): ?>
        <?php foreach ($rows as $row): ?>
          <tr>
            <td><?= htmlspecialchars($row['nama_produk']) ?></td>
            <td class="jenis"><?= ucfirst($row['jenis']) ?></td>
            <td class="right"><?= number_format($row['total_qty']) ?></td>
            <td class="right">Rp <?= number_format($row['total_subtotal'], 0, ',', '.') ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="4" style="text-align:center;">Tidak ada transaksi</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <a href="laporan-harian.php" class="back-button">← Kembali ke Laporan Harian</a>
</div>
</body>
</html>
