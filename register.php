<?php
// register.php
require 'auth.php';
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
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
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
          rgba(0, 255, 136, 0.1) 0%,
          transparent 50%
        ),
        radial-gradient(
          circle at 80% 80%,
          rgba(255, 0, 255, 0.1) 0%,
          transparent 50%
        ),
        radial-gradient(
          circle at 40% 20%,
          rgba(0, 212, 255, 0.05) 0%,
          transparent 50%
        );
      pointer-events: none;
      z-index: 1;
    }

    .card-register {
      position: relative;
      z-index: 2;
      width: 100%;
      max-width: 420px;
      background: rgba(26, 31, 58, 0.8);
      backdrop-filter: blur(10px);
      padding: 2rem;
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

    .brand-container {
      text-align: center;
      margin-bottom: 2rem;
    }

    .brand {
      width: 150px;
      height: 72px;
      border-radius: 15px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, var(--success), var(--primary));
      box-shadow: 0 8px 24px rgba(0, 255, 136, 0.3);
      font-weight: 700;
      font-size: 24px;
      color: #001;
      margin: 0 auto 1rem;
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

    h4 {
      font-size: 1.8rem;
      font-weight: 700;
      background: linear-gradient(135deg, var(--success), var(--primary));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 0.5rem;
    }

    .subtitle {
      color: rgba(224, 230, 237, 0.7);
      font-size: 0.9rem;
    }

    .alert {
      padding: 15px;
      border-radius: 10px;
      margin-bottom: 1.5rem;
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

    .alert-success {
      background: rgba(0, 255, 136, 0.1);
      border-left: 4px solid var(--success);
      color: var(--success);
    }

    .alert-danger {
      background: rgba(255, 71, 87, 0.1);
      border-left: 4px solid var(--danger);
      color: var(--danger);
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    label {
      display: block;
      font-weight: 600;
      margin-bottom: 8px;
      color: var(--text);
      font-size: 0.9rem;
    }

    input {
      width: 100%;
      padding: 12px 15px;
      border-radius: 10px;
      border: 1px solid rgba(0, 255, 136, 0.2);
      background: rgba(26, 31, 58, 0.6);
      color: var(--text);
      font-size: 16px;
      transition: all 0.3s ease;
    }

    input:focus {
      outline: none;
      border-color: var(--success);
      background: rgba(26, 31, 58, 0.8);
      box-shadow: 0 0 15px rgba(0, 255, 136, 0.3);
    }

    button {
      width: 100%;
      background: linear-gradient(135deg, var(--success), var(--primary));
      border: none;
      padding: 14px;
      border-radius: 10px;
      color: #001;
      font-weight: 700;
      font-size: 16px;
      cursor: pointer;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
      margin-top: 10px;
    }

    button::before {
      content: "";
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      transition: left 0.5s ease;
    }

    button:hover::before {
      left: 100%;
    }

    button:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 20px rgba(0, 255, 136, 0.4);
    }

    .form-footer {
      text-align: center;
      margin-top: 1.5rem;
      font-size: 0.9rem;
      color: rgba(224, 230, 237, 0.7);
    }

    .form-footer a {
      color: var(--success);
      text-decoration: none;
      font-weight: 600;
      transition: color 0.3s ease;
    }

    .form-footer a:hover {
      color: var(--primary);
    }

    .card-footer {
      margin-top: 1rem;
      padding-top: 1rem;
      border-top: 1px solid rgba(0, 255, 136, 0.1);
      text-align: center;
      font-size: 0.8rem;
      color: rgba(224, 230, 237, 0.6);
    }

    .card-footer code {
      background: rgba(0, 255, 136, 0.1);
      padding: 2px 6px;
      border-radius: 4px;
      font-family: 'Courier New', Courier, monospace;
      color: var(--success);
    }

    @media (max-width: 600px) {
      .card-register {
        padding: 1.5rem;
      }
      
      h4 {
        font-size: 1.5rem;
      }
    }
  </style>
</head>
<body>
  <div class="card-register">
    <div class="brand-container">
      <div class="brand">CyberLabs</div>
      <h4>Buat Akun Baru</h4>
      <small class="subtitle">Isi form berikut untuk registrasi</small>
    </div>

    <?php if($msg): ?>
      <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>
    <?php if($err): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
    <?php endif; ?>

    <form method="post" novalidate>
      <div class="form-group">
        <label for="username">Username</label>
        <input id="username" name="username" placeholder="Pilih username unik" required>
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input id="password" name="password" type="password" placeholder="••••••••" required>
      </div>
      <div class="form-group">
        <label for="full_name">Nama Lengkap</label>
        <input id="full_name" name="full_name" placeholder="Nama Anda">
      </div>
      <button type="submit">Daftar</button>
    </form>

    <div class="form-footer">
      <span>Sudah punya akun? <a href="login.php">Login</a></span>
    </div>
    <div class="card-footer">
      &copy; 2025 Sava-Eva-Navaro
    </div>
  </div>
</body>
</html>