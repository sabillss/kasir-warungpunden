<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'pemilik') {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

$bulan = $_GET['bulan'] ?? date('n');
$tahun = $_GET['tahun'] ?? date('Y');

// Set header agar file diekspor sebagai Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=laporan_penjualan_{$bulan}_{$tahun}.xls");
header("Pragma: no-cache");
header("Expires: 0");

// Tampilkan tabel
echo "<table border='1'>";
echo "<thead>
        <tr>
          <th>Nama Produk</th>
          <th>Jenis</th>
          <th>Total Qty</th>
          <th>Total Subtotal</th>
        </tr>
      </thead><tbody>";

// Gabungkan data menu dan konsinyasi
$stmt = $koneksi->prepare("
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
$stmt->bind_param("ii", $bulan, $tahun);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    echo "<tr>
        <td>" . htmlspecialchars($row['nama_produk']) . "</td>
        <td>" . ucfirst($row['jenis']) . "</td>
        <td align='right'>" . number_format($row['total_qty']) . "</td>
        <td align='right'>Rp " . number_format($row['total_subtotal'], 0, ',', '.') . "</td>
      </tr>";
}

echo "</tbody></table>";
?>
