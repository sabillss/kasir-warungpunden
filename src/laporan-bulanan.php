<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'pemilik') {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

$bulan = $_GET['bulan'] ?? date('n');
$tahun = $_GET['tahun'] ?? date('Y');

// Ringkasan total
$stmt = $koneksi->prepare("
    SELECT 
        SUM(subtotal) AS total_penjualan,
        SUM(qty) AS total_qty
    FROM penjualan_detail d
    JOIN transaksi_penjualan t ON d.transaksi_id = t.id
    WHERE MONTH(t.tanggal) = ? AND YEAR(t.tanggal) = ?
");
$stmt->bind_param("ii", $bulan, $tahun);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

$total_penjualan = $result['total_penjualan'] ?? 0;
$total_qty = $result['total_qty'] ?? 0;

// Rincian per produk
$stmt_detail = $koneksi->prepare("
    SELECT 
        CASE d.jenis
            WHEN 'menu' THEN m.nama
            WHEN 'konsinyasi' THEN k.nama_barang
            ELSE 'Tidak diketahui'
        END AS nama_produk,
        d.jenis,
        SUM(d.qty) AS total_qty,
        SUM(d.subtotal) AS total_subtotal
    FROM penjualan_detail d
    JOIN transaksi_penjualan t ON d.transaksi_id = t.id
    LEFT JOIN menus m ON d.jenis = 'menu' AND d.menu_id = m.id
    LEFT JOIN konsinyasi k ON d.jenis = 'konsinyasi' AND d.menu_id = k.id
    WHERE MONTH(t.tanggal) = ? AND YEAR(t.tanggal) = ?
    GROUP BY d.jenis, d.menu_id
    ORDER BY d.jenis, nama_produk
");
$stmt_detail->bind_param("ii", $bulan, $tahun);
$stmt_detail->execute();
$detail_result = $stmt_detail->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Penjualan Bulanan | Warung Punden</title>
  <?php include 'pwa-setup.php'; ?>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(to bottom right, #f8ede3, #e9d5c0);
      padding: 30px;
      display: flex;
      justify-content: center;
    }
    .container {
      background: #fff;
      padding: 40px;
      border-radius: 16px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.1);
      max-width: 900px;
      width: 100%;
    }
    h1, h2 {
      text-align: center;
      font-family: 'Playfair Display', serif;
      color: #4e342e;
    }
    .summary {
      background: #fef6f2;
      padding: 20px;
      border-radius: 12px;
      margin-bottom: 30px;
      text-align: center;
    }
    .summary h3 {
      margin: 0;
      font-size: 20px;
    }
    .summary p {
      font-size: 18px;
      font-weight: bold;
      margin: 5px 0;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }
    th, td {
      padding: 10px;
      border: 1px solid #e5d3c5;
      text-align: left;
    }
    th {
      background: #f2e3d3;
      color: #4e342e;
    }
    td.right {
      text-align: right;
    }
    .jenis {
      font-style: italic;
      font-size: 13px;
      color: #8b6b5c;
    }
    .filter-form {
      text-align: center;
      margin-bottom: 20px;
    }
    select, button {
      padding: 8px 12px;
      margin: 0 5px;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 14px;
    }
    button {
      background: #6d4c41;
      color: #fff;
      font-weight: bold;
      cursor: pointer;
    }
    button:hover {
      background: #4e342e;
    }
    .back-button {
      display: block;
      text-align: center;
      margin-top: 30px;
      text-decoration: none;
      color: #6d4c41;
      font-weight: bold;
    }
    .back-button:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
<div class="container">
  <h1>Laporan Penjualan Bulan <?= date("F", mktime(0,0,0,$bulan,1)) ?> <?= $tahun ?></h1>

  <form method="get" class="filter-form">
    <select name="bulan">
      <?php for ($b=1; $b<=12; $b++): ?>
        <option value="<?= $b ?>" <?= $b == $bulan ? 'selected' : '' ?>>
          <?= date("F", mktime(0, 0, 0, $b, 1)) ?>
        </option>
      <?php endfor; ?>
    </select>
    <select name="tahun">
      <?php for ($t = date('Y') - 5; $t <= date('Y'); $t++): ?>
        <option value="<?= $t ?>" <?= $t == $tahun ? 'selected' : '' ?>><?= $t ?></option>
      <?php endfor; ?>
    </select>
    <button type="submit">Tampilkan</button>
  </form>

  <!-- Tombol Export -->
<form method="get" action="export-penjualan.php" style="text-align:center; margin-bottom: 30px;">
  <input type="hidden" name="bulan" value="<?= $bulan ?>">
  <input type="hidden" name="tahun" value="<?= $tahun ?>">
  <button type="submit" class="filter-button export-button">Export ke Excel</button>
</form>

  <div class="summary">
    <h3>📦 Total Item Terjual</h3>
    <p><?= number_format($total_qty) ?> item</p>
    <h3>💰 Total Penjualan</h3>
    <p>Rp <?= number_format($total_penjualan, 0, ',', '.') ?></p>
  </div>

  <h2>📋 Rincian Penjualan per Produk</h2>
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
      <?php if ($detail_result->num_rows > 0): ?>
        <?php while ($row = $detail_result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['nama_produk']) ?></td>
            <td class="jenis"><?= ucfirst($row['jenis']) ?></td>
            <td class="right"><?= number_format($row['total_qty']) ?></td>
            <td class="right">Rp <?= number_format($row['total_subtotal'], 0, ',', '.') ?></td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="4" style="text-align:center;">Tidak ada data</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <a href="dashboard-pemilik.php" class="back-button">← Kembali ke Dashboard</a>
</div>
</body>
</html>
