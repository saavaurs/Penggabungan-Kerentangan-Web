<?php
require 'config.php';

 $user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Dashboard — <?= htmlspecialchars($user['username']) ?></title>
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
      max-width: 900px;
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

    h1 {
      text-align: center;
      margin-bottom: 3rem;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      font-size: 2.5rem;
      font-weight: bold;
      animation: pulse 2s infinite;
    }

    @keyframes pulse {
      0%, 100% {
        transform: scale(1);
      }
      50% {
        transform: scale(1.02);
      }
    }

    .areas-container {
      display: flex;
      gap: 2rem;
    }

    /* Area Cards */
    .area-card {
      flex: 1;
      background: rgba(26, 31, 58, 0.6);
      backdrop-filter: blur(10px);
      padding: 2rem;
      border-radius: 20px;
      border: 1px solid;
      transition: all 0.3s ease;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .area-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
    }

    .area-card.vulnerable {
      border-color: rgba(255, 71, 87, 0.3);
      animation: danger-pulse 3s infinite;
    }

    @keyframes danger-pulse {
      0%, 100% {
        box-shadow: 0 0 15px rgba(255, 71, 87, 0.2);
      }
      50% {
        box-shadow: 0 0 25px rgba(255, 71, 87, 0.4);
      }
    }

    .area-card.safe {
      border-color: rgba(0, 255, 136, 0.3);
    }

    .area-card h3 {
      font-size: 1.5rem;
      margin-bottom: 1rem;
      font-weight: 700;
    }

    .area-card.vulnerable h3 {
      color: var(--danger);
    }

    .area-card.safe h3 {
      color: var(--success);
    }

    .area-card p {
      color: rgba(224, 230, 237, 0.7);
      line-height: 1.6;
      margin-bottom: 2rem;
      flex-grow: 1;
    }

    /* Links as Buttons */
    .area-card a {
      display: inline-block;
      padding: 12px 25px;
      border-radius: 10px;
      font-weight: 700;
      font-size: 1rem;
      text-decoration: none;
      cursor: pointer;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
      text-align: center;
    }

    .area-card a::before {
      content: "";
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      transition: left 0.5s ease;
    }

    .area-card a:hover::before {
      left: 100%;
    }

    .area-card.vulnerable a {
      background: linear-gradient(135deg, var(--danger), var(--warning));
      color: white;
    }

    .area-card.vulnerable a:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 20px rgba(255, 71, 87, 0.4);
    }

    .area-card.safe a {
      background: linear-gradient(135deg, var(--success), var(--primary));
      color: #001;
    }

    .area-card.safe a:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 20px rgba(0, 255, 136, 0.4);
    }

    /* Responsive */
    @media (max-width: 768px) {
      .areas-container {
        flex-direction: column;
      }
      
      h1 {
        font-size: 2rem;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Dashboard — <?= htmlspecialchars($user['username']) ?></h1>

    <div class="areas-container">
      <div class="area-card vulnerable">
        <h3>⚠️ VULNERABLE AREA</h3>
        <p>Contoh Broken Access Control (IDOR) — tanpa validasi ownership.</p>
        <a href="vuln/list.php">Masuk ke area VULN</a>
      </div>

      <div class="area-card safe">
        <h3>✅ SAFE AREA</h3>
        <p>Versi aman dengan UUID + Token + Ownership Check.</p>
        <a href="safe/list.php">Masuk ke area SAFE</a>
      </div>
    </div>
  </div>
</body>
</html>