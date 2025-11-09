<?php
// vuln/logout.php
session_start();
$_SESSION = [];
session_destroy();
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Logging Out...</title>
  <meta http-equiv="refresh" content="2;url=login.php">
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
      background: radial-gradient(circle at 20% 50%, rgba(0, 212, 255, 0.1) 0%, transparent 50%);
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
      margin-bottom: 1.5rem;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      font-size: 2.5rem;
      font-weight: bold;
    }

    p {
      font-size: 18px;
      color: rgba(224, 230, 237, 0.8);
      margin: 15px 0;
    }

    .spinner {
      margin: 2rem auto;
      width: 50px;
      height: 50px;
      border: 4px solid rgba(0, 212, 255, 0.2);
      border-top: 4px solid var(--primary);
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    a {
      display: inline-block;
      margin-top: 20px;
      text-decoration: none;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      color: white;
      padding: 12px 25px;
      border-radius: 10px;
      font-weight: bold;
      transition: all 0.3s ease;
    }

    a:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 20px rgba(0, 212, 255, 0.4);
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Logging Out</h2>
    <p>You have been logged out successfully.</p>
    <div class="spinner"></div>
    <p>Redirecting to login page...</p>
    <p>If not redirected, <a href="login.php">click here</a>.</p>
  </div>
</body>
</html>