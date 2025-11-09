<?php
require 'config.php';
require '../../global_header.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND password = ?");
    $stmt->execute([$username, $password]);
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $username;
        header('Location: dashboard.php');
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Dashboard - Demo App</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #00d4ff;
            --secondary: #ff00ff;
            --dark: #0a0e27;
            --darker: #060818;
            --light: #1a1f3a;
            --text: #e0e6ed;
            --danger: #ff4757;
            --success: #00ff88;
            --warning: #ffa502;
        }

        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--darker) 0%, var(--dark) 100%);
            color: var(--text);
            min-height: 100vh;
            overflow-x: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        /* Animated Background */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(
                circle at 20% 50%,
                rgba(0, 212, 255, 0.1) 0%,
                transparent 50%
            ),
            radial-gradient(
                circle at 80% 80%,
                rgba(255, 0, 255, 0.1) 0%,
                transparent 50%
            ),
            radial-gradient(
                circle at 40% 20%,
                rgba(0, 255, 136, 0.05) 0%,
                transparent 50%
            );
            pointer-events: none;
            z-index: 1;
        }

        .container {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 700px;
            background: rgba(26, 31, 58, 0.8);
            backdrop-filter: blur(10px);
            padding: 3rem;
            border-radius: 20px;
            border: 1px solid rgba(0, 212, 255, 0.2);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.5s ease;
            text-align: center;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 2rem;
        }

        h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text);
            margin-top: 2rem;
            margin-bottom: 1rem;
        }

        p {
            color: rgba(224, 230, 237, 0.7);
            line-height: 1.6;
        }

        /* Navigation Links */
        nav {
            margin: 2rem 0;
        }

        nav p {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
        }

        nav a {
            display: inline-block;
            padding: 12px 25px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 1rem;
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        nav a::before {
            content: "";
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        nav a:hover::before {
            left: 100%;
        }

        nav a:hover {
            transform: translateY(-3px);
        }

        nav a.vulnerable-link {
            background: linear-gradient(135deg, var(--danger), var(--warning));
            color: white;
            animation: blink 2s infinite;
        }

        nav a.vulnerable-link:hover {
            box-shadow: 0 10px 20px rgba(255, 71, 87, 0.4);
        }

        nav a.safe-link {
            background: linear-gradient(135deg, var(--success), var(--primary));
            color: #001;
        }

        nav a.safe-link:hover {
            box-shadow: 0 10px 20px rgba(0, 255, 136, 0.4);
        }

        nav a.logout-link {
            background: transparent;
            color: var(--text);
            border: 1px solid rgba(224, 230, 237, 0.3);
        }

        nav a.logout-link:hover {
            background: rgba(224, 230, 237, 0.1);
            border-color: var(--text);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }

        /* Notification Message */
        .message {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 600;
            opacity: 0;
            transform: translateY(-20px);
            transition: opacity 0.5s ease, transform 0.5s ease;
        }

        .message.show {
            opacity: 1;
            transform: translateY(0);
        }

        .message.success {
            background: rgba(0, 255, 136, 0.1);
            border-left: 4px solid var(--success);
            color: var(--success);
        }

        .message.error {
            background: rgba(255, 71, 87, 0.1);
            border-left: 4px solid var(--danger);
            color: var(--danger);
        }

        @media (max-width: 600px) {
            .container {
                padding: 2rem;
            }
            
            h1 {
                font-size: 2rem;
            }

            nav p {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <?php if (isset($error)): ?>
            <div class="message error"><?= htmlspecialchars($error) ?></div> 
        <?php endif; ?>

        <?php if (isset($message) && $message): ?>
            <div class="message success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <h1>Selamat datang, <?= htmlspecialchars($_SESSION['username']) ?>!</h1>
        
        <nav>
            <p>
                <a href="artikel_vul.php" class="vulnerable-link">📝 Artikel (Versi RENTAN)</a>
                <a href="artikel_safe.php" class="safe-link">✅ Artikel (Versi AMAN)</a>
            </p>
        </nav>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Cari elemen notifikasi
            const notification = document.querySelector('.message');

            // 2. Jika ada notifikasi...
            if (notification) {
                // 3. Munculkan setelah sepersekian detik (untuk memicu animasi)
                setTimeout(() => {
                    notification.classList.add('show');
                }, 100); // 100ms jeda

                // 4. Sembunyikan (dan hapus) setelah 4 detik
                setTimeout(() => {
                    notification.classList.remove('show');
                    
                    // 5. Hapus elemen dari halaman setelah animasinya selesai
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.parentNode.removeChild(notification);
                        }
                    }, 500); // 500ms = durasi transisi di CSS
                }, 4000); // 4000ms = 4 detik
            }
        });
    </script>
</body>
</html>