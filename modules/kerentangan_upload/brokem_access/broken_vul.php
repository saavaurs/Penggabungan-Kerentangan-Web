<?php
session_start();
// Simulasi login user biasa
$_SESSION['username'] = 'budi';
$_SESSION['role'] = 'user';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Broken Access Control - Versi Rentan</title>
    <link rel="stylesheet" href="../../style.css">
</head>
<body>
<div class="container">
    <h2>Broken Access Control (Versi Rentan)</h2>
    <p>Login sebagai: <b><?= $_SESSION['username'] ?></b> (role: <?= $_SESSION['role'] ?>)</p>
    <p>⚠️ Tidak ada validasi role pada halaman admin.</p>
    <p><a href="admin_page.php">➡️ Buka halaman admin tanpa cek role</a></p>
    <p><a href="../../dashboard.php">⬅️ Kembali ke Dashboard</a></p>
</div>
</body>
</html>
