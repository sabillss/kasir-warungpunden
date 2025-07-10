<?php
include 'koneksi.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=laporan_pengeluaran.xls");

$dari = $_GET['dari'] ?? '';
$sampai = $_GET['sampai'] ?? '';
$bulan = $_GET['bulan'] ?? '';
$tahun = $_GET['tahun'] ?? '';
$mode = $_GET['mode'] ?? 'harian';

$total = 0;

echo "LAPORAN PENGELUARAN\n";

if ($mode === 'bulanan' && $bulan && $tahun) {
    echo "Mode: Bulanan\tBulan: $bulan\tTahun: $tahun\n\n";
    echo "Tanggal\tKategori\tKeperluan\tJumlah (Rp)\n";
    
    $query = "SELECT tanggal, kategori, keterangan, nominal 
              FROM pengeluaran 
              WHERE MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' 
              ORDER BY tanggal DESC";
} else {
    echo "Mode: Harian\tDari: $dari\tSampai: $sampai\n\n";
    echo "Tanggal\tKategori\tKeperluan\tJumlah (Rp)\n";
    
    if ($dari && $sampai) {
        $query = "SELECT tanggal, kategori, keterangan, nominal 
                  FROM pengeluaran 
                  WHERE tanggal BETWEEN '$dari' AND '$sampai' 
                  ORDER BY tanggal DESC";
    } else {
        $query = "SELECT tanggal, kategori, keterangan, nominal 
                  FROM pengeluaran 
                  ORDER BY tanggal DESC";
    }
}

$result = $koneksi->query($query);

while ($row = $result->fetch_assoc()) {
    echo "{$row['tanggal']}\t{$row['kategori']}\t{$row['keterangan']}\t" . number_format($row['nominal'], 0, ',', '.') . "\n";
    $total += $row['nominal'];
}

echo "\n\t\tTotal Pengeluaran:\tRp " . number_format($total, 0, ',', '.');
?>
