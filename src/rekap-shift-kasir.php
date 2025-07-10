<?php
session_start();
if ($_SESSION['role'] !== 'pemilik') {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';

// Filter bulan & tahun
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

// Ambil semua log shift
$query = "SELECT * FROM log_shift_kasir 
          WHERE MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' 
          ORDER BY tanggal DESC, jam_masuk DESC";
$result = $koneksi->query($query);

// Rekap jumlah shift per kasir per hari + jam kerja
$rekap_query = "SELECT username, tanggal, COUNT(*) AS jumlah_shift,
                MIN(jam_masuk) AS jam_awal, MAX(jam_keluar) AS jam_akhir
                FROM log_shift_kasir
                WHERE MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun'
                GROUP BY username, tanggal
                ORDER BY tanggal DESC";
$rekap_result = $koneksi->query($rekap_query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Shift Kasir - Warung Punden</title>
    <?php include 'pwa-setup.php'; ?>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #fcf9f6;
            margin: 0;
            padding: 30px;
        }

        h2 {
            text-align: center;
            color: #4e342e;
            margin-bottom: 20px;
        }

        form {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            margin-bottom: 40px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
            font-size: 14px;
        }

        th {
            background-color: #6d4c41;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f4f1;
        }

        select, button {
            padding: 6px 10px;
            font-size: 14px;
        }

        .back {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #6d4c41;
            font-weight: bold;
        }

        .back:hover {
            text-decoration: underline;
        }

        h3 {
            margin-top: 40px;
            color: #3e2723;
        }
    </style>
</head>
<body>

<h2>Rekap Shift Kasir</h2>

<form method="get">
    Filter Bulan:
    <select name="bulan">
        <?php
        for ($b = 1; $b <= 12; $b++) {
            $pad = str_pad($b, 2, '0', STR_PAD_LEFT);
            $selected = ($bulan == $pad) ? 'selected' : '';
            echo "<option value='$pad' $selected>$pad</option>";
        }
        ?>
    </select>

    Tahun:
    <select name="tahun">
        <?php
        for ($t = date('Y'); $t >= date('Y') - 2; $t--) {
            $selected = ($tahun == $t) ? 'selected' : '';
            echo "<option value='$t' $selected>$t</option>";
        }
        ?>
    </select>
    <button type="submit">Tampilkan</button>
</form>

<!-- Tabel log shift -->
<h3>Log Shift Harian</h3>
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Kasir</th>
            <th>Tanggal</th>
            <th>Shift</th>
            <th>Jam Masuk</th>
            <th>Jam Keluar</th>
            <th>Durasi (jam)</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><?= date("d-m-Y", strtotime($row['tanggal'])) ?></td>
                    <td><?= $row['shift'] ?></td>
                    <td><?= $row['jam_masuk'] ?></td>
                    <td><?= $row['jam_keluar'] ?></td>
                    <td><?= number_format($row['durasi_jam'], 2) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="7">Tidak ada data shift untuk bulan ini.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<!-- Rekap jumlah shift per hari -->
<h3>Rekap Jumlah Shift per Kasir per Hari</h3>
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Kasir</th>
            <th>Tanggal</th>
            <th>Jumlah Shift</th>
            <th>Jam Kerja</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($rekap_result->num_rows > 0): ?>
            <?php $no = 1; while ($row = $rekap_result->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><?= date("d-m-Y", strtotime($row['tanggal'])) ?></td>
                    <td><?= $row['jumlah_shift'] ?></td>
                    <td><?= $row['jam_awal'] ?> – <?= $row['jam_akhir'] ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5">Tidak ada data rekap shift.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<div style="text-align:center;">
    <a href="dashboard-pemilik.php" class="back">← Kembali ke Dashboard</a>
</div>

</body>
</html>
