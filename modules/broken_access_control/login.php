<?php
// vuln/login.php
session_start();
require_once 'config.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            
            header('Location: index.php');
            exit;
        } else {
            $message = 'Invalid username or password.';
        }
    } catch (PDOException $e) {
        $message = 'Database error occurred.';
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Login - Broken Access Control</title>
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
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
    }

    body::before {
      content: "";
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: radial-gradient(circle at 20% 50%, rgba(0, 212, 255, 0.1) 0%, transparent 50%),
                  radial-gradient(circle at 80% 80%, rgba(255, 0, 255, 0.1) 0%, transparent 50%);
      pointer-events: none;
      z-index: 1;
    }

    .container {
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

    h2 {
      text-align: center;
      margin-bottom: 2rem;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      font-size: 2rem;
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
      padding: 8px 15px;
      border-radius: 8px;
      background: rgba(26, 31, 58, 0.4);
      border: 1px solid rgba(0, 212, 255, 0.1);
      transition: all 0.3s ease;
    }

    .back:hover {
      color: var(--primary);
      transform: translateX(-5px);
      border-color: var(--primary);
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
      margin-top: 25px;
    }

    button:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 20px rgba(0, 212, 255, 0.4);
    }

    .message {
      background: rgba(255, 71, 87, 0.1);
      border-left: 4px solid var(--danger);
      padding: 15px;
      border-radius: 10px;
      margin-bottom: 20px;
      color: var(--danger);
      font-weight: 600;
    }

    .info {
      font-size: 13px;
      color: rgba(224, 230, 237, 0.7);
      margin-top: 20px;
      text-align: center;
      line-height: 1.5;
      padding: 15px;
      background: rgba(26, 31, 58, 0.4);
      border-radius: 10px;
      border: 1px solid rgba(0, 212, 255, 0.1);
    }

    .info strong {
      color: var(--primary);
    }

    .credentials {
      margin-top: 15px;
      padding: 10px;
      background: rgba(0, 212, 255, 0.05);
      border-radius: 8px;
      font-size: 12px;
    }

    .credentials div {
      margin: 5px 0;
    }
  </style>
</head>
<body>
  <div class="container">
    <a href="../index.php" class="back">← Back</a>
    
    <h2>🔓 Login</h2>

    <?php if ($message): ?>
      <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="post" action="">
      <label>Username</label>
      <input type="text" name="username" required autofocus>

      <label>Password</label>
      <input type="password" name="password" required>

      <button type="submit">Login</button>
    </form>

    <div class="info">
      <strong>Demo Credentials:</strong>
      <div class="credentials">
        <div>👤 <strong>alice</strong> / password123</div>
        <div>👤 <strong>bob</strong> / password123</div>
        <div>🔑 <strong>admin</strong> / password123</div>
      </div>
    </div>
  </div>
</body>
</html>