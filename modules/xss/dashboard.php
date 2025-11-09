<?php
// dashboard.php
// Protected dashboard page. Requires login.
// Shows links to vulnerable/safe demo pages and Logout.

require 'auth_simple.php'; // harus tersedia di project
$pdo = pdo_connect();

$user = current_user();
if (!$user) {
    // jika belum login, redirect ke login
    header('Location: login.php');
    exit;
}

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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background: linear-gradient(120deg,#f7fbff 0%, #eef7f9 100%); min-height:100vh; font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, Arial; }
    .wrap { max-width:1100px; margin:36px auto; padding:18px; }
    .topbar { display:flex; align-items:center; gap:16px; justify-content:space-between; margin-bottom:18px; }
    .brand { display:inline-flex; width:56px; height:56px; border-radius:12px; align-items:center; justify-content:center; font-weight:700; color:#fff; background: linear-gradient(135deg,#0d6efd,#6610f2); box-shadow:0 6px 18px rgba(13,110,253,0.12); }
    .card-grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:16px; margin-top:18px; }
    .dash-card { border-radius:12px; padding:18px; background:#fff; box-shadow:0 10px 30px rgba(15,23,42,0.06); min-height:120px; display:flex; flex-direction:column; justify-content:space-between; }
    .card-title { font-size:1.05rem; font-weight:600; }
    .card-desc { color:#6c757d; font-size:.9rem; margin-top:6px; }
    .btn-block { width:100%; margin-top:12px; }
    .stat { font-size:1.25rem; font-weight:700; color:#343a40; }
    .small-muted { color:#6c757d; font-size:.9rem; }
    .danger-badge { background:#ffe9e9; color:#b02a37; padding:6px 10px; border-radius:999px; font-size:.75rem; }
    footer { margin-top:22px; text-align:center; color:#6c757d; font-size:.9rem; }
    @media (max-width:640px) {
      .topbar { flex-direction:column; align-items:flex-start; gap:10px; }
    }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="topbar">
      <div class="d-flex align-items-center gap-3">
        <div class="brand">LAB</div>
        <div>
          <div style="font-size:1.15rem; font-weight:700;">Dashboard Demo Keamanan Web</div>
          <div class="small-muted">Signed in as <strong><?php echo esc($user['username'] ?? ''); ?></strong></div>
        </div>
      </div>

      <div class="d-flex gap-2 align-items-center">
        <div class="text-end me-3 small-muted">
          <div>Posts: <strong><?php echo is_null($stats['posts']) ? '—' : esc((string)$stats['posts']); ?></strong></div>
          <div>Comments: <strong><?php echo is_null($stats['comments']) ? '—' : esc((string)$stats['comments']); ?></strong></div>
        </div>
        <a href="logout.php" class="btn btn-outline-secondary">Logout</a>
      </div>
    </div>

    <div class="card-grid">
      <!-- Vulnerable: Post -->
      <div class="dash-card">
        <div>
          <div class="card-title">post_vul.php</div>
          <div class="card-desc">Halaman posting yang menampilkan komentar raw (Stored XSS). Gunakan untuk demonstrasi XSS.</div>
        </div>
        <div class="mt-2">
          <a href="post_vul.php" class="btn btn-danger btn-block">Buka (VULNERABLE)</a>
        </div>
      </div>

      <!-- Vulnerable: Search -->
      <div class="dash-card">
        <div>
          <div class="card-title">search_vul.php</div>
          <div class="card-desc">Pencarian komentar yang rentan terhadap SQL Injection (concatenated query).</div>
        </div>
        <div class="mt-2">
          <a href="search_vul.php" class="btn btn-danger btn-block">Buka (VULNERABLE)</a>
        </div>
      </div>

      <!-- Safe: Post -->
      <div class="dash-card">
        <div>
          <div class="card-title">post_safe.php</div>
          <div class="card-desc">Versi aman: komentar di-escape, CSRF & owner-only delete. Gunakan untuk perbandingan.</div>
        </div>
        <div class="mt-2">
          <a href="post_safe.php" class="btn btn-success btn-block">Buka (SAFE)</a>
        </div>
      </div>

      <!-- Safe: Search -->
      <div class="dash-card">
        <div>
          <div class="card-title">search_safe.php</div>
          <div class="card-desc">Pencarian aman: prepared statements dan hasil di-escape. Untuk perbandingan dengan versi vulnerable.</div>
        </div>
        <div class="mt-2">
          <a href="search_safe.php" class="btn btn-success btn-block">Buka (SAFE)</a>
        </div>
      </div>
    </div>

    <div style="margin-top:20px;">
      <div class="card dash-card">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="card-title">Tools & Info</div>
            <div class="card-desc">Gunakan tombol di atas untuk membuka halaman demo. Ingat: jalankan hanya di lingkungan lab/terisolasi.</div>
          </div>
          <div style="text-align:right;">
            <div class="small-muted">Users</div>
            <div class="stat"><?php echo is_null($stats['users']) ? '—' : esc((string)$stats['users']); ?></div>
          </div>
        </div>

        <div style="margin-top:12px;">
          <a href="post_vul.php?id=1" class="btn btn-outline-danger me-2">Open vulnerable post (sample)</a>
          <a href="post_safe.php?id=1" class="btn btn-outline-success">Open safe post (sample)</a>
        </div>
      </div>
    </div>

    <footer>
      <div>Tip: untuk demo, siapkan beberapa akun dummy (alice, bob) dan beberapa komentar XSS/SQLi di DB.</div>
    </footer>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
