<?php
require 'config.php';
require_login();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];

    $file_path = null;
    if (!empty($_FILES['file']['name'])) {
        $upload_dir = 'uploads/';
        $file_name = $_FILES['file']['name'];
        $tmp_file = $_FILES['file']['tmp_name'];
        $target = $upload_dir . basename($file_name);

        // ❌ TIDAK ADA VALIDASI — RENTAN!
        if (move_uploaded_file($tmp_file, $target)) {
            $file_path = $target;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO articles (user_id, title, content, file_path) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $title, $content, $file_path]);

    $message = "Artikel berhasil disimpan!";
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Artikel - Versi RENTAN</title>
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
        <h2>Tulis Artikel (Versi RENTAN)</h2>
        <?php if ($message):
            echo "<p style='color:green'>$message</p>"; endif; ?>

        <form method="post" enctype="multipart/form-data">
            Judul: <input type="text" name="title" required><br><br>
            Isi: <textarea name="content" required></textarea><br><br>
            File (opsional): <input type="file" name="file"><br><br>
            <button type="submit">Simpan Artikel</button>
        </form>

        <p style="color:red; font-weight:bold;">
            ⚠️ PERINGATAN: Versi ini memungkinkan upload file PHP berbahaya!
        </p>
        <a href="dashboard.php">Kembali ke Dashboard</a>
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