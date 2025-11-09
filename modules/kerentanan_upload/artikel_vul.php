<?php
require 'config.php';
require_login();

 $message = '';
 $error = ''; // Menambahkan variabel error secara eksplisit

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
        } else {
            $error = "Gagal mengunggah file."; // Menambahkan pesan error
        }
    }

    if (empty($error)) {
        $stmt = $pdo->prepare("INSERT INTO articles (user_id, title, content, file_path) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $title, $content, $file_path]);
        $message = "Artikel berhasil disimpan!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Artikel - Versi RENTAN</title>
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
                rgba(255, 71, 87, 0.1) 0%,
                transparent 50%
            ),
            radial-gradient(
                circle at 80% 80%,
                rgba(255, 0, 255, 0.1) 0%,
                transparent 50%
            ),
            radial-gradient(
                circle at 40% 20%,
                rgba(255, 165, 2, 0.05) 0%,
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
            padding: 2rem;
            border-radius: 20px;
            border: 1px solid rgba(255, 71, 87, 0.3);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.5s ease;
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

        h2 {
            text-align: center;
            margin-bottom: 2rem;
            background: linear-gradient(135deg, var(--danger), var(--warning));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 2rem;
            font-weight: bold;
            position: relative;
        }

        h2::after {
            content: "";
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background: linear-gradient(90deg, var(--danger), var(--warning));
            border-radius: 3px;
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

        /* Form Elements */
        form {
            display: flex;
            flex-direction: column;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--text);
            font-size: 0.9rem;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 12px 15px;
            border-radius: 10px;
            border: 1px solid rgba(255, 71, 87, 0.3);
            background: rgba(26, 31, 58, 0.6);
            color: var(--text);
            font-size: 16px;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus,
        textarea:focus {
            outline: none;
            border-color: var(--danger);
            background: rgba(26, 31, 58, 0.8);
            box-shadow: 0 0 15px rgba(255, 71, 87, 0.3);
        }

        textarea {
            resize: vertical;
            min-height: 120px;
        }

        /* Custom File Input */
        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }

        .file-input-wrapper input[type=file] {
            position: absolute;
            top: 0;
            right: 0;
            min-width: 100%;
            min-height: 100%;
            font-size: 100px;
            text-align: right;
            filter: alpha(opacity=0);
            opacity: 0;
            outline: none;
            cursor: pointer;
            display: block;
        }

        .file-input-button {
            display: block;
            width: 100%;
            padding: 12px 15px;
            border-radius: 10px;
            border: 1px dashed rgba(255, 71, 87, 0.4);
            background: rgba(26, 31, 58, 0.4);
            color: rgba(224, 230, 237, 0.7);
            font-size: 0.9rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .file-input-wrapper:hover .file-input-button {
            border-color: var(--danger);
            background: rgba(255, 71, 87, 0.1);
            color: var(--text);
        }

        button {
            width: 100%;
            background: linear-gradient(135deg, var(--danger), var(--warning));
            border: none;
            padding: 14px;
            border-radius: 10px;
            color: white;
            font-weight: 700;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            margin-top: 10px;
        }

        button::before {
            content: "";
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        button:hover::before {
            left: 100%;
        }

        button:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(255, 71, 87, 0.4);
        }

        /* Warning Paragraph */
        .warning-vuln {
            margin-top: 20px;
            padding: 15px;
            background: rgba(255, 71, 87, 0.1);
            border: 1px solid rgba(255, 71, 87, 0.2);
            border-radius: 10px;
            color: var(--danger);
            font-weight: 600;
            text-align: center;
            animation: blink 2s infinite;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            text-align: center;
            width: 100%;
            padding: 10px;
            color: var(--text);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border-radius: 8px;
        }

        .back-link:hover {
            color: var(--danger);
            background: rgba(255, 71, 87, 0.1);
        }

        @media (max-width: 600px) {
            .container {
                padding: 1.5rem;
            }
            
            h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>⚠️ Tulis Artikel (Versi RENTAN)</h2>

        <?php if ($error): ?>
            <div class="message error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($message): ?>
            <div class="message success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Judul</label>
                <input type="text" id="title" name="title" required>
            </div>
            
            <div class="form-group">
                <label for="content">Isi</label>
                <textarea id="content" name="content" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="file">File (opsional)</label>
                <div class="file-input-wrapper">
                    <input type="file" id="file" name="file">
                    <div class="file-input-button">Pilih file (SEMUA TIPE FILE DIIZINKAN)</div>
                </div>
            </div>
            
            <button type="submit">Simpan Artikel</button>
        </form>

        <p class="warning-vuln">
            ⚠️ PERINGATAN: Versi ini rentan! Tidak ada validasi file, memungkinkan upload file berbahaya seperti PHP shell.
        </p>
        
        <a href="dashboard.php" class="back-link">Kembali</a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Update file input button text on file selection
            const fileInput = document.querySelector('input[type=file]');
            const fileButton = document.querySelector('.file-input-button');

            fileInput.addEventListener('change', function() {
                if (fileInput.files.length > 0) {
                    const fileName = fileInput.files[0].name;
                    fileButton.textContent = `File dipilih: ${fileName}`;
                } else {
                    fileButton.textContent = 'Pilih file (SEMUA TIPE FILE DIIZINKAN)';
                }
            });

            // Notification animation script
            const notification = document.querySelector('.message');

            if (notification) {
                setTimeout(() => {
                    notification.classList.add('show');
                }, 100);

                setTimeout(() => {
                    notification.classList.remove('show');
                    
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.parentNode.removeChild(notification);
                        }
                    }, 500);
                }, 4000);
            }
        });
    </script>
</body>

</html>