<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Warung Punden</title>
    <?php include 'pwa-setup.php'; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Inter:wght@400;600&display=swap" rel="stylesheet">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5ede3, #e0cfc1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            padding: 30px 20px;
        }

        .content {
            flex-grow: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
        }

        .login-container {
            background: #fffdfc;
            padding: 60px 50px;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }

        .login-container img.logo {
            width: 120px;
            margin-bottom: 30px;
        }

        h2 {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            color: #3e2723;
            margin-bottom: 25px;
        }

        form input {
            width: 100%;
            padding: 14px;
            margin-bottom: 18px;
            border: 1px solid #ccc;
            border-radius: 10px;
            font-size: 15px;
        }

        form button {
            padding: 14px;
            width: 100%;
            background-color: #6d4c41;
            color: white;
            font-weight: bold;
            font-size: 16px;
            border: none;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        form button:hover {
            background-color: #4e342e;
            transform: scale(1.03);
        }

        .register-link {
            margin-top: 18px;
            font-size: 15px;
        }

        .register-link a {
            color: #6d4c41;
            text-decoration: none;
            font-weight: 600;
        }

        .footer {
            width: 100%;
            text-align: center;
            padding: 25px 10px;
            font-size: 16px;
            background-color: #f3e7da;
            color: #5d4037;
            border-top: 2px dashed #d7bba8;
        }

        .footer span {
            font-size: 20px;
        }

        @media screen and (max-width: 600px) {
            .login-container {
                padding: 40px 25px;
            }

            h2 {
                font-size: 26px;
            }

            .footer {
                font-size: 14px;
            }
            .back-home {
                background-color: #fceee5;
                background-color: #fceee5;
                padding: 14px 18px;
                border-radius: 10px;
                margin-top: 20px;
                font-size: 15px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
                display: inline-block;
            }
            .back-home a {
                color: #6d4c41;
                text-decoration: none;
                font-weight: 600;
            }
            .back-home a:hover {
                text-decoration: underline;
            }
        }
    </style>
</head>
<body>

    <div class="content">
        <div class="login-container">
            <img src="logo-warungpunden.png" alt="Logo Warung Punden" class="logo">
            <h2>Login Sistem Kasir</h2>
            <form action="cek_login.php" method="post">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>

            <p class="back-home">
                <a href="index.php">← Kembali ke Halaman Awal</a>
            </p>

            <p class="register-link">Belum punya akun? <a href="register.php">Daftar di sini</a></p>
        </div>
    </div>

    <div class="footer">
        Dibuat dengan <span>☕</span> & semangat lembur oleh Tim Warung Punden ✨
    </div>

</body>
</html>
