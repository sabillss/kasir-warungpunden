<?php 
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

$produk_ids = $_POST['produk_id'];
$quantities = $_POST['qty'];
$jenis_produk = $_POST['jenis'];
$tanggal = $_POST['tanggal'] ?? date('Y-m-d');
$jam = $_POST['jam'] ?? date('H:i:s');
$total = 0;
$items = [];

$valid_item = false;
foreach ($quantities as $qty) {
    if ($qty > 0) {
        $valid_item = true;
        break;
    }
}
if (!$valid_item) {
    echo "<script>alert('Silakan pilih minimal 1 produk.'); history.back();</script>";
    exit;
}

$koneksi->begin_transaction();
try {
    $stmt = $koneksi->prepare("INSERT INTO transaksi_penjualan (tanggal, jam) VALUES (?, ?)");
    $stmt->bind_param("ss", $tanggal, $jam);
    $stmt->execute();
    $transaksi_id = $koneksi->insert_id;

    $detail_stmt = $koneksi->prepare("INSERT INTO penjualan_detail (transaksi_id, menu_id, qty, subtotal, jenis) VALUES (?, ?, ?, ?, ?)");

    for ($i = 0; $i < count($produk_ids); $i++) {
        $id = intval($produk_ids[$i]);
        $qty = intval($quantities[$i]);
        $jenis = $jenis_produk[$i];

        if ($qty <= 0) continue;

        if ($jenis === 'menu') {
            $q = $koneksi->prepare("SELECT nama, harga_jual FROM menus WHERE id=?");
        } else {
            $q = $koneksi->prepare("SELECT nama_barang as nama, harga_jual FROM konsinyasi WHERE id=?");
        }
        $q->bind_param("i", $id);
        $q->execute();
        $data = $q->get_result()->fetch_assoc();
        $nama = $data['nama'];
        $harga = $data['harga_jual'];
        $subtotal = $harga * $qty;
        $total += $subtotal;

        $detail_stmt->bind_param("iiids", $transaksi_id, $id, $qty, $subtotal, $jenis);
        $detail_stmt->execute();

        $items[] = [
            'nama' => $nama,
            'qty' => $qty,
            'harga' => $harga,
            'subtotal' => $subtotal,
            'jenis' => $jenis
        ];
    }

    $koneksi->commit();
} catch (Exception $e) {
    $koneksi->rollback();
    echo "Terjadi kesalahan: " . $e->getMessage();
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Transaksi Berhasil</title>
  <?php include 'pwa-setup.php'; ?>
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: #f8ede3;
      padding: 40px;
    }
    .container {
      max-width: 600px;
      background: #fff;
      margin: auto;
      padding: 30px 40px;
      border-radius: 12px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.1);
      text-align: center;
    }
    .success-alert {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
      padding: 16px;
      margin-bottom: 25px;
      border-radius: 8px;
      font-size: 16px;
    }
    .timestamp {
      font-size: 14px;
      color: #777;
      margin-bottom: 20px;
    }
    ul {
      list-style: none;
      padding: 0;
      margin-bottom: 20px;
      text-align: left;
    }
    ul li {
      padding: 8px 0;
      border-bottom: 1px dashed #ccc;
    }
    .total {
      font-weight: bold;
      font-size: 18px;
      text-align: right;
      color: #2c3e50;
    }
    .btn-group {
      margin-top: 30px;
    }
    .btn {
      background-color: #5a3e36;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 8px;
      margin: 5px;
      text-decoration: none;
    }
    .btn:hover {
      background-color: #3e2b24;
    }
  </style>
</head>
<body>
<div class="container">
  <div class="success-alert">✅ <strong>Transaksi Berhasil Disimpan!</strong></div>
  <div class="timestamp">Waktu: <?= $tanggal ?> <?= $jam ?></div>

  <ul>
    <?php foreach ($items as $item): ?>
      <li>
        <?= $item['qty'] ?>x <?= htmlspecialchars($item['nama']) ?> 
        (Rp <?= number_format($item['harga'], 0, ',', '.') ?>) 
        <?= $item['jenis'] === 'konsinyasi' ? '<em>[Titipan]</em>' : '' ?> -
        Rp <?= number_format($item['subtotal'], 0, ',', '.') ?>
      </li>
    <?php endforeach; ?>
  </ul>

  <p class="total">Total: Rp <?= number_format($total, 0, ',', '.') ?></p>

  <div class="btn-group">
    <a href="transaksi.php" class="btn">+ Transaksi Baru</a>
    <?php if ($_SESSION['role'] === 'kasir'): ?>
      <a href="dashboard-kasir.php" class="btn">🏠 Dashboard Kasir</a>
    <?php else: ?>
      <a href="dashboard-pemilik.php" class="btn">🏠 Dashboard Pemilik</a>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
