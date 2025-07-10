<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Warung Punden</title>
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

        html, body {
            height: 100%;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5ede3, #e0cfc1);
            color: #4e342e;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 30px 20px;
        }

        .container {
            background: #fffdfc;
            padding: 60px 50px;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
            text-align: center;
            max-width: 600px;
            width: 100%;
            animation: fadeIn 0.8s ease-out;
        }

        .logo {
            width: 230px;
            margin-bottom: 30px;
            animation: slideDown 1s ease-out;
        }

        h1 {
            font-family: 'Playfair Display', serif;
            font-size: 34px;
            color: #3e2723;
            margin-bottom: 16px;
        }

        p {
            font-size: 17px;
            color: #5d4037;
            margin-bottom: 40px;
        }

        .button {
            display: inline-block;
            padding: 14px 32px;
            background-color: #6d4c41;
            color: white;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            border-radius: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
        }

        .button:hover {
            background-color: #4e342e;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 15px;
            background-color: #f3e7da;
            color: #5d4037;
            padding: 18px 10px;
            border-top: 2px dashed #d7bba8;
            width: 100%;
        }

        .footer span {
            font-size: 18px;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @media screen and (max-width: 600px) {
            .container {
                padding: 40px 25px;
            }

            h1 {
                font-size: 28px;
            }

            .button {
                padding: 12px 24px;
                font-size: 15px;
            }

            .footer {
                font-size: 13px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="logo-warungpunden.png" alt="Logo Warung Punden" class="logo">
        <h1>Selamat Datang<br><span style="color:#6d4c41;">Warung Punden</span></h1>
        <p>Sistem kasir profesional untuk mengelola transaksi, stok bahan baku, dan laporan warung Anda secara mudah dan cepat.</p>
        <a href="login.php" class="button">Masuk ke Sistem</a>
    </div>

    <div class="footer">
        Dibuat dengan <span>☕</span> & semangat oleh Tim Warung Punden ✨
    </div>
</body>
</html>
