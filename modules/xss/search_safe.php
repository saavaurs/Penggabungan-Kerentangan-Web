<?php
// search_safe.php (SAFE version)
// - Uses prepared statements to prevent SQL injection.
// - Escapes all user content to prevent XSS.
// - Highlights matched query terms in escaped output (safe).
// - Styling uses Bootstrap and matches vulnerable UI for teaching comparison.
//
// Usage: place in same project that provides pdo_connect() and auth_simple.php
// DO NOT mix this file with vulnerable one on a public server (keep lab isolated).

require 'auth_simple.php';
 $pdo = pdo_connect();

 $q = trim((string)($_GET['q'] ?? ''));
 $results = [];
 $error = null;

if ($q !== '') {
    try {
        // safe prepared statement with case-insensitive search
        $sql = "SELECT c.id, u.username, c.comment, c.created_at
                FROM comments c
                LEFT JOIN users u ON c.user_id = u.id
                WHERE LOWER(c.comment) LIKE :q OR LOWER(u.username) LIKE :q
                ORDER BY c.created_at DESC
                LIMIT 200"; // safety limit for results
        $stmt = $pdo->prepare($sql);
        $like = '%' . mb_strtolower($q, 'UTF-8') . '%';
        $stmt->execute([':q' => $like]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // don't reveal DB error in production; log if needed
        $error = 'Terjadi kesalahan saat mencari. Coba lagi.';
    }
}

// helper: safely escape and optionally highlight query in escaped text
function safe_highlight(string $text, string $query): string {
    // escape first to avoid XSS
    $escaped = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    if ($query === '') return nl2br($escaped);

    // prepare safe regex of escaped query (preg_quote on escaped form prevents issues)
    $safe_q = htmlspecialchars($query, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $pattern = '/' . preg_quote($safe_q, '/') . '/iu'; // case-insensitive, unicode
    // wrap matches with <mark> (safe because $escaped is escaped; replacement uses captured group)
    $highlighted = preg_replace($pattern, '<mark>$0</mark>', $escaped);
    // convert newlines to <br>
    return nl2br($highlighted);
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Search Comments — SAFE</title>
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

    .search-card {
      position: relative;
      z-index: 2;
      max-width: 980px;
      margin: 42px auto;
      background: rgba(26, 31, 58, 0.8);
      backdrop-filter: blur(10px);
      padding: 30px;
      border-radius: 20px;
      border: 1px solid rgba(0, 255, 136, 0.2);
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

    /* Header */
    .header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 30px;
      padding: 20px;
      background: rgba(26, 31, 58, 0.6);
      backdrop-filter: blur(10px);
      border-radius: 15px;
      border: 1px solid rgba(0, 255, 136, 0.2);
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
      width: 56px;
      height: 56px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 16px;
      color: #001;
      background: linear-gradient(135deg, var(--success), var(--primary));
      box-shadow: 0 8px 24px rgba(0, 255, 136, 0.3);
      animation: pulse 2s infinite;
    }

    @keyframes pulse {
      0%, 100% {
        transform: scale(1);
        box-shadow: 0 8px 24px rgba(0, 255, 136, 0.3);
      }
      50% {
        transform: scale(1.05);
        box-shadow: 0 8px 30px rgba(0, 255, 136, 0.5);
      }
    }

    .header h4 {
      font-size: 1.3rem;
      font-weight: 700;
      background: linear-gradient(135deg, var(--success), var(--primary));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .header .note {
      color: rgba(224, 230, 237, 0.7);
      font-size: 0.9rem;
      margin-top: 5px;
    }

    .safe-badge {
      font-size: 0.75rem;
      background: rgba(0, 255, 136, 0.2);
      color: var(--success);
      padding: 6px 10px;
      border-radius: 999px;
      border: 1px solid var(--success);
      font-weight: 700;
    }

    /* Search Form */
    .search-form {
      display: flex;
      gap: 15px;
      margin-bottom: 20px;
    }

    .search-input {
      flex: 1;
      padding: 12px 15px;
      border-radius: 10px;
      border: 1px solid rgba(0, 255, 136, 0.2);
      background: rgba(26, 31, 58, 0.6);
      color: var(--text);
      font-size: 16px;
      transition: all 0.3s ease;
    }

    .search-input:focus {
      outline: none;
      border-color: var(--success);
      background: rgba(26, 31, 58, 0.8);
      box-shadow: 0 0 15px rgba(0, 255, 136, 0.3);
    }

    /* Buttons */
    .btn {
      display: inline-block;
      padding: 12px 20px;
      border-radius: 10px;
      font-weight: 600;
      font-size: 0.9rem;
      text-align: center;
      text-decoration: none;
      cursor: pointer;
      transition: all 0.3s ease;
      border: none;
    }

    .btn-success {
      background: linear-gradient(135deg, var(--success), var(--primary));
      color: #001;
    }

    .btn-success:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0, 255, 136, 0.3);
    }

    .btn-outline-secondary {
      background: transparent;
      color: var(--text);
      border: 1px solid rgba(224, 230, 237, 0.3);
    }

    .btn-outline-secondary:hover {
      background: rgba(224, 230, 237, 0.1);
      border-color: var(--text);
    }

    .btn-outline-warning {
      background: transparent;
      color: var(--warning);
      border: 1px solid var(--warning);
    }

    .btn-outline-warning:hover {
      background: rgba(255, 165, 2, 0.1);
      border-color: var(--warning);
    }

    .btn-sm {
      padding: 6px 12px;
      font-size: 0.8rem;
    }

    /* Results Section */
    .results-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      padding: 15px;
      background: rgba(26, 31, 58, 0.4);
      border-radius: 10px;
    }

    .results-header h5 {
      font-size: 1.1rem;
      font-weight: 700;
    }

    .results-header .note {
      color: rgba(224, 230, 237, 0.7);
      font-size: 0.8rem;
      margin-top: 5px;
    }

    .count-badge {
      font-weight: 600;
      color: var(--success);
      background: rgba(0, 255, 136, 0.1);
      padding: 5px 10px;
      border-radius: 20px;
      border: 1px solid var(--success);
    }

    /* Comment Card */
    .comment {
      padding: 18px;
      background: rgba(26, 31, 58, 0.4);
      border-radius: 10px;
      border: 1px solid rgba(0, 255, 136, 0.1);
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.2);
      margin-bottom: 15px;
      transition: all 0.3s ease;
    }

    .comment:hover {
      transform: translateY(-3px);
      border-color: rgba(0, 255, 136, 0.3);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
    }

    .comment-header {
      display: flex;
      justify-content: space-between;
      margin-bottom: 10px;
    }

    .comment-author {
      font-weight: 700;
      color: var(--primary);
    }

    .comment-time {
      color: rgba(224, 230, 237, 0.6);
      font-size: 0.8rem;
    }

    .comment-text {
      color: var(--text);
      line-height: 1.5;
      word-wrap: break-word;
    }

    /* Highlight */
    mark {
      background: rgba(255, 229, 143, 0.3);
      color: var(--warning);
      padding: 2px 4px;
      border-radius: 4px;
      font-weight: 600;
    }

    /* Alert */
    .alert {
      padding: 15px;
      border-radius: 10px;
      margin-bottom: 20px;
    }

    .alert-danger {
      background: rgba(255, 71, 87, 0.1);
      border-left: 4px solid var(--danger);
      color: var(--danger);
    }

    .alert-info {
      background: rgba(0, 212, 255, 0.1);
      border-left: 4px solid var(--primary);
      color: var(--primary);
    }

    /* Footer */
    .card-footer {
      margin-top: 20px;
      padding-top: 20px;
      border-top: 1px solid rgba(0, 255, 136, 0.1);
      text-align: center;
      font-size: 0.8rem;
      color: rgba(224, 230, 237, 0.6);
    }

    /* Utility */
    .text-muted {
      color: rgba(224, 230, 237, 0.7);
    }

    .text-end {
      text-align: right;
    }

    .text-break {
      word-wrap: break-word;
      word-break: break-word;
    }

    .mb-0 { margin-bottom: 0; }
    .mb-3 { margin-bottom: 1.5rem; }
    .me-3 { margin-right: 1rem; }
    .ms-auto { margin-left: auto; }
    .mt-2 { margin-top: 1rem; }
    .my-4 { margin-top: 1.5rem; margin-bottom: 1.5rem; }
    .d-flex { display: flex; }
    .g-2 > * + * { margin-left: 0.5rem; }
    .align-items-center { align-items: center; }
    .small { font-size: 0.8rem; }

    hr {
      border: none;
      height: 1px;
      background: linear-gradient(90deg, transparent, rgba(0, 255, 136, 0.2), transparent);
      margin: 20px 0;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
      }
      
      .header .text-end {
        text-align: left;
        width: 100%;
      }
      
      .search-form {
        flex-direction: column;
      }
      
      .results-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
      }
    }
  </style>
