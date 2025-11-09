<?php
// create_user_vul_form.php
// DEMO ONLY: VULNERABLE user creation form — gunakan hanya di lab lokal/VM

 $dsn = 'mysql:host=127.0.0.1;port=3306;dbname=praktek_sqli;charset=utf8mb4';
 $dbUser = 'root';
 $dbPass = ''; // sesuaikan jika perlu

 $message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $fullname = $_POST['full_name'] ?? '';

    try {
        $pdo = new PDO($dsn, $dbUser, $dbPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        // VULNERABLE: menyimpan password plaintext and concatenation query
        $sql = "INSERT INTO users_vul (username, password, full_name) VALUES ('" 
                . $username . "', '" . $password . "', '" . $fullname . "')";
        $pdo->exec($sql);

        $message = "User rentan berhasil dibuat: " . htmlspecialchars($username);

    } catch (PDOException $e) {
        $message = "Terjadi kesalahan server (demo).";
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Create User (VULNERABLE)</title>
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

    .container {
      position: relative;
      z-index: 2;
      width: 100%;
      max-width: 500px;
      background: rgba(26, 31, 58, 0.8);
      backdrop-filter: blur(10px);
      padding: 2.5rem;
      border-radius: 20px;
      border: 1px solid rgba(255, 0, 255, 0.2);
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

    h2 {
      text-align: center;
      margin-bottom: 2rem;
      background: linear-gradient(135deg, var(--danger), var(--warning));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      font-size: 2rem;
      font-weight: bold;
      position: relative;
    }

    h2::after {
      content: "";
      position: absolute;
      bottom: -10px;
      left: 50%;
      transform: translateX(-50%);
      width: 100px;
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
    }

    input {
      width: 100%;
      padding: 12px 15px;
      border-radius: 10px;
      border: 1px solid rgba(255, 0, 255, 0.2);
      background: rgba(26, 31, 58, 0.6);
      color: var(--text);
      margin-bottom: 20px;
      font-size: 16px;
      transition: all 0.3s ease;
    }

    input:focus {
      outline: none;
      border-color: var(--secondary);
      background: rgba(26, 31, 58, 0.8);
      box-shadow: 0 0 15px rgba(255, 0, 255, 0.3);
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
      background: rgba(255, 165, 2, 0.1);
      border-left: 4px solid var(--warning);
      padding: 15px;
      border-radius: 10px;
      margin-bottom: 20px;
      color: var(--warning);
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
      background: rgba(26, 31, 58, 0.4);
      border-radius: 10px;
      border: 1px solid rgba(255, 0, 255, 0.1);
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
      border: 1px solid rgba(255, 0, 255, 0.1);
    }

    .back:hover {
      color: var(--secondary);
      transform: translateX(-5px);
      border-color: var(--secondary);
    }

    @media (max-width: 600px) {
      .container {
        padding: 1.5rem;
      }
      
      h2 {
        font-size: 1.5rem;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <a class="back" href="index.php">← Kembali</a>

    <h2>CREATE USER — VERSI RENTAN (DEMO)</h2>

    <?php if ($message): ?>
      <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="post" action="">
      <label>Username</label>
      <input type="text" name="username" required>

      <label>Password</label>
      <input type="text" name="password" required>

      <label>Full name</label>
      <input type="text" name="full_name">

      <button type="submit">Buat User (vul)</button>
    </form>

    <p class="note">Catatan: contoh ini <strong style="color:var(--danger)">rentan</strong> — tidak gunakan di lingkungan publik.</p>
  </div>
</body>
</html>