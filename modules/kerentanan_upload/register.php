<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $password]);
        echo "<p style='color:green'>Registrasi berhasil! <a href='index.php'>Login</a></p>";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            echo "<p style='color:red'>Username sudah digunakan!</p>";
        } else {
            echo "<p style='color:red'>Terjadi kesalahan.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Register - Demo App</title>
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
        <h2>Daftar Akun Baru</h2>
        <form method="post">
            Username: <input type="text" name="username" required><br><br>
            Password: <input type="password" name="password" required><br><br>
            <button type="submit">Daftar</button>
        </form>
        <p>Sudah punya akun? <a href="index.php">Login</a></p>
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