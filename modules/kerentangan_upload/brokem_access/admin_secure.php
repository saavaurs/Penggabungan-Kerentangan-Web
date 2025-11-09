<?php
session_start();
// Simulasi login user biasa
$_SESSION['username'] = 'budi';
$_SESSION['role'] = 'user';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Broken Access Control - Versi Aman</title>
    <link rel="stylesheet" href="../../style.css">
</head>
<body>
<div class="container">
    <h2>Broken Access Control (Versi Aman)</h2>
    <p>Login sebagai: <b><?= $_SESSION['username'] ?></b> (role: <?= $_SESSION['role'] ?>)</p>
    <p>✅ Akses ke halaman admin dilindungi server-side.</p>
    <p><a href="admin_secure.php">➡️ Coba akses halaman admin (terproteksi)</a></p>
    <p><a href="../../dashboard.php">⬅️ Kembali ke Dashboard</a></p>
</div>
</body>
</html>
