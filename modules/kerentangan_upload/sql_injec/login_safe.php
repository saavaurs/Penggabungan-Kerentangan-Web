<?php
require '../../config.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // ✅ Aman: Menggunakan prepared statement (PDO)
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->execute([$username, $password]);

    if ($stmt->rowCount() > 0) {
        $message = "✅ Login berhasil! Selamat datang, " . htmlspecialchars($username);
    } else {
        $message = "❌ Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>SQL Injection - Versi Aman</title>
    <link rel="stylesheet" href="../../style.css">
</head>
<body>
<div class="container">
    <h2>SQL Injection (Versi Aman)</h2>
    <form method="post">
        Username: <input type="text" name="username" required><br><br>
        Password: <input type="password" name="password" required><br><br>
        <button type="submit">Login</button>
    </form>
    <p style="color:green;">
        ✅ Query sudah aman karena menggunakan prepared statement.<br>
        Input user tidak langsung dimasukkan ke query SQL.
    </p>
    <p><?= $message ?></p>
    <p><a href="login_vul.php">⬅️ Lihat versi rentan</a></p>
</div>
</body>
</html>
