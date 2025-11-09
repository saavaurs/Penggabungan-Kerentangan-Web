<?php
// dashboard.php
session_start();
if (!isset($_SESSION['user_id'])) {
    // arahkan ke halaman login sesuai kebutuhan manual saat demo
    header('Location: login_safe.php');
    exit;
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Dashboard</title>
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
      max-width: 600px;
      background: rgba(26, 31, 58, 0.8);
      backdrop-filter: blur(10px);
      padding: 3rem;
      border-radius: 20px;
      border: 1px solid rgba(0, 212, 255, 0.2);
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
      text-align: center;
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
      margin-bottom: 2rem;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      font-size: 2.5rem;
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
      background: linear-gradient(90deg, var(--primary), var(--secondary));
      border-radius: 3px;
    }

    p {
      font-size: 18px;
      color: var(--text);
      margin: 15px 0;
      line-height: 1.6;
    }

    .user-info {
      background: rgba(26, 31, 58, 0.4);
      padding: 20px;
      border-radius: 15px;
      margin: 2rem 0;
      border: 1px solid rgba(0, 212, 255, 0.1);
    }

    .user-info p {
      margin: 10px 0;
    }

    .label {
      color: var(--primary);
      font-weight: bold;
    }

    a {
      display: inline-block;
      margin-top: 30px;
      text-decoration: none;
      background: linear-gradient(135deg, var(--danger), var(--warning));
      color: white;
      padding: 12px 25px;
      border-radius: 10px;
      font-weight: bold;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    a::before {
      content: "";
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      transition: left 0.5s ease;
    }

    a:hover::before {
      left: 100%;
    }

    a:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 20px rgba(255, 71, 87, 0.4);
    }

    .mode-badge {
      display: inline-block;
      padding: 5px 15px;
      border-radius: 20px;
      font-weight: bold;
      margin-top: 10px;
      font-size: 14px;
    }

    .mode-safe {
      background: rgba(0, 255, 136, 0.2);
      color: var(--success);
      border: 1px solid var(--success);
    }

    .mode-vul {
      background: rgba(255, 71, 87, 0.2);
      color: var(--danger);
      border: 1px solid var(--danger);
    }

    @media (max-width: 600px) {
      .container {
        padding: 2rem;
      }
      
      h2 {
        font-size: 2rem;
      }
    }
  </style>
</head>
<body>
  <div class="container">
      <h2>DASHBOARD</h2>
      
      <div class="user-info">
        <p><span class="label">Selamat datang,</span> <?=htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username'])?></p>
        <p><span class="label">Mode demo:</span> <?=htmlspecialchars($_SESSION['demo_mode'] ?? 'unknown')?></p>
        <div class="mode-badge mode-<?=htmlspecialchars($_SESSION['demo_mode'] ?? 'unknown')?>">
          <?=htmlspecialchars($_SESSION['demo_mode'] ?? 'unknown') === 'safe' ? 'AMAN' : 'RENTAN'?>
        </div>
      </div>
      
      <p><a href="logout.php">Logout</a></p>
  </div>
</body>
</html>