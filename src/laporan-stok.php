<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'pemilik') {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

$notif = $_SESSION['notif'] ?? '';
unset($_SESSION['notif']);

$limit = 5;
$page = max(1, intval($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

$filter_menu = $_GET['cari_menu'] ?? '';
$filter_bahan = $_GET['cari_bahan'] ?? '';
$filter_konsinyasi = $_GET['cari_konsinyasi'] ?? '';

$menus = $koneksi->query("SELECT * FROM menus WHERE nama LIKE '%$filter_menu%' LIMIT $limit OFFSET $offset");
$total_menu = $koneksi->query("SELECT COUNT(*) as total FROM menus WHERE nama LIKE '%$filter_menu%'")->fetch_assoc()['total'];

$bahan = $koneksi->query("SELECT * FROM bahan_baku WHERE nama LIKE '%$filter_bahan%' LIMIT $limit OFFSET $offset");
$total_bahan = $koneksi->query("SELECT COUNT(*) as total FROM bahan_baku WHERE nama LIKE '%$filter_bahan%'")->fetch_assoc()['total'];

$konsinyasi = $koneksi->query("SELECT * FROM konsinyasi WHERE nama_barang LIKE '%$filter_konsinyasi%' LIMIT $limit OFFSET $offset");
$total_konsinyasi = $koneksi->query("SELECT COUNT(*) as total FROM konsinyasi WHERE nama_barang LIKE '%$filter_konsinyasi%'")->fetch_assoc()['total'];

if (isset($_POST['tambah_menu'])) {
    $stmt = $koneksi->prepare("INSERT INTO menus (nama, stok, harga_jual) VALUES (?, ?, ?)");
    $stmt->bind_param("sii", $_POST['nama_menu'], $_POST['stok'], $_POST['harga_jual']);
    $stmt->execute(); 
    $_SESSION['notif'] = 'Menu berhasil ditambahkan!';
    header("Location: laporan-stok.php"); 
    exit;
}
if (isset($_POST['edit_menu'])) {
    $stmt = $koneksi->prepare("UPDATE menus SET nama=?, stok=?, harga_jual=? WHERE id=?");
    $stmt->bind_param("siii", $_POST['nama'], $_POST['stok'], $_POST['harga_jual'], $_POST['id']);
    $stmt->execute();
    $_SESSION['notif'] = 'Menu berhasil diperbarui!';
    header("Location: laporan-stok.php");
    exit;
}

if (isset($_POST['tambah_bahan'])) {
    $stmt = $koneksi->prepare("INSERT INTO bahan_baku (nama, stok, satuan) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $_POST['nama_bahan'], $_POST['stok'], $_POST['satuan']);
    $stmt->execute(); 
    $_SESSION['notif'] = 'Bahan baku berhasil ditambahkan!';
    header("Location: laporan-stok.php"); 
    exit;
}
if (isset($_POST['edit_bahan'])) {
    $stmt = $koneksi->prepare("UPDATE bahan_baku SET nama=?, stok=?, satuan=? WHERE id=?");
    $stmt->bind_param("sisi", $_POST['nama'], $_POST['stok'], $_POST['satuan'], $_POST['id']);
    $stmt->execute();
    $_SESSION['notif'] = 'Bahan baku berhasil diperbarui!';
    header("Location: laporan-stok.php");
    exit;
}

if (isset($_POST['tambah_konsinyasi'])) {
    $stmt = $koneksi->prepare("INSERT INTO konsinyasi (nama_barang, stok, harga_titip, harga_jual) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siii", $_POST['nama_barang'], $_POST['stok'], $_POST['harga_titip'], $_POST['harga_jual']);
    $stmt->execute(); 
    $_SESSION['notif'] = 'Barang konsinyasi berhasil ditambahkan!';
    header("Location: laporan-stok.php"); 
    exit;
}
if (isset($_POST['edit_konsinyasi'])) {
    $stmt = $koneksi->prepare("UPDATE konsinyasi SET nama_barang=?, stok=?, harga_titip=?, harga_jual=? WHERE id=?");
    $stmt->bind_param("siiii", $_POST['nama_barang'], $_POST['stok'], $_POST['harga_titip'], $_POST['harga_jual'], $_POST['id']);
    $stmt->execute();
    $_SESSION['notif'] = 'Barang konsinyasi berhasil diperbarui!';
    header("Location: laporan-stok.php");
    exit;
}
?>

<?php if (!empty($notif)): ?>
<div id="popupNotif" style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: #d7baa7; color: #4e342e; padding: 20px 30px; border-radius: 10px; font-weight: bold; text-align: center; box-shadow: 0 4px 12px rgba(0,0,0,0.2); z-index: 9999;">
  <?= htmlspecialchars($notif) ?><br><br>
  <button onclick="document.getElementById('popupNotif').remove()" style="margin-top: 10px; padding: 8px 16px; background: #6d4c41; color: white; border: none; border-radius: 6px; cursor: pointer;">OK</button>
</div>
<?php endif; ?>

<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Stok | Warung Punden</title>
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
      max-width: 960px;
      width: 100%;
    }
    h2 {
      font-family: 'Playfair Display', serif;
      font-size: 24px;
      color: #4e342e;
      margin-top: 30px;
      margin-bottom: 20px;
    }
    .tab-buttons {
      text-align: center;
      margin-bottom: 20px;
    }
    .tab-button {
      padding: 10px 20px;
      margin: 0 6px;
      background-color: #d7baa7;
      border: none;
      border-radius: 6px;
      font-weight: bold;
      color: #4e342e;
      cursor: pointer;
    }
    .tab-button.active {
      background-color: #6d4c41;
      color: #fff;
    }
    .tab-content {
      display: none;
    }
    .tab-content.active {
      display: block;
    }
    input, select {
      padding: 8px;
      margin: 4px;
      border-radius: 6px;
      border: 1px solid #ccc;
    }
    button {
      padding: 8px 14px;
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
      margin-top: 12px;
    }
    th, td {
      padding: 10px;
      border: 1px solid #e5d3c5;
    }
    th {
      background-color: #f2e3d3;
      color: #4e342e;
    }
    .pagination {
      margin-top: 10px;
      text-align: center;
    }
    .pagination a {
      display: inline-block;
      padding: 6px 10px;
      margin: 2px;
      border: 1px solid #ccc;
      border-radius: 5px;
      background: #eee;
      color: #4e342e;
      text-decoration: none;
    }
    .pagination a:hover {
      background: #ccc;
    }
    .back-link {
      display: inline-block;
      margin-bottom: 20px;
      text-decoration: none;
      font-weight: bold;
      color: #6d4c41;
    }
  </style>
</head>
<body>
<div class="container">
  <a href="dashboard-pemilik.php" class="back-link">← Kembali ke Dashboard</a>
  <h2>Laporan Stok</h2>

  <div class="tab-buttons">
    <button class="tab-button active" onclick="showTab('menu')">🍽️ Menu</button>
    <button class="tab-button" onclick="showTab('bahan')">🧂 Bahan Baku</button>
    <button class="tab-button" onclick="showTab('konsinyasi')">📦 Konsinyasi</button>
  </div>

  <!-- MENU -->
  <div class="tab-content active" id="tab-menu">
    <form method="post"><input name="nama_menu" placeholder="Nama Menu"><input type="number" name="stok" placeholder="Stok"><input type="number" name="harga_jual" placeholder="Harga Jual"><button name="tambah_menu">Tambah</button></form>
    <form method="get"><input name="cari_menu" value="<?= htmlspecialchars($filter_menu) ?>" placeholder="Cari Menu..."><button>Cari</button></form>
    <table><tr><th>Nama</th><th>Stok</th><th>Harga</th><th>Aksi</th></tr>
    <?php while ($m = $menus->fetch_assoc()): ?>
    <tr><form method="post"><td><input name="nama" value="<?= $m['nama'] ?>"></td><td><input type="number" name="stok" value="<?= $m['stok'] ?>"></td><td><input type="number" name="harga_jual" value="<?= $m['harga_jual'] ?>"></td><td><input type="hidden" name="id" value="<?= $m['id'] ?>"><button name="edit_menu">💾</button></td></form></tr>
    <?php endwhile; ?></table>
    <div class="pagination">
      <?php for ($i = 1; $i <= ceil($total_menu / $limit); $i++): ?>
        <a href="?page=<?= $i ?>&cari_menu=<?= urlencode($filter_menu) ?>"><?= $i ?></a>
      <?php endfor; ?>
    </div>
  </div>

  <!-- BAHAN BAKU -->
  <div class="tab-content" id="tab-bahan">
    <form method="post"><input name="nama_bahan" placeholder="Nama Bahan"><input type="number" name="stok" placeholder="Stok"><input name="satuan" placeholder="Satuan"><button name="tambah_bahan">Tambah</button></form>
    <form method="get"><input name="cari_bahan" value="<?= htmlspecialchars($filter_bahan) ?>" placeholder="Cari Bahan Baku..."><button>Cari</button></form>
    <table><tr><th>Nama</th><th>Stok</th><th>Satuan</th><th>Aksi</th></tr>
    <?php while ($b = $bahan->fetch_assoc()): ?>
    <tr><form method="post"><td><input name="nama" value="<?= $b['nama'] ?>"></td><td><input type="number" name="stok" value="<?= $b['stok'] ?>"></td><td><input name="satuan" value="<?= $b['satuan'] ?>"></td><td><input type="hidden" name="id" value="<?= $b['id'] ?>"><button name="edit_bahan">💾</button></td></form></tr>
    <?php endwhile; ?></table>
    <div class="pagination">
      <?php for ($i = 1; $i <= ceil($total_bahan / $limit); $i++): ?>
        <a href="?page=<?= $i ?>&cari_bahan=<?= urlencode($filter_bahan) ?>"><?= $i ?></a>
      <?php endfor; ?>
    </div>
  </div>

  <!-- KONSINYASI -->
  <div class="tab-content" id="tab-konsinyasi">
    <form method="post"><input name="nama_barang" placeholder="Nama Barang"><input type="number" name="stok" placeholder="Stok"><input type="number" name="harga_titip" placeholder="Harga Titip"><input type="number" name="harga_jual" placeholder="Harga Jual"><button name="tambah_konsinyasi">Tambah</button></form>
    <form method="get"><input name="cari_konsinyasi" value="<?= htmlspecialchars($filter_konsinyasi) ?>" placeholder="Cari Barang Titipan..."><button>Cari</button></form>
    <table><tr><th>Nama</th><th>Stok</th><th>Harga Titip</th><th>Harga Jual</th><th>Untung</th><th>Aksi</th></tr>
    <?php while ($k = $konsinyasi->fetch_assoc()): ?>
    <tr><form method="post"><td><input name="nama_barang" value="<?= $k['nama_barang'] ?>"></td><td><input type="number" name="stok" value="<?= $k['stok'] ?>"></td><td><input type="number" name="harga_titip" value="<?= $k['harga_titip'] ?>"></td><td><input type="number" name="harga_jual" value="<?= $k['harga_jual'] ?>"></td><td>Rp <?= number_format($k['harga_jual'] - $k['harga_titip']) ?></td><td><input type="hidden" name="id" value="<?= $k['id'] ?>"><button name="edit_konsinyasi">💾</button></td></form></tr>
    <?php endwhile; ?></table>
    <div class="pagination">
      <?php for ($i = 1; $i <= ceil($total_konsinyasi / $limit); $i++): ?>
        <a href="?page=<?= $i ?>&cari_konsinyasi=<?= urlencode($filter_konsinyasi) ?>"><?= $i ?></a>
      <?php endfor; ?>
    </div>
  </div>
</div>

<script>
function showTab(tabName) {
  document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
  document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
  document.getElementById('tab-' + tabName).classList.add('active');
  event.target.classList.add('active');
}
</script>
</body>
</html>
