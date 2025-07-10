<?php 
session_start();
if ($_SESSION['role'] !== 'pemilik') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Pemilik - Warung Punden</title>
    <?php include 'pwa-setup.php'; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #fcf8f5;
            color: #4e342e;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header {
            background-color: #5d4037;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 48px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .brand {
            display: flex;
            align-items: center;
            font-family: 'Playfair Display', serif;
            font-size: 22px;
        }

        .brand img.logo {
            height: 70px;
            margin-right: 24px;
        }

        nav a {
            margin-left: 24px;
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            font-size: 15px;
            transition: opacity 0.2s ease;
        }

        nav a:hover {
            opacity: 0.8;
        }

        main {
            flex: 1;
            padding: 70px 24px;
            max-width: 1100px;
            margin: 0 auto;
        }

        h2 {
            font-family: 'Playfair Display', serif;
            font-size: 34px;
            margin-bottom: 12px;
            color: #3e2723;
            text-align: center;
        }

        .subtitle {
            font-size: 18px;
            text-align: center;
            color: #6d4c41;
            margin-bottom: 50px;
        }

        .card-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 28px;
        }

        .card {
            background-color: #fff;
            border-radius: 14px;
            padding: 36px 28px;
            box-shadow: 0 6px 16px rgba(0,0,0,0.08);
            text-align: center;
            text-decoration: none;
            color: inherit;
            transition: all 0.2s ease-in-out;
        }

        .card h3 {
            font-size: 20px;
            margin-bottom: 10px;
        }

        .card p {
            font-size: 15px;
            color: #6d4c41;
        }

        .card:hover {
            transform: translateY(-6px) scale(1.02);
            box-shadow: 0 12px 24px rgba(0,0,0,0.12);
        }

        footer {
            text-align: center;
            padding: 24px;
            font-size: 14px;
            background-color: #f1e2d1;
            color: #5d4037;
            border-top: 2px dashed #d7bba8;
        }

        footer span {
            font-size: 18px;
        }

        @media screen and (max-width: 600px) {
            header {
                flex-direction: column;
                align-items: flex-start;
            }
            nav {
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>

<header>
    <div class="brand">
        <img src="logo-warungpunden.png" alt="Logo" class="logo">
        Warung Punden
    </div>
    <nav>
        <a href="dashboard-pemilik.php">Dashboard</a> 
        <a href="logout.php">Logout</a>
    </nav>
</header>

<main>
    <h2>Halo, <?= htmlspecialchars($_SESSION['username']) ?> (Pemilik)</h2>
    <p class="subtitle">Selamat datang di sistem kasir Warung Punden.</p>

    <div class="card-container">
        <a href="transaksi.php" class="card">
            <h3>🧾 Transaksi</h3>
            <p>Catat penjualan harian</p>
        </a>
        <a href="laporan-stok.php" class="card">
            <h3>📦 Stok Bahan</h3>
            <p>Lihat & kelola stok bahan</p>
        </a>
        <a href="laporan-harian.php" class="card">
            <h3>📊 Laporan Harian</h3>
            <p>Total pemasukan harian</p>
        </a>
        <a href="laporan-bulanan.php" class="card">
            <h3>📅 Laporan Bulanan</h3>
            <p>Rekap pemasukan & pengeluaran bulanan</p>
        </a>
        <a href="laporan-pengeluaran.php" class="card">
            <h3>📉 Pengeluaran</h3>
            <p>Catat biaya operasional</p>
        </a>
        <a href="rekap-shift-kasir.php" class="card">
            <h3>👥 Shift Kasir</h3>
            <p>Lihat rekap shift & jam kerja kasir</p>
        </a>
    </div>
</main>

<footer>
    Sistem dibuat dengan <span>☕</span> & semangat oleh Tim Warung Punden ✨
</footer>

</body>
</html>
