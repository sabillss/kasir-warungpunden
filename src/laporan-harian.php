<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'pemilik') {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

$bulan = $_GET['bulan'] ?? '';
$tahun = $_GET['tahun'] ?? date('Y');

$where = "WHERE d.qty > 0";
if ($bulan != '') {
    $where .= " AND MONTH(t.tanggal) = " . intval($bulan);
}
if ($tahun != '') {
    $where .= " AND YEAR(t.tanggal) = " . intval($tahun);
}

$query = "
  SELECT 
    DATE(t.tanggal) AS tanggal, 
    SUM(d.subtotal) AS total_pendapatan
  FROM transaksi_penjualan t
  JOIN penjualan_detail d ON t.id = d.transaksi_id
  $where
  GROUP BY DATE(t.tanggal)
  ORDER BY DATE(t.tanggal) DESC
";

$hasil = $koneksi->query($query);
?>

<!DOCTYPE html> 
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Harian | Warung Punden</title>
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
      max-width: 900px;
      width: 100%;
    }
    h2 {
      font-family: 'Playfair Display', serif;
      font-size: 26px;
      color: #4e342e;
      text-align: center;
      margin-bottom: 30px;
    }
    form.filter {
      text-align: center;
      margin-bottom: 30px;
    }
    select {
      padding: 8px 12px;
      border-radius: 8px;
      border: 1px solid #ccc;
      margin-right: 10px;
      font-size: 14px;
    }
    button {
      padding: 8px 16px;
      background-color: #6d4c41;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 600;
    }
    button:hover {
      background-color: #4e342e;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
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
    td a {
      color: #4e342e;
      text-decoration: none;
      font-weight: 500;
    }
    td a:hover {
      text-decoration: underline;
    }
    .back-link {
      display: block;
      text-align: center;
      margin-top: 30px;
      font-size: 15px;
      color: #6d4c41;
      text-decoration: none;
      font-weight: 600;
    }
    .back-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
<div class="container">
  <h2>Laporan Pendapatan Harian</h2>

  <form method="GET" class="filter">
    <label>Bulan:
      <select name="bulan">
        <option value="">Semua</option>
        <?php for ($i = 1; $i <= 12; $i++): ?>
          <option value="<?= $i ?>" <?= $bulan == $i ? 'selected' : '' ?>>
            <?= date('F', mktime(0, 0, 0, $i, 1)) ?>
          </option>
        <?php endfor; ?>
      </select>
    </label>

    <label>Tahun:
      <select name="tahun">
        <?php 
        $thisYear = date('Y');
        for ($y = $thisYear; $y >= $thisYear - 5; $y--): ?>
          <option value="<?= $y ?>" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
        <?php endfor; ?>
      </select>
    </label>
    <button type="submit">Tampilkan</button>
  </form>

  <table>
    <thead>
      <tr>
        <th>Tanggal</th>
        <th>Pendapatan</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($hasil->num_rows > 0): ?>
        <?php while ($row = $hasil->fetch_assoc()): ?>
          <tr>
            <td>
              <a href="laporan-detail-harian.php?tanggal=<?= $row['tanggal'] ?>">
                <?= date('l, d M Y', strtotime($row['tanggal'])) ?>
              </a>
            </td>
            <td>Rp <?= number_format($row['total_pendapatan'], 0, ',', '.') ?></td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="2" style="text-align:center;">Tidak ada data</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <a href="dashboard-pemilik.php" class="back-link">← Kembali ke Dashboard</a>
</div>
</body>
</html>
