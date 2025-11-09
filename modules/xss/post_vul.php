<?php
// post_vul.php (VULNERABLE - intentionally for lab demo)
// - Komentar DITAMPILKAN RAW (stored XSS enabled)
// - Tetap memakai CSRF & owner-check agar demo terkontrol (optional)
// - Jangan gunakan di server publik

require 'auth_simple.php';

 $pdo = pdo_connect();
 $post_id = (int)($_GET['id'] ?? 1);
 $user = current_user(); // may be null

// ensure session CSRF token exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
}

 $msg = '';
 $err = '';

// Handle new comment (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'post_comment') {
    if (!$user) {
        $err = 'Anda harus login untuk mengirim komentar.';
    } else {
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            $err = 'Request tidak valid (CSRF).';
        } else {
            $comment = trim($_POST['comment'] ?? '');
            if ($comment === '') {
                $err = 'Komentar tidak boleh kosong.';
            } elseif (mb_strlen($comment) > 2000) {
                $err = 'Komentar terlalu panjang (maks 2000 karakter).';
            } else {
                $stmt = $pdo->prepare("INSERT INTO comments (user_id, post_id, comment, created_at) VALUES (:uid, :pid, :c, NOW())");
                $stmt->execute([
                    ':uid' => $user['id'],
                    ':pid' => $post_id,
                    ':c'   => $comment
                ]);
                header("Location: post_vul.php?id=$post_id");
                exit;
            }
        }
    }
}

// Handle delete comment (POST) - only owner allowed
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_comment') {
    if (!$user) {
        $err = 'Anda harus login untuk menghapus komentar.';
    } else {
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            $err = 'Request tidak valid (CSRF).';
        } else {
            $del_id = (int)($_POST['delete_comment_id'] ?? 0);
            if ($del_id <= 0) {
                $err = 'ID komentar tidak valid.';
            } else {
                $stmt = $pdo->prepare("SELECT user_id FROM comments WHERE id = :cid");
                $stmt->execute([':cid' => $del_id]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$row) {
                    $err = 'Komentar tidak ditemukan.';
                } elseif ((int)$row['user_id'] !== (int)$user['id']) {
                    $err = 'Anda tidak berhak menghapus komentar ini.';
                } else {
                    $stmt = $pdo->prepare("DELETE FROM comments WHERE id = :cid");
                    $stmt->execute([':cid' => $del_id]);
                    header("Location: post_vul.php?id=$post_id");
                    exit;
                }
            }
        }
    }
}

