<?php
// register.php
require 'auth_simple.php';
$pdo = pdo_connect();
$msg = '';
$err = '';

if($_SERVER['REQUEST_METHOD']==='POST'){
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $full_name = trim($_POST['full_name'] ?? '');

    if($username && $password){
        try {
            // lab: simpan plaintext, di produksi wajib password_hash()
            $stmt = $pdo->prepare("INSERT INTO users (username, password, full_name) VALUES (:u,:p,:n)");
            $stmt->execute([':u'=>$username, ':p'=>$password, ':n'=>$full_name]);
            $msg = "User berhasil didaftarkan. Silakan login.";
        } catch (Exception $e) {
            $err = "Registrasi gagal: kemungkinan username sudah dipakai.";
        }
    } else {
        $err = "Username & password wajib diisi.";
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Register — Lab</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background: linear-gradient(120deg,#f8f9fa 0%, #e9eef8 100%); min-height:100vh; }
    .card-register { max-width:420px; margin:70px auto; border-radius:12px; box-shadow:0 8px 30px rgba(16,24,40,0.08); }
    .brand { width:72px; height:72px; border-radius:10px; display:inline-flex; align-items:center; justify-content:center; background:#fff; box-shadow:0 4px 12px rgba(16,24,40,0.06); font-weight:700; font-size:20px; color:#198754; }
  </style>
</head>
<body>
  <div class="card card-register">
    <div class="card-body p-4">
      <div class="text-center mb-3">
        <div class="brand mx-auto mb-2">XSS</div>
        <h4 class="card-title mb-0">Buat Akun Baru</h4>
        <small class="text-muted">Isi form berikut untuk registrasi</small>
      </div>

      <?php if($msg): ?>
        <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
      <?php endif; ?>
      <?php if($err): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
      <?php endif; ?>

      <form method="post" novalidate>
        <div class="mb-3">
          <label for="username" class="form-label">Username</label>
          <input id="username" name="username" class="form-control" placeholder="Pilih username unik" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input id="password" name="password" type="password" class="form-control" placeholder="••••••••" required>
        </div>
        <div class="mb-3">
          <label for="full_name" class="form-label">Nama Lengkap</label>
          <input id="full_name" name="full_name" class="form-control" placeholder="Nama Anda">
        </div>
        <div class="d-grid">
          <button class="btn btn-success" type="submit">Daftar</button>
        </div>
      </form>

      <div class="text-center mt-3">
        <span class="small">Sudah punya akun? <a href="login.php">Login</a></span>
      </div>
    </div>
    <div class="card-footer text-center small text-muted">
      ⚠️ Lab demo: password disimpan plaintext. Produksi harus gunakan <code>password_hash()</code>.
    </div>
  </div>
</body>
</html>
