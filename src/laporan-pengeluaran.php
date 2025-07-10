<?php  
session_start();
if ($_SESSION['role'] !== 'pemilik') {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

// Tangani Edit Pengeluaran
if (isset($_POST['edit_pengeluaran']) && $_POST['pengeluaran_id']) {
    $id = $_POST['pengeluaran_id'];
    $kategori = $_POST['kategori'];
    $keterangan = $_POST['keterangan'];
    $nominal = $_POST['nominal'];

    $stmt = $koneksi->prepare("UPDATE pengeluaran SET kategori = ?, keterangan = ?, nominal = ? WHERE id = ?");
    $stmt->bind_param("ssii", $kategori, $keterangan, $nominal, $id);
    $stmt->execute();

    header("Location: laporan-pengeluaran.php?" . http_build_query($_GET));
    exit;
}

// Tangani Hapus Pengeluaran
if (isset($_POST['hapus_pengeluaran']) && $_POST['pengeluaran_id']) {
    $id = $_POST['pengeluaran_id'];

    $stmt = $koneksi->prepare("DELETE FROM pengeluaran WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: laporan-pengeluaran.php?" . http_build_query($_GET));
    exit;
}

// Ambil parameter
$dari = $_GET['dari'] ?? '';
$sampai = $_GET['sampai'] ?? '';
$mode = $_GET['mode'] ?? 'harian';
$bulan = $_GET['bulan'] ?? date('n');
$tahun = $_GET['tahun'] ?? date('Y');

$pengeluaran_data = [];
$penjualan_map = [];
$pengeluaran_detail = [];

if ($mode === 'harian') {
    $query_pengeluaran = "SELECT id, tanggal, kategori, keterangan, nominal FROM pengeluaran";
    $res_pengeluaran = $koneksi->query($query_pengeluaran);
    while ($row = $res_pengeluaran->fetch_assoc()) {
        $tanggal = $row['tanggal'];
        if (!isset($pengeluaran_data[$tanggal])) {
            $pengeluaran_data[$tanggal] = 0;
            $pengeluaran_detail[$tanggal] = [];
        }
        $pengeluaran_data[$tanggal] += $row['nominal'];
        $pengeluaran_detail[$tanggal][] = $row;
    }

    $query_penjualan = "SELECT DATE(tp.tanggal) AS tanggal, SUM(pd.subtotal) AS total 
                        FROM transaksi_penjualan tp 
                        JOIN penjualan_detail pd ON tp.id = pd.transaksi_id 
                        GROUP BY DATE(tp.tanggal)";
    $res_penjualan = $koneksi->query($query_penjualan);
    while ($row = $res_penjualan->fetch_assoc()) {
        $penjualan_map[$row['tanggal']] = $row['total'];
    }
} else {
    $key = sprintf('%04d-%02d', $tahun, $bulan);
    $query_pengeluaran = "SELECT MONTH(tanggal) as bulan, YEAR(tanggal) as tahun, 
                                 SUM(nominal) as total_pengeluaran 
                          FROM pengeluaran 
                          WHERE MONTH(tanggal) = $bulan AND YEAR(tanggal) = $tahun 
                          GROUP BY bulan, tahun";
    $res_pengeluaran = $koneksi->query($query_pengeluaran);
    while ($row = $res_pengeluaran->fetch_assoc()) {
        $pengeluaran_data[$key] = $row['total_pengeluaran'];
    }

    $query_penjualan = "SELECT MONTH(tp.tanggal) AS bulan, YEAR(tp.tanggal) AS tahun, 
                               SUM(pd.subtotal) AS total 
                        FROM transaksi_penjualan tp 
                        JOIN penjualan_detail pd ON tp.id = pd.transaksi_id 
                        WHERE MONTH(tp.tanggal) = $bulan AND YEAR(tp.tanggal) = $tahun 
                        GROUP BY bulan, tahun";
    $res_penjualan = $koneksi->query($query_penjualan);
    while ($row = $res_penjualan->fetch_assoc()) {
        $penjualan_map[$key] = $row['total'];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Pengeluaran | Warung Punden</title>
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
      max-width: 1000px;
      width: 100%;
    }
    h2 {
      font-family: 'Playfair Display', serif;
      font-size: 26px;
      color: #4e342e;
      text-align: center;
      margin-bottom: 10px;
    }
    .description {
      font-size: 16px;
      color: #5d4037;
      text-align: center;
      margin-bottom: 30px;
    }
    form {
      margin-bottom: 20px;
      text-align: center;
    }
    select, input[type="date"], input[type="number"], input[type="text"] {
      padding: 8px 12px;
      border-radius: 8px;
      border: 1px solid #ccc;
      margin: 4px;
      font-size: 14px;
    }
    input[type="submit"], a.button, button {
      text-decoration: none;
      background-color: #6d4c41;
      color: #fff;
      padding: 8px 16px;
      border-radius: 8px;
      font-weight: 600;
      display: inline-block;
      margin: 5px;
      border: none;
      cursor: pointer;
    }
    input[type="submit"]:hover, a.button:hover, button:hover {
      background-color: #4e342e;
    }
    .export-button {
      background-color: #388e3c;
    }
    .report-box {
      background-color: #fdf6f0;
      padding: 25px;
      border-radius: 12px;
      margin-bottom: 30px;
      box-shadow: 0 4px 14px rgba(0,0,0,0.05);
      text-align: center;
    }
    .report-box h3 {
      font-size: 20px;
      color: #3e2723;
      margin-bottom: 5px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    th, td {
      padding: 10px;
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
    .rugi {
      background-color: #ffebee;
      color: #c62828;
      font-weight: bold;
    }
    .untung {
      background-color: #e8f5e9;
      color: #2e7d32;
      font-weight: bold;
    }
    .detail {
      background-color: #fefcf9;
      font-size: 14px;
    }
  </style>
</head>
<body>
<div class="container">
  <h2>Laporan Pengeluaran</h2>
  <p class="description">Perbandingan pengeluaran dan penjualan berdasarkan <?= $mode ?>.</p>

  <form method="GET">
    <select name="mode">
      <option value="harian" <?= $mode === 'harian' ? 'selected' : '' ?>>Harian</option>
      <option value="bulanan" <?= $mode === 'bulanan' ? 'selected' : '' ?>>Bulanan</option>
    </select>
    <?php if ($mode === 'harian'): ?>
      <input type="date" name="dari" value="<?= htmlspecialchars($dari) ?>">
      <input type="date" name="sampai" value="<?= htmlspecialchars($sampai) ?>">
    <?php else: ?>
      <input type="number" name="bulan" min="1" max="12" value="<?= htmlspecialchars($bulan) ?>">
      <input type="number" name="tahun" value="<?= htmlspecialchars($tahun) ?>">
    <?php endif; ?>
    <input type="submit" value="Tampilkan">
    <a href="laporan-pengeluaran.php" class="button">Reset</a>
    <a href="input-pengeluaran.php" class="button">+ Tambah Pengeluaran</a>
  </form>

  <form method="get" action="export-pengeluaran.php" style="text-align:center; margin-bottom: 30px;">
    <input type="hidden" name="mode" value="<?= htmlspecialchars($mode) ?>">
    <?php if ($mode === 'harian'): ?>
      <input type="hidden" name="dari" value="<?= htmlspecialchars($dari) ?>">
      <input type="hidden" name="sampai" value="<?= htmlspecialchars($sampai) ?>">
    <?php else: ?>
      <input type="hidden" name="bulan" value="<?= htmlspecialchars($bulan) ?>">
      <input type="hidden" name="tahun" value="<?= htmlspecialchars($tahun) ?>">
    <?php endif; ?>
    <button type="submit" class="export-button">Export ke Excel</button>
  </form>

  <div class="report-box">
    <h3>💸 Total Pengeluaran</h3>
    <p><strong>Rp <?= number_format(array_sum($pengeluaran_data), 0, ',', '.') ?></strong></p>
  </div>

  <table>
    <thead>
      <tr>
        <th><?= $mode === 'harian' ? 'Tanggal' : 'Periode' ?></th>
        <th>Pengeluaran</th>
        <th>Penjualan</th>
        <th>Selisih</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($pengeluaran_data as $key => $pengeluaran):
          $penjualan = $penjualan_map[$key] ?? 0;
          $selisih = $penjualan - $pengeluaran;
          $status = $selisih >= 0 ? 'Untung' : 'Rugi';
          $class = $selisih >= 0 ? 'untung' : 'rugi'; ?>
          <tr class="<?= $class ?>">
            <td><?= $mode === 'harian' ? date('d M Y', strtotime($key)) : date('F Y', strtotime($key.'-01')) ?></td>
            <td class="right">Rp <?= number_format($pengeluaran, 0, ',', '.') ?></td>
            <td class="right">Rp <?= number_format($penjualan, 0, ',', '.') ?></td>
            <td class="right">Rp <?= number_format($selisih, 0, ',', '.') ?></td>
            <td><?= $status ?></td>
          </tr>
          <?php if ($mode === 'harian' && isset($pengeluaran_detail[$key])):
              foreach ($pengeluaran_detail[$key] as $det): ?>
                <tr class="detail">
                  <form method="post">
                    <td colspan="2">
                      <input type="hidden" name="pengeluaran_id" value="<?= $det['id'] ?>">
                      <input type="text" name="kategori" value="<?= htmlspecialchars($det['kategori']) ?>" style="width: 100px;">
                      <input type="text" name="keterangan" value="<?= htmlspecialchars($det['keterangan']) ?>" style="width: 160px;">
                    </td>
                    <td colspan="2">
                      <input type="number" name="nominal" value="<?= $det['nominal'] ?>" style="width: 100px;">
                    </td>
                    <td>
                      <button type="submit" name="edit_pengeluaran">💾</button>
                      <button type="submit" name="hapus_pengeluaran" onclick="return confirm('Yakin hapus?')">🗑️</button>
                    </td>
                  </form>
                </tr>
              <?php endforeach; ?>
          <?php endif; ?>
      <?php endforeach; ?>
    </tbody>
  </table>

  <a href="dashboard-pemilik.php" style="display:block;margin-top:30px;text-align:center;color:#6d4c41;text-decoration:none;">← Kembali ke Dashboard</a>
</div>
</body>
</html>
