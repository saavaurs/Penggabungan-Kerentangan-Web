<?php
require '../../config.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // ⚠️ Rentan: Query langsung digabung tanpa validasi
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $pdo->query($query);

    if ($result->rowCount() > 0) {
        $message = "✅ Login berhasil! Selamat datang, " . htmlspecialchars($username);
    } else {
        $message = "❌ Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>SQL Injection - Versi Rentan</title>
    <link rel="stylesheet" href="../../style.css">
</head>
<body>
<div class="container">
    <h2>SQL Injection (Versi Rentan)</h2>
    <form method="post">
        Username: <input type="text" name="username" required><br><br>
        Password: <input type="password" name="password" required><br><br>
        <button type="submit">Login</button>
    </form>
    <p style="color:red;">
        ⚠️ Coba payload berikut untuk bypass login: <br>
        <code>' OR '1'='1</code>
    </p>
    <p><?= $message ?></p>
    <p><a href="login_safe.php">➡️ Lihat versi aman</a></p>
</div>
</body>
</html>
