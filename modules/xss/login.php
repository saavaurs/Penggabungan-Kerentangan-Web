<?php
// login.php
require 'auth_simple.php'; // tetap panggil jika Anda butuh helper current_user(), dll.
$err = '';

// simple CSRF token (lab/demo)
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    // CSRF check
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $err = 'Invalid request (CSRF).';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $err = 'Username dan password wajib diisi.';
        } else {
            $pdo = pdo_connect();
            $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = :u LIMIT 1");
            $stmt->execute([':u' => $username]);
            $u = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($u) {
                // Prefer secure password verification (bcrypt/argon2)
                // But keep plaintext fallback for lab compatibility:
                $ok = false;
                if (password_verify($password, $u['password'])) {
                    $ok = true;
                } elseif ($password === $u['password']) { // legacy plaintext (lab only)
                    $ok = true;
                }

                if ($ok) {
                    // login success
                    // regenerate session id to prevent fixation
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $u['id'];
                    // optional: unset CSRF token so it's single-use
                    unset($_SESSION['csrf_token']);
                    // header('Location: post_vul.php?id=1');
                    header('Location: dashboard.php');
                    exit;
                } else {
                    $err = 'Login gagal: username atau password salah.';
                }
            } else {
                $err = 'Login gagal: username atau password salah.';
            }
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Login — Lab</title>

  <!-- Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body { background: linear-gradient(120deg,#f8f9fa 0%, #e9eef8 100%); min-height:100vh; }
    .card-login { max-width:420px; margin:70px auto; border-radius:12px; box-shadow: 0 8px 30px rgba(16,24,40,0.08); }
    .brand { width:72px; height:72px; border-radius:10px; display:inline-flex; align-items:center; justify-content:center; background:#fff; box-shadow:0 4px 12px rgba(16,24,40,0.06); font-weight:700; font-size:20px; color:#0d6efd; }
    .form-footer { font-size:0.9rem; }
  </style>
</head>
<body>
  <div class="card card-login">
    <div class="card-body p-4">
      <div class="text-center mb-3">
        <div class="brand mx-auto mb-2">XSS</div>
        <h4 class="card-title mb-0">Selamat datang</h4>
        <small class="text-muted">Masuk untuk melanjutkan</small>
      </div>

      <?php if($err): ?>
        <div class="alert alert-danger" role="alert">
          <?= htmlspecialchars($err); ?>
        </div>
      <?php endif; ?>

      <form method="post" novalidate>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']); ?>">

        <div class="mb-3">
          <label for="username" class="form-label">Username</label>
          <input id="username" name="username" class="form-control" placeholder="masukkan username" required
                 value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
        </div>

        <div class="mb-3">
          <label for="password" class="form-label d-flex justify-content-between">
            <span>Password</span>
            <a href="#" class="small">Lupa password?</a>
          </label>
          <input id="password" name="password" type="password" class="form-control" placeholder="••••••••" required>
        </div>

        <div class="d-grid">
          <button class="btn btn-primary" type="submit">Masuk</button>
        </div>
      </form>

      <div class="text-center mt-3 form-footer">
        <span>Belum punya akun? <a href="register.php">Daftar</a></span>
      </div>
    </div>
    <div class="card-footer text-center small text-muted">
      Untuk keperluan lab: password bisa berupa plaintext. Di produksi, gunakan <code>password_hash()</code>.
    </div>
  </div>

  <!-- Optional: Bootstrap JS (tidak wajib untuk form) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // focus ke username pada load
    document.getElementById('username')?.focus();
  </script>
</body>
</html>

