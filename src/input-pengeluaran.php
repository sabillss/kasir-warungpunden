<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'pemilik') {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
    $kategori = $_POST['kategori'];
    $keterangan = $_POST['keterangan'];
    $nominal = $_POST['nominal'];
    $jam = $_POST['jam'];

    $stmt = $koneksi->prepare("INSERT INTO pengeluaran (tanggal, kategori, keterangan, nominal) VALUES (?, ?, ?, ?)");

    for ($i = 0; $i < count($keterangan); $i++) {
        if (!empty($keterangan[$i]) && $nominal[$i] > 0) {
            $tanggalLengkap = $tanggal . ' ' . $jam[$i]; // simpan jika perlu format lengkap
            $stmt->bind_param("sssd", $tanggal, $kategori[$i], $keterangan[$i], $nominal[$i]);
            $stmt->execute();
        }
    }

    header("Location: laporan-pengeluaran.php?msg=success");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Input Pengeluaran</title>
    <?php include 'pwa-setup.php'; ?>
    <style>
        body {
            font-family: sans-serif;
            background: #f4f1ec;
            padding: 40px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        h2 {
            text-align: center;
            margin-bottom: 24px;
            color: #4e342e;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd0c8;
        }
        input, select {
            padding: 6px;
            width: 100%;
        }
        button {
            padding: 10px 20px;
            background-color: #6d4c41;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background-color: #4e342e;
        }
        .add-btn {
            background-color: #888;
            margin-bottom: 20px;
        }
        .add-btn:hover {
            background-color: #555;
        }
        .back-link {
            display: block;
            margin-top: 20px;
            text-align: center;
            text-decoration: none;
            background-color: #a1887f;
            color: white;
            padding: 10px 18px;
            border-radius: 6px;
        }
        .back-link:hover {
            background-color: #8d6e63;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Input Pengeluaran</h2>
    <form method="POST">
        <label>Tanggal: <input type="date" name="tanggal" value="<?= date('Y-m-d') ?>" required></label><br><br>

        <button type="button" class="add-btn" onclick="addRow()">+ Tambah Baris</button>

        <table id="formTable">
            <thead>
                <tr>
                    <th>Jam</th>
                    <th>Kategori</th>
                    <th>Keperluan</th>
                    <th>Nominal</th>
                    <th>Hapus</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="time" name="jam[]" value="<?= date('H:i') ?>"></td>
                    <td>
                        <select name="kategori[]">
                            <option value="Belanja">Belanja</option>
                            <option value="Operasional">Operasional</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </td>
                    <td><input type="text" name="keterangan[]" placeholder="Contoh: Beli gas LPG"></td>
                    <td><input type="number" name="nominal[]" min="0" step="100"></td>
                    <td><button type="button" onclick="removeRow(this)">❌</button></td>
                </tr>
            </tbody>
        </table>

        <div style="text-align:center;">
            <button type="submit">Simpan Pengeluaran</button>
        </div>
    </form>

    <a href="laporan-pengeluaran.php" class="back-link">← Kembali ke Laporan Pengeluaran</a>
</div>

<script>
function addRow() {
    const tbody = document.querySelector('#formTable tbody');
    const row = document.createElement('tr');
    row.innerHTML = `
        <td><input type="time" name="jam[]" value="<?= date('H:i') ?>"></td>
        <td>
            <select name="kategori[]">
                <option value="Belanja">Belanja</option>
                <option value="Operasional">Operasional</option>
                <option value="Lainnya">Lainnya</option>
            </select>
        </td>
        <td><input type="text" name="keterangan[]" placeholder="Contoh: Beli es batu"></td>
        <td><input type="number" name="nominal[]" min="0" step="100"></td>
        <td><button type="button" onclick="removeRow(this)">❌</button></td>
    `;
    tbody.appendChild(row);
}

function removeRow(btn) {
    btn.closest('tr').remove();
}
</script>
</body>
</html>
