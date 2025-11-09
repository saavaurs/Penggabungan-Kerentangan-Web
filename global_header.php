<?php
// Pastikan session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fungsi untuk mendapatkan judul halaman yang aman
 $page_title = $page_title ?? 'Module Page';
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title><?php echo htmlspecialchars($page_title); ?></title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    /* ... (masukkan semua CSS tema cyberpunk/neon Anda di sini) ... */

    /* CSS untuk Header Global */
    .global-header {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      z-index: 1000;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 12px 30px;
      background: rgba(26, 31, 58, 0.9);
      backdrop-filter: blur(15px);
      border-bottom: 1px solid rgba(0, 212, 255, 0.2);
    }

    .header-brand {
      display: flex;
      align-items: center;
      gap: 12px;
      font-weight: 700;
      font-size: 1.2rem;
      color: var(--primary);
    }

    .header-actions {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .header-link {
      color: var(--text);
      text-decoration: none;
      font-weight: 600;
      padding: 8px 15px;
      border-radius: 8px;
      transition: all 0.3s ease;
    }

    .header-link:hover {
      background: rgba(0, 212, 255, 0.1);
      color: var(--primary);
    }

    .user-info {
        color: rgba(224, 230, 237, 0.7);
        font-size: 0.9rem;
    }
    .btn-back {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 10px 16px;
      border-radius: 10px;
      background: rgba(26, 31, 58, 0.6);
      color: var(--text);
      text-decoration: none;
      border: 1px solid rgba(0, 212, 255, 0.2);
      font-weight: 700;
      transition: all 0.3s ease;
    }

    /* Tambahkan padding-top ke body agar tidak tertutup header */
    body {
      padding-top: 60px; /* Sesuaikan dengan tinggi header */
    }
  </style>
</head>
<body>
  <header class="global-header">
    <div class="header-brand">
      <div style="width:30px; height:30px; border-radius:8px; background:linear-gradient(135deg,var(--primary), var(--secondary)); display:flex; align-items:center; justify-content:center; color:#001; font-weight:700;">L</div>
      Lab Keamanan
    </div>
    <div class="header-actions">
      <a class="btn-back" href="../../dashboard.php" aria-label="Kembali ke Dashboard">Kembali ke Dashboard</a>
    </div>
  </header>

  <!-- Konten halaman akan dimuat di sini -->