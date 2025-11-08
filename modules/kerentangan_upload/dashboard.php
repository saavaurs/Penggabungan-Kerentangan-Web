<?php
require 'config.php';
require_login();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Dashboard - Demo App</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <?php if (isset($error)): ?>
        <div class="message error"><?= htmlspecialchars($error) ?></div> 
    <?php endif; ?>

    <?php if (isset($message) && $message): ?>
        <div class="message success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <div class="container">
        <h1>Selamat datang, <?= htmlspecialchars($_SESSION['username']) ?>!</h1>
        <nav>
            <p>
                <a href="artikel_vul.php">📝 Artikel (Versi RENTAN)</a> |
                <a href="artikel_safe.php">✅ Artikel (Versi AMAN)</a> |
                <a href="logout.php">Logout</a>
            </p>
        </nav>
        <h2>Menu Utama</h2>
        <p>Ini adalah dashboard setelah login.</p>
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