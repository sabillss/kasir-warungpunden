<?php
session_start();
date_default_timezone_set('Asia/Jakarta');
if ($_SESSION['role'] !== 'kasir') {
    header("Location: login.php");
    exit;
}

// Simpan waktu login (timestamp UNIX untuk hitung durasi kerja di logout.php)
if (!isset($_SESSION['jam_login'])) {
    $_SESSION['jam_login'] = time();
}

// Format waktu login tampilannya (H:i)
if (!isset($_SESSION['login_time'])) {
    $_SESSION['login_time'] = date("H:i", $_SESSION['jam_login']);
}

// Tentukan shift aktif berdasarkan jam login
$jam = (int)date("H", $_SESSION['jam_login']);
if ($jam >= 10 && $jam < 18) {
    $shift = "Shift 1 (10:00–18:00)";
} elseif ($jam >= 18 && $jam <= 23) {
    $shift = "Shift 2 (18:00–24:00)";
} else {
    $shift = "Di luar shift (10:00–24:00)";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Kasir | Warung Punden</title>
    <?php include 'pwa-setup.php'; ?>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #fdf6ee, #e8dcd1);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        header {
            background-color: #6d4c41;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 40px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .brand {
            font-family: 'Playfair Display', serif;
            font-size: 22px;
        }

        nav a {
            margin-left: 24px;
            color: white;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: opacity 0.2s ease;
        }

        nav a:hover {
            opacity: 0.85;
        }

        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 60px 20px;
        }

        .container {
            background-color: #ffffff;
            padding: 40px 32px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.08);
            text-align: center;
            max-width: 460px;
            width: 100%;
        }

        h2 {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            color: #3e2723;
            margin-bottom: 8px;
        }

        .info {
            font-size: 14px;
            color: #6d4c41;
            margin-bottom: 26px;
            line-height: 1.6;
        }

        .button {
            display: block;
            width: 100%;
            max-width: 260px;
            margin: 10px auto;
            padding: 14px 24px;
            background-color: #00a8ff;
            color: white;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: background-color 0.2s ease;
        }

        .button:hover {
            background-color: #0097e6;
        }

        .logout {
            background-color: #d63031;
        }

        .logout:hover {
            background-color: #c0392b;
        }

        footer {
            text-align: center;
            padding: 20px;
            font-size: 14px;
            background-color: #f3e7da;
            color: #5d4037;
            border-top: 2px dashed #d7bba8;
        }
    </style>
</head>
<body>

<header>
    <div class="brand">Warung Punden</div>
    <nav>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<main>
    <div class="container">
        <h2>Halo, <?= htmlspecialchars($_SESSION['username']) ?> (Kasir)</h2>
        <div class="info">
            🕒 Login pada: <strong><?= $_SESSION['login_time'] ?></strong> WIB<br>
            📅 Tanggal: <strong><?= date("d F Y") ?></strong><br>
            🔄 Shift saat ini: <strong><?= $shift ?></strong>
        </div>

        <a href="transaksi.php" class="button">🧾 Input Transaksi</a>
        <a href="logout.php" class="button logout">🚪 Logout</a>
    </div>
</main>

<footer>
    Sistem ini dibuat dengan ☕ & semangat oleh Tim Warung Punden ✨
</footer>

</body>
</html>
