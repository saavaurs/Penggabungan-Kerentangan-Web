<?php
// login.php
require 'auth_simple.php'; // tetap panggil jika Anda butuh helper current_user(), dll.
 $err = '';

require '../../global_header.php';
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

    .card-login {
      position: relative;
      z-index: 2;
      width: 100%;
      max-width: 420px;
      background: rgba(26, 31, 58, 0.8);
      backdrop-filter: blur(10px);
      padding: 2.5rem;
      border-radius: 20px;
      border: 1px solid rgba(0, 212, 255, 0.2);
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
      width: 72px;
      height: 72px;
      border-radius: 15px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      box-shadow: 0 8px 24px rgba(0, 212, 255, 0.3);
      font-weight: 700;
      font-size: 24px;
      color: #001;
      margin: 0 auto 1rem;
      animation: pulse 2s infinite;
    }

    @keyframes pulse {
      0%, 100% {
        transform: scale(1);
        box-shadow: 0 8px 24px rgba(0, 212, 255, 0.3);
      }
      50% {
        transform: scale(1.05);
        box-shadow: 0 8px 30px rgba(0, 212, 255, 0.5);
      }
    }

    h4 {
      font-size: 1.8rem;
      font-weight: 700;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
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
      color: var(--danger);
      background: rgba(255, 71, 87, 0.1);
      border-left: 4px solid var(--danger);
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

    .form-label {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .form-link {
      color: var(--primary);
      text-decoration: none;
      font-size: 0.8rem;
      transition: color 0.3s ease;
    }

    .form-link:hover {
      color: var(--secondary);
    }

    input {
      width: 100%;
      padding: 12px 15px;
      border-radius: 10px;
      border: 1px solid rgba(0, 212, 255, 0.2);
      background: rgba(26, 31, 58, 0.6);
      color: var(--text);
      font-size: 16px;
      transition: all 0.3s ease;
    }

    input:focus {
      outline: none;
      border-color: var(--primary);
      background: rgba(26, 31, 58, 0.8);
      box-shadow: 0 0 15px rgba(0, 212, 255, 0.3);
    }

    button {
      width: 100%;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      border: none;
      padding: 14px;
      border-radius: 10px;
      color: white;
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
      box-shadow: 0 10px 20px rgba(0, 212, 255, 0.4);
    }

    .form-footer {
      text-align: center;
      margin-top: 1.5rem;
      font-size: 0.9rem;
      color: rgba(224, 230, 237, 0.7);
    }

    .form-footer a {
      color: var(--primary);
      text-decoration: none;
      font-weight: 600;
      transition: color 0.3s ease;
    }

    .form-footer a:hover {
      color: var(--secondary);
    }

    .card-footer {
      margin-top: 1.5rem;
      padding-top: 1.5rem;
      border-top: 1px solid rgba(0, 212, 255, 0.1);
      text-align: center;
      font-size: 0.8rem;
      color: rgba(224, 230, 237, 0.6);
    }

    .card-footer code {
      background: rgba(0, 212, 255, 0.1);
      padding: 2px 6px;
      border-radius: 4px;
      font-family: 'Courier New', Courier, monospace;
      color: var(--primary);
    }

    @media (max-width: 600px) {
      .card-login {
        padding: 1.5rem;
      }
      
      h4 {
        font-size: 1.5rem;
      }
    }
  </style>
</head>
<body>
  <div class="card-login">
    <div class="brand-container">
      <div class="brand">XSS</div>
      <h4>Selamat datang</h4>
      <small class="subtitle">Masuk untuk melanjutkan</small>
    </div>

    <?php if($err): ?>
      <div class="alert">
        <?= htmlspecialchars($err); ?>
      </div>
    <?php endif; ?>

    <form method="post" novalidate>
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']); ?>">

      <div class="form-group">
        <label for="username">Username</label>
        <input id="username" name="username" placeholder="masukkan username" required
               value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
      </div>

      <div class="form-group">
        <div class="form-label">
          <label for="password">Password</label>
          <a href="#" class="form-link">Lupa password?</a>
        </div>
        <input id="password" name="password" type="password" placeholder="••••••••" required>
      </div>

      <button type="submit">Masuk</button>
    </form>

    <div class="form-footer">
      <span>Belum punya akun? <a href="register.php">Daftar</a></span>
    </div>
    
    <div class="card-footer">
      Untuk keperluan lab: password bisa berupa plaintext. Di produksi, gunakan <code>password_hash()</code>.
    </div>
  </div>

  <script>
    // focus ke username pada load
    document.getElementById('username')?.focus();
  </script>
</body>
</html>