<?php
// dashboard.php
// Protected dashboard page. Requires login.
// Shows links to vulnerable/safe demo pages and Logout.
require '../../global_header.php';
require 'auth_simple.php'; // harus tersedia di project
 $pdo = pdo_connect();

 $user = current_user();


// fetch some simple stats (best-effort; tidak fatal jika query gagal)
 $stats = [
    'posts' => null,
    'comments' => null,
    'users' => null,
];
try {
    $stmt = $pdo->query("SELECT COUNT(*) AS cnt FROM posts");
    $stats['posts'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['cnt'];
} catch (Exception $e) { /* ignore */ }

try {
    $stmt = $pdo->query("SELECT COUNT(*) AS cnt FROM comments");
    $stats['comments'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['cnt'];
} catch (Exception $e) { /* ignore */ }

try {
    $stmt = $pdo->query("SELECT COUNT(*) AS cnt FROM users");
    $stats['users'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['cnt'];
} catch (Exception $e) { /* ignore */ }

function esc($s) {
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Dashboard — Lab Demo</title>
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
          rgba(0, 212, 255, 0.1) 0%,
          transparent 50%
        ),
        radial-gradient(
          circle at 80% 80%,
          rgba(255, 0, 255, 0.1) 0%,
          transparent 50%
        ),
        radial-gradient(
          circle at 40% 20%,
          rgba(0, 255, 136, 0.05) 0%,
          transparent 50%
        );
      pointer-events: none;
      z-index: 1;
    }

    .wrap {
      position: relative;
      z-index: 2;
      max-width: 1200px;
      margin: 36px auto;
      padding: 20px;
    }

    /* Top Bar */
    .topbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 20px;
      margin-bottom: 40px;
      padding: 20px;
      background: rgba(26, 31, 58, 0.6);
      backdrop-filter: blur(10px);
      border-radius: 15px;
      border: 1px solid rgba(0, 212, 255, 0.2);
      animation: slideDown 0.5s ease;
    }

    @keyframes slideDown {
      from {
        opacity: 0;
        transform: translateY(-20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .brand-container {
      display: flex;
      align-items: center;
      gap: 16px;
    }

    .brand {
      display: flex;
      width: 56px;
      height: 56px;
      border-radius: 12px;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 20px;
      color: #001;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      box-shadow: 0 8px 24px rgba(0, 212, 255, 0.3);
      animation: pulse 2s infinite;
    }

    @keyframes pulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.05); }
    }

    .user-info h1 {
      font-size: 1.3rem;
      font-weight: 700;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    
    .user-info p {
      color: rgba(224, 230, 237, 0.7);
      font-size: 0.9rem;
    }

    .topbar-right {
      display: flex;
      align-items: center;
      gap: 20px;
    }

    .stats-mini {
      text-align: right;
    }

    .stats-mini div {
      font-size: 0.9rem;
      color: rgba(224, 230, 237, 0.7);
    }

    .stats-mini strong {
      color: var(--primary);
      font-weight: 700;
    }

    /* Card Grid */
    .card-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 25px;
      margin-bottom: 30px;
    }

    .dash-card {
      background: rgba(26, 31, 58, 0.6);
      backdrop-filter: blur(10px);
      border-radius: 15px;
      padding: 25px;
      border: 1px solid rgba(0, 212, 255, 0.1);
      box-shadow: 0 8px 22px rgba(0, 0, 0, 0.3);
      transition: all 0.3s ease;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      min-height: 200px;
      animation: fadeIn 0.5s ease backwards;
    }

    .dash-card:nth-child(1) { animation-delay: 0.1s; }
    .dash-card:nth-child(2) { animation-delay: 0.2s; }
    .dash-card:nth-child(3) { animation-delay: 0.3s; }
    .dash-card:nth-child(4) { animation-delay: 0.4s; }

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

    .dash-card:hover {
      transform: translateY(-8px);
      border-color: var(--primary);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
    }

    .card-title {
      font-size: 1.2rem;
      font-weight: 700;
      color: var(--text);
      margin-bottom: 10px;
    }

    .card-desc {
      color: rgba(224, 230, 237, 0.7);
      font-size: 0.9rem;
      line-height: 1.5;
    }

    /* Buttons */
    .btn {
      display: inline-block;
      padding: 12px 20px;
      border-radius: 10px;
      font-weight: 700;
      font-size: 0.9rem;
      text-align: center;
      text-decoration: none;
      cursor: pointer;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
      border: none;
      margin-top: 15px;
    }

    .btn::before {
      content: "";
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      transition: left 0.5s ease;
    }

    .btn:hover::before {
      left: 100%;
    }

    .btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
    }

    .btn-danger {
      background: linear-gradient(135deg, var(--danger), var(--warning));
      color: white;
    }
    .btn-danger:hover {
      box-shadow: 0 10px 20px rgba(255, 71, 87, 0.4);
    }

    .btn-success {
      background: linear-gradient(135deg, var(--success), var(--primary));
      color: #001;
    }
    .btn-success:hover {
      box-shadow: 0 10px 20px rgba(0, 255, 136, 0.4);
    }
    
    .btn-outline-danger {
      background: transparent;
      color: var(--danger);
      border: 1px solid var(--danger);
    }
    .btn-outline-danger:hover {
      background: rgba(255, 71, 87, 0.1);
      box-shadow: 0 10px 20px rgba(255, 71, 87, 0.2);
    }

    .btn-outline-success {
      background: transparent;
      color: var(--success);
      border: 1px solid var(--success);
    }
    .btn-outline-success:hover {
      background: rgba(0, 255, 136, 0.1);
      box-shadow: 0 10px 20px rgba(0, 255, 136, 0.2);
    }

    /* Tools & Info Card */
    .tools-card {
      background: rgba(26, 31, 58, 0.6);
      backdrop-filter: blur(10px);
      border-radius: 15px;
      padding: 25px;
      border: 1px solid rgba(0, 212, 255, 0.2);
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }

    .tools-card .stat {
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--primary);
    }
    
    .tools-card .small-muted {
      color: rgba(224, 230, 237, 0.7);
      font-size: 0.9rem;
    }

    .tools-card .actions {
      display: flex;
      gap: 10px;
    }

    /* Footer */
    footer {
      text-align: center;
      color: rgba(224, 230, 237, 0.6);
      font-size: 0.9rem;
      margin-top: 30px;
      padding: 20px;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .topbar {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
      }
      .topbar-right {
        width: 100%;
        justify-content: space-between;
      }
      .tools-card {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
      }
      .tools-card .actions {
        width: 100%;
      }
      .btn {
        flex-grow: 1;
        text-align: center;
      }
    }
  </style>
