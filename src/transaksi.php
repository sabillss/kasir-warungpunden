<?php
session_start();
if (!isset($_SESSION['username']) || 
   ($_SESSION['role'] !== 'pemilik' && $_SESSION['role'] !== 'kasir')) {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

$menus = $koneksi->query("SELECT id, nama, harga_jual, stok FROM menus");
$konsinyasi = $koneksi->query("SELECT id, nama_barang, harga_jual, stok FROM konsinyasi");
$date_now = date('Y-m-d');
$time_now = date('H:i:s');
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Transaksi Penjualan | Warung Punden</title>
  <?php include 'pwa-setup.php'; ?>
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(to bottom right, #f8ede3, #e9d5c0);
      padding: 30px;
    }
    .container {
      max-width: 900px;
      margin: auto;
      background: #fff;
      padding: 40px;
      border-radius: 16px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      margin-bottom: 25px;
      color: #4e342e;
      font-family: 'Playfair Display', serif;
    }
    .timestamp {
      text-align: center;
      margin-bottom: 15px;
      color: #6d4c41;
      font-size: 14px;
    }
    .search-bar {
      text-align: center;
      margin-bottom: 20px;
    }
    .search-bar input {
      padding: 10px;
      width: 300px;
      border: 1px solid #ccc;
      border-radius: 8px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }
    th, td {
      padding: 12px;
      text-align: left;
      border: 1px solid #e0c7aa;
    }
    th {
      background-color: #f2e3d3;
      color: #4e342e;
    }
    tr:nth-child(even) {
      background-color: #fdf9f3;
    }
    input[type="number"] {
      width: 60px;
      padding: 6px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    button {
      margin-top: 20px;
      padding: 10px 24px;
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
    .back-link {
      display: block;
      margin-top: 30px;
      text-align: center;
      color: #6d4c41;
      text-decoration: none;
      font-weight: 600;
    }
    .back-link:hover {
      text-decoration: underline;
    }
    .label-ket {
      font-size: 12px;
      color: #888;
      font-style: italic;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Input Transaksi Penjualan</h2>

    <div class="timestamp">
      Waktu Transaksi: <strong><?= $date_now ?> <?= $time_now ?></strong>
    </div>

    <div class="search-bar">
      <input type="text" id="searchInput" placeholder="Cari menu atau barang titipan...">
    </div>

    <form method="POST" action="simpan-transaksi.php">
      <input type="hidden" name="tanggal" value="<?= $date_now ?>">
      <input type="hidden" name="jam" value="<?= $time_now ?>">

      <table id="menuTable">
        <thead>
          <tr>
            <th>Produk</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Qty</th>
          </tr>
        </thead>
        <tbody>
          <!-- Menu -->
          <?php while ($menu = $menus->fetch_assoc()): ?>
          <tr>
            <td>
              <?= htmlspecialchars($menu['nama']) ?> <span class="label-ket">(Menu)</span>
              <input type="hidden" name="produk_id[]" value="<?= $menu['id'] ?>">
              <input type="hidden" name="jenis[]" value="menu">
            </td>
            <td>Rp <?= number_format($menu['harga_jual'], 0, ',', '.') ?></td>
            <td><?= $menu['stok'] ?></td>
            <td><input type="number" name="qty[]" value="0" min="0"></td>
          </tr>
          <?php endwhile; ?>

          <!-- Konsinyasi -->
          <?php while ($k = $konsinyasi->fetch_assoc()): ?>
          <tr>
            <td>
              <?= htmlspecialchars($k['nama_barang']) ?> <span class="label-ket">(Titipan)</span>
              <input type="hidden" name="produk_id[]" value="<?= $k['id'] ?>">
              <input type="hidden" name="jenis[]" value="konsinyasi">
            </td>
            <td>Rp <?= number_format($k['harga_jual'], 0, ',', '.') ?></td>
            <td><?= $k['stok'] ?></td>
            <td><input type="number" name="qty[]" value="0" min="0"></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>

      <div style="text-align:center;">
        <button type="submit">Simpan Transaksi</button>
      </div>
    </form>

    <a href="<?= $_SESSION['role'] === 'kasir' ? 'dashboard-kasir.php' : 'dashboard-pemilik.php' ?>" class="back-link">← Kembali ke Dashboard</a>
  </div>

  <script>
    const searchInput = document.getElementById("searchInput");
    const rows = document.querySelectorAll("#menuTable tbody tr");

    searchInput.addEventListener("keyup", function () {
      const keyword = this.value.toLowerCase();
      rows.forEach(row => {
        const text = row.querySelector("td").textContent.toLowerCase();
        row.style.display = text.includes(keyword) ? "" : "none";
      });
    });
  </script>
</body>
</html>

</html>
