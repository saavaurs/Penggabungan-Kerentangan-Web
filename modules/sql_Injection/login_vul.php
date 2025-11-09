<?php
// login_vul.php  (VERSI RENTAN — DEMO)
session_start();

 $dsn = 'mysql:host=127.0.0.1;port=3306;dbname=praktek_sqli;charset=utf8mb4';
 $dbUser = 'root';
 $dbPass = '';
 $message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    try {
        $pdo = new PDO($dsn, $dbUser, $dbPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        // --- pola rentan: concatenation langsung dengan input user ---
        $sql = "SELECT id, username, password, full_name FROM users_vul
                WHERE username = '" . $username . "' AND password = '" . $password . "'";
        $stmt = $pdo->query($sql);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['demo_mode'] = 'vul';
            header('Location: dashboard.php');
            exit;
        } else {
            $message = 'Username atau password salah.';
        }
    } catch (PDOException $e) {
        $message = 'Terjadi kesalahan server.';
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Login — VERSI RENTAN (Demo)</title>
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

    .box {
      position: relative;
      z-index: 2;
      width: 100%;
      max-width: 420px;
      background: rgba(26, 31, 58, 0.8);
      backdrop-filter: blur(10px);
      padding: 2.5rem;
      border-radius: 20px;
      border: 1px solid rgba(255, 71, 87, 0.3);
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

    h3 {
      text-align: center;
      margin-bottom: 2rem;
      background: linear-gradient(135deg, var(--danger), var(--warning));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      font-size: 2rem;
      font-weight: bold;
      position: relative;
    }

    h3::after {
      content: "";
      position: absolute;
      bottom: -10px;
      left: 50%;
      transform: translateX(-50%);
      width: 80px;
      height: 3px;
      background: linear-gradient(90deg, var(--danger), var(--warning));
      border-radius: 3px;
    }

    label {
      display: block;
      font-weight: 600;
      margin-bottom: 8px;
      color: var(--text);
      font-size: 0.9rem;
      margin-top: 10px;
    }

    input {
      width: 100%;
      padding: 12px 15px;
      border-radius: 10px;
      border: 1px solid rgba(255, 71, 87, 0.3);
      background: rgba(26, 31, 58, 0.6);
      color: var(--text);
      font-size: 16px;
      transition: all 0.3s ease;
    }

    input:focus {
      outline: none;
      border-color: var(--danger);
      background: rgba(26, 31, 58, 0.8);
      box-shadow: 0 0 15px rgba(255, 71, 87, 0.3);
    }

    button {
      width: 100%;
      background: linear-gradient(135deg, var(--danger), var(--warning));
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
      margin-top: 25px;
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
      box-shadow: 0 10px 20px rgba(255, 71, 87, 0.4);
    }

    .message {
      background: rgba(255, 71, 87, 0.1);
      border-left: 4px solid var(--danger);
      padding: 15px;
      border-radius: 10px;
      margin-bottom: 20px;
      color: var(--danger);
      font-weight: 600;
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

    .note {
      font-size: 13px;
      color: rgba(224, 230, 237, 0.7);
      margin-top: 20px;
      text-align: center;
      line-height: 1.5;
      padding: 15px;
      background: rgba(255, 71, 87, 0.1);
      border-radius: 10px;
      border: 1px solid rgba(255, 71, 87, 0.2);
    }

    .note strong {
      color: var(--danger);
      font-weight: bold;
    }

    .back {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      color: var(--text);
      text-decoration: none;
      margin-bottom: 20px;
      font-weight: 600;
      transition: all 0.3s ease;
      padding: 8px 15px;
      border-radius: 8px;
      background: rgba(26, 31, 58, 0.4);
      border: 1px solid rgba(255, 71, 87, 0.2);
    }

    .back:hover {
      color: var(--danger);
      transform: translateX(-5px);
      border-color: var(--danger);
    }

    .warning-badge {
      display: inline-block;
      background: rgba(255, 71, 87, 0.2);
      color: var(--danger);
      padding: 5px 12px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: bold;
      margin-bottom: 15px;
      border: 1px solid var(--danger);
      animation: blink 2s infinite;
    }

    @keyframes blink {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.7; }
    }

    @media (max-width: 600px) {
      .box {
        padding: 1.5rem;
      }
      
      h3 {
        font-size: 1.5rem;
      }
    }
  </style>
</head>
<body>
  <div class="box">
    <a href="index.php" class="back">← Kembali</a>
    
    <div class="warning-badge">⚠️ VULNERABLE DEMO</div>
    
    <h3>🔓 Login — Versi Rentan (Demo)</h3>
    
    <?php if ($message): ?>
      <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    
    <form method="post" action="">
      <label>Username</label>
      <input name="username" type="text" required>
      
      <label>Password</label>
      <input name="password" type="password" required>
      
      <button type="submit">Login</button>
    </form>
    
    <p class="note">
      <strong>Peringatan:</strong> Contoh ini <strong>sengaja rentan</strong> (concatenation, password plaintext).<br>
      Jalankan hanya di lingkungan lokal yang terisolasi untuk tujuan pembelajaran.
    </p>
  </div>
</body>
</html>