// Fetch post
 $stmt = $pdo->prepare("SELECT * FROM posts WHERE id=:id LIMIT 1");
 $stmt->execute([':id' => $post_id]);
 $post = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch comments
 $stmt = $pdo->prepare("
    SELECT c.*, u.username 
    FROM comments c 
    LEFT JOIN users u ON c.user_id = u.id 
    WHERE c.post_id = :pid 
    ORDER BY c.created_at DESC
");
 $stmt->execute([':pid' => $post_id]);
 $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title><?php echo htmlspecialchars($post['title'] ?? 'Post'); ?> — LAB (VULNERABLE)</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
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

    .container-main {
      position: relative;
      z-index: 2;
      max-width: 900px;
      margin: 36px auto;
      padding: 20px;
    }

    /* Header */
    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
      padding: 20px;
      background: rgba(26, 31, 58, 0.6);
      backdrop-filter: blur(10px);
      border-radius: 15px;
      border: 1px solid rgba(255, 71, 87, 0.3);
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
      width: 48px;
      height: 48px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 16px;
      color: #fff;
      background: linear-gradient(135deg, var(--danger), var(--warning));
      box-shadow: 0 8px 24px rgba(255, 71, 87, 0.3);
      animation: pulse 2s infinite;
    }

    @keyframes pulse {
      0%, 100% {
        transform: scale(1);
        box-shadow: 0 8px 24px rgba(255, 71, 87, 0.3);
      }
      50% {
        transform: scale(1.05);
        box-shadow: 0 8px 30px rgba(255, 71, 87, 0.5);
      }
    }

    .header h4 {
      font-size: 1.3rem;
      font-weight: 700;
      background: linear-gradient(135deg, var(--danger), var(--warning));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .header .note {
      color: rgba(224, 230, 237, 0.7);
      font-size: 0.9rem;
      margin-top: 5px;
    }

    .vuln-badge {
      font-size: 0.75rem;
      background: rgba(255, 71, 87, 0.2);
      color: var(--danger);
      padding: 6px 10px;
      border-radius: 999px;
      border: 1px solid var(--danger);
      font-weight: 700;
      animation: blink 2s infinite;
    }

    @keyframes blink {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.7; }
    }

    /* Post Card */
    .post-card {
      background: rgba(26, 31, 58, 0.6);
      backdrop-filter: blur(10px);
      border-radius: 15px;
      border: 1px solid rgba(255, 71, 87, 0.2);
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
      overflow: hidden;
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

    .post-body {
      padding: 28px;
    }

    .post-meta {
      color: rgba(224, 230, 237, 0.7);
      font-size: 0.9rem;
      margin-bottom: 15px;
    }

    /* Buttons */
    .btn {
      display: inline-block;
      padding: 8px 16px;
      border-radius: 8px;
      font-weight: 600;
      font-size: 0.9rem;
      text-align: center;
      text-decoration: none;
      cursor: pointer;
      transition: all 0.3s ease;
      border: none;
      margin: 0 5px;
    }

    .btn-sm {
      padding: 6px 12px;
      font-size: 0.8rem;
    }

    .btn-primary {
      background: linear-gradient(135deg, var(--danger), var(--warning));
      color: white;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(255, 71, 87, 0.3);
    }

    .btn-danger {
      background: linear-gradient(135deg, var(--danger), var(--warning));
      color: white;
    }

    .btn-danger:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(255, 71, 87, 0.3);
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

    /* Form Elements */
    .form-control {
      width: 100%;
      padding: 12px 15px;
      border-radius: 10px;
      border: 1px solid rgba(255, 71, 87, 0.3);
      background: rgba(26, 31, 58, 0.6);
      color: var(--text);
      font-size: 15px;
      transition: all 0.3s ease;
      resize: vertical;
      font-family: inherit;
    }

    .form-control:focus {
      outline: none;
      border-color: var(--danger);
      background: rgba(26, 31, 58, 0.8);
      box-shadow: 0 0 15px rgba(255, 71, 87, 0.3);
    }

    /* Comment Card */
    .comment-card {
      margin-top: 18px;
      padding: 18px;
      background: rgba(26, 31, 58, 0.4);
      border-radius: 10px;
      border: 1px solid rgba(255, 71, 87, 0.1);
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
      transition: all 0.3s ease;
    }

    .comment-card:hover {
      transform: translateY(-3px);
      border-color: rgba(255, 71, 87, 0.3);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
    }

    .comment-header {
      display: flex;
      justify-content: space-between;
      margin-bottom: 10px;
    }

    .comment-author {
      font-weight: 700;
      color: var(--danger);
    }

    .comment-time {
      color: rgba(224, 230, 237, 0.6);
      font-size: 0.8rem;
    }

    .raw-comment {
      white-space: pre-wrap;
      color: var(--text);
      line-height: 1.5;
      background: rgba(255, 71, 87, 0.05);
      padding: 10px;
      border-radius: 8px;
      border-left: 3px solid var(--danger);
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

    .alert-success {
      background: rgba(0, 255, 136, 0.1);
      border-left: 4px solid var(--success);
      color: var(--success);
    }

    /* Footer */
    .card-footer {
      padding: 15px 28px;
      background: rgba(255, 71, 87, 0.1);
      border-top: 1px solid rgba(255, 71, 87, 0.2);
      color: var(--danger);
      font-size: 0.8rem;
      font-weight: 600;
    }

    /* Utility */
    .text-muted {
      color: rgba(224, 230, 237, 0.7);
    }

    .mb-0 { margin-bottom: 0; }
    .mb-1 { margin-bottom: 0.5rem; }
    .mb-2 { margin-bottom: 1rem; }
    .mb-3 { margin-bottom: 1.5rem; }
    .mb-4 { margin-bottom: 2rem; }
    .mt-2 { margin-top: 1rem; }
    .mt-3 { margin-top: 1.5rem; }
    .d-flex { display: flex; }
    .d-inline-block { display: inline-block; }
    .justify-content-between { justify-content: space-between; }
    .align-items-center { align-items: center; }
    .gap-3 { gap: 1rem; }
    .text-end { text-align: right; }
    .small { font-size: 0.8rem; }

    hr {
      border-color: rgba(255, 71, 87, 0.2);
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
      
      .comment-header {
        flex-direction: column;
        gap: 5px;
      }
    }
  </style>
</head>
<body>
  <div class="container-main">
    <div class="header">
      <div class="brand-container">
        <div class="brand">LAB</div>
        <div>
          <h4>Contoh Artikel</h4>
          <div class="note">Halaman ini <strong>intentionally vulnerable</strong> untuk demo Stored XSS (komentar ditampilkan raw).</div>
        </div>
      </div>

      <div class="text-end">
        <span class="vuln-badge">VULNERABLE</span>
      </div>
    </div>

    <div class="post-card">
      <div class="post-body">
        <div class="d-flex justify-content-between mb-2">
          <div>
            <?php if ($user): ?>
              <small class="text-muted">Signed in as: <strong><?php echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?></strong></small>
            <?php else: ?>
              <a class="btn btn-primary btn-sm" href="login.php">Login</a>
            <?php endif; ?>
          </div>
          <?php if ($user): ?>
            <div>
              <a class="btn btn-outline-secondary btn-sm" href="logout.php">Logout</a>
              <a class="btn btn-outline-warning btn-sm" href="dashboard.php">Kembali</a>
            </div>
          <?php endif; ?>
        </div>

        <h2 class="mb-1"><?php echo htmlspecialchars($post['title'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></h2>
        <div class="post-meta mb-3">
          <?php echo htmlspecialchars($post['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?> 
          <?php if (!empty($post['author'])): ?> &nbsp;oleh <?php echo htmlspecialchars($post['author'], ENT_QUOTES, 'UTF-8'); ?> <?php endif; ?>
        </div>

        <div class="mb-4"><?php echo nl2br(htmlspecialchars($post['body'] ?? '', ENT_QUOTES, 'UTF-8')); ?></div>

        <hr>

        <h4>Tulis Komentar</h4>

        <?php if ($err): ?>
          <div class="alert alert-danger"><?php echo htmlspecialchars($err, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <?php if ($msg): ?>
          <div class="alert alert-success"><?php echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php if ($user): ?>
          <form method="post" class="mb-3" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="action" value="post_comment">
            <div class="mb-3">
              <textarea name="comment" rows="5" class="form-control" placeholder="Tulis komentar Anda (maks 2000 karakter)"><?php echo isset($_POST['comment']) ? htmlspecialchars($_POST['comment'], ENT_QUOTES, 'UTF-8') : ''; ?></textarea>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <div class="note">HTML dalam komentar akan dieksekusi — ini sengaja untuk demo XSS.</div>
              <div>
                <button type="submit" class="btn btn-primary">Kirim Komentar</button>
              </div>
            </div>
          </form>
        <?php else: ?>
          <p>Anda harus <a href="login.php">login</a> untuk mengirim komentar.</p>
        <?php endif; ?>

        <hr>

        <h4>Komentar</h4>

        <?php if (empty($comments)): ?>
          <p class="text-muted">Belum ada komentar.</p>
        <?php else: ?>
          <div class="mt-3">
            <?php foreach ($comments as $c): ?>
              <div class="comment-card mb-3">
                <div class="comment-header">
                  <div>
                    <span class="comment-author"><?php echo htmlspecialchars($c['username'] ?? 'Guest', ENT_QUOTES, 'UTF-8'); ?></span>
                    <div class="comment-time"><?php echo htmlspecialchars($c['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
                  </div>

                  <?php if ($user && (int)$c['user_id'] === (int)$user['id']): ?>
                    <div>
                      <form method="post" style="display:inline-block;" onsubmit="return confirm('Hapus komentar ini?');">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="action" value="delete_comment">
                        <input type="hidden" name="delete_comment_id" value="<?php echo (int)$c['id']; ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                      </form>
                    </div>
                  <?php endif; ?>
                </div>

                <div class="mt-2 raw-comment">
                  <!-- VULNERABLE: RAW output (stored XSS enabled) -->
                  <?php echo $c['comment']; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

      </div>

      <div class="card-footer">
        <strong>PERINGATAN:</strong> Halaman ini intentionally vulnerable (Stored XSS). 
        Gunakan hanya untuk latihan di lingkungan terisolasi.
      </div>
    </div>
  </div>
</body>
</html>