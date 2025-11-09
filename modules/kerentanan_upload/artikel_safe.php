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
        $file_size = $_FILES['file']['size'];

        // ✅ Validasi ekstensi
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed_ext)) {
            die("Ekstensi file tidak diizinkan!");
        }

        // ✅ Validasi ukuran (max 2MB)
        if ($file_size > 2 * 1024 * 1024) {
            die("File terlalu besar! Maksimal 2MB.");
        }

        // ✅ Validasi MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $tmp_file);
        finfo_close($finfo);

        $allowed_mimes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
        if (!in_array($mime, $allowed_mimes)) {
            die("Tipe file tidak valid!");
        }

        // ✅ Nama file acak
        $new_name = uniqid('upload_') . '.' . $ext;
        $target = $upload_dir . $new_name;

        if (move_uploaded_file($tmp_file, $target)) {
            $file_path = $target;
        } else {
            die("Gagal menyimpan file.");
        }
    }

    $stmt = $pdo->prepare("INSERT INTO articles (user_id, title, content, file_path) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $title, $content, $file_path]);

    $message = "Artikel berhasil disimpan dengan aman!";
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Artikel - Versi AMAN</title>
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
        <h2>Tulis Artikel (Versi AMAN)</h2>
        <?php if ($message):
            echo "<p style='color:green'>$message</p>"; endif; ?>

        <form method="post" enctype="multipart/form-data">
            Judul: <input type="text" name="title" required><br><br>
            Isi: <textarea name="content" required></textarea><br><br>
            File (opsional): <input type="file" name="file"><br><br>
            <button type="submit">Simpan Artikel</button>
        </form>

        <p style="color:green; font-weight:bold;">
            ✅ Versi ini memblokir file berbahaya dan hanya mengizinkan gambar/PDF.
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