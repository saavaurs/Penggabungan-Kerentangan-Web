<?php
require 'config.php';
if (empty($_SESSION['user'])) header('Location: login.php');
$user = $_SESSION['user'];
?>
<h1>Dashboard — <?=htmlspecialchars($user['username'])?></h1>

<div style="display:flex; gap:20px;">
  <div style="border:1px solid #c00; padding:16px;">
    <h3>VULNERABLE AREA</h3>
    <p>Contoh Broken Access Control (IDOR) — tanpa validasi ownership.</p>
    <a href="vuln/list.php">Masuk ke area VULN</a>
  </div>

  <div style="border:1px solid #0a0; padding:16px;">
    <h3>SAFE AREA</h3>
    <p>Versi aman dengan UUID + Token + Ownership Check.</p>
    <a href="safe/list.php">Masuk ke area SAFE</a>
  </div>
</div>

<p><a href="logout.php">Logout</a></p>