</head>
<body>
  <div class="search-card">
    <div class="header">
      <div class="brand-container">
        <div class="brand">SAFE</div>
        <div>
          <h4>Search Komentar (SAFE)</h4>
          <div class="note">Versi aman: prepared statements + escaping. Cocok untuk perbandingan dengan versi vulnerable.</div>
        </div>
      </div>
      <div class="text-end">
        <span class="safe-badge">SAFE</span>
        <a class="btn btn-outline-warning btn-sm" href="dashboard.php">Kembali</a>
      </div>
    </div>

    <form class="search-form" method="get" action="">
      <input name="q" class="search-input" placeholder="Cari komentar atau username..." value="<?php echo htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?>" autofocus>
      <button class="btn btn-success" type="submit">Search</button>
    </form>

    <?php if ($q !== ''): ?>
      <hr class="my-4">
      <div class="results-header">
        <div>
          <h5 class="mb-0">Hasil untuk: <small class="text-muted"><?php echo htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?></small></h5>
          <div class="note">Menampilkan komentar yang mengandung kata pencarian atau username (case-insensitive).</div>
        </div>
        <div class="text-end">
          <span class="count-badge"><?php echo count($results); ?> hasil</span>
        </div>
      </div>

      <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
      <?php endif; ?>

      <?php if (empty($results)): ?>
        <div class="alert alert-info">Tidak ada hasil untuk pencarian ini.</div>
      <?php else: ?>
        <div>
          <?php foreach ($results as $r): ?>
            <div class="comment">
              <div class="comment-header">
                <div>
                  <span class="comment-author"><?php echo htmlspecialchars($r['username'] ?? 'Guest', ENT_QUOTES, 'UTF-8'); ?></span>
                  <div class="comment-time"><?php echo htmlspecialchars($r['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
                <div>
                  <a href="#" class="btn btn-sm btn-outline-secondary">View</a>
                </div>
              </div>

              <div class="comment-text text-break">
                <!-- SAFE: escaped + highlighted -->
                <?php echo safe_highlight((string)$r['comment'], $q); ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    <?php endif; ?>

    <div class="card-footer">
      Catatan: file ini **aman** — menggunakan prepared statements dan escaping output. 
      Untuk demonstrasi perbandingan, bandingkan dengan `search_vul.php` (intentionally vulnerable).
    </div>
  </div>
</body>
</html>