</head>
<body>
  <div class="wrap">
    <header class="topbar">
      <div class="brand-container">
        <div class="brand">LAB</div>
        <div class="user-info">
          <h1>Dashboard Demo Keamanan Web</h1>
          <p>Signed in as <strong><?php echo esc($user['username'] ?? ''); ?></strong></p>
        </div>
      </div>

      <div class="topbar-right">
        <div class="stats-mini">
          <div>Posts: <strong><?php echo is_null($stats['posts']) ? '—' : esc((string)$stats['posts']); ?></strong></div>
          <div>Comments: <strong><?php echo is_null($stats['comments']) ? '—' : esc((string)$stats['comments']); ?></strong></div>
        </div>
      </div>
    </header>

    <main class="card-grid">
      <!-- Vulnerable: Post -->
      <div class="dash-card">
        <div>
          <div class="card-title">post_vul.php</div>
          <div class="card-desc">Halaman posting yang menampilkan komentar raw (Stored XSS). Gunakan untuk demonstrasi XSS.</div>
        </div>
        <a href="post_vul.php" class="btn btn-danger">Buka (VULNERABLE)</a>
      </div>

      <!-- Vulnerable: Search -->
      <div class="dash-card">
        <div>
          <div class="card-title">search_vul.php</div>
          <div class="card-desc">Pencarian komentar yang rentan terhadap SQL Injection (concatenated query).</div>
        </div>
        <a href="search_vul.php" class="btn btn-danger">Buka (VULNERABLE)</a>
      </div>

      <!-- Safe: Post -->
      <div class="dash-card">
        <div>
          <div class="card-title">post_safe.php</div>
          <div class="card-desc">Versi aman: komentar di-escape, CSRF & owner-only delete. Gunakan untuk perbandingan.</div>
        </div>
        <a href="post_safe.php" class="btn btn-success">Buka (SAFE)</a>
      </div>

      <!-- Safe: Search -->
      <div class="dash-card">
        <div>
          <div class="card-title">search_safe.php</div>
          <div class="card-desc">Pencarian aman: prepared statements dan hasil di-escape. Untuk perbandingan dengan versi vulnerable.</div>
        </div>
        <a href="search_safe.php" class="btn btn-success">Buka (SAFE)</a>
      </div>
    </main>

    <section class="tools-card">
      <div>
        <div class="card-title">Tools & Info</div>
        <div class="card-desc">Gunakan tombol di atas untuk membuka halaman demo. Ingat: jalankan hanya di lingkungan lab/terisolasi.</div>
      </div>
      <div style="text-align:right;">
        <div class="small-muted">Users</div>
        <div class="stat"><?php echo is_null($stats['users']) ? '—' : esc((string)$stats['users']); ?></div>
      </div>
    </section>
    
    <div class="tools-card actions">
      <a href="post_vul.php?id=1" class="btn btn-outline-danger">Open vulnerable post (sample)</a>
      <a href="post_safe.php?id=1" class="btn btn-outline-success">Open safe post (sample)</a>
    </div>

    <footer>
      <div>Tip: untuk demo, siapkan beberapa akun dummy (alice, bob) dan beberapa komentar XSS/SQLi di DB.</div>
    </footer>
  </div>
</body>
</html>