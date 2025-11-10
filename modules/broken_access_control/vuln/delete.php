<?php
// vuln/delete.php
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['user'])) header('Location: ../login.php');

 $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { 
    http_response_code(400); 
    exit('Bad Request'); 
}

// VULNERABLE: no ownership check - any user can delete any item
// This is intentionally insecure for demonstration purposes
 $pdo->exec("DELETE FROM items_vuln WHERE id = $id");

// Redirect back to the list
header('Location: list.php'); 
exit;
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Item Deleted</title>
  <meta http-equiv="refresh" content="3;url=list.php"> <!-- Redirect after 3 seconds -->
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

    .container {
      position: relative;
      z-index: 2;
      width: 100%;
      max-width: 500px;
      background: rgba(26, 31, 58, 0.8);
      backdrop-filter: blur(10px);
      padding: 3rem;
      border-radius: 20px;
      border: 1px solid rgba(255, 71, 87, 0.3);
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
      background: linear-gradient(135deg, var(--danger), var(--warning));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      font-size: 2rem;
      font-weight: bold;
    }

    .icon {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 2rem;
      background: rgba(255, 71, 87, 0.2);
      border: 2px solid var(--danger);
      font-size: 2.5rem;
      color: var(--danger);
      animation: pulse 2s infinite;
    }

    @keyframes pulse {
      0%, 100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(255, 71, 87, 0.7);
      }
      50% {
        transform: scale(1.05);
        box-shadow: 0 0 0 10px rgba(255, 71, 87, 0);
      }
    }

    .message {
      font-size: 1.2rem;
      color: var(--text);
      margin-bottom: 2rem;
      line-height: 1.6;
    }

    .warning-text {
      font-size: 0.9rem;
      color: var(--danger);
      background: rgba(255, 71, 87, 0.1);
      padding: 15px;
      border-radius: 10px;
      border-left: 4px solid var(--danger);
      margin-bottom: 2rem;
      text-align: left;
    }

    .warning-text strong {
      color: var(--warning);
    }

    .countdown {
      margin-top: 2rem;
      font-size: 1rem;
      color: rgba(224, 230, 237, 0.7);
    }

    .countdown span {
      font-weight: bold;
      color: var(--primary);
      font-size: 1.2rem;
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
      box-shadow: 0 10px 20px rgba(0, 212, 255, 0.4);
    }

    /* Responsive */
    @media (max-width: 600px) {
      .container {
        padding: 2rem;
      }
      
      h2 {
        font-size: 1.5rem;
      }
    }
  </style>
</head>
<body>
  <div class="container">
      <div class="icon">🗑️</div>
      <h2>Item Deleted</h2>
      
      <p class="message">Item dengan ID <strong><?php echo $id; ?></strong> telah berhasil dihapus.</p>
      
      <div class="warning-text">
        <strong>PERINGATAN KEAMANAN:</strong> Operasi ini tidak memiliki pemeriksaan kepemilikan (ownership check). 
        Dalam aplikasi nyata, ini adalah kerentanan berat yang memungkinkan pengguna untuk menghapus item milik pengguna lain.
      </div>
      
      <div class="countdown">
        Anda akan dialihkan kembali ke daftar item dalam <span>3</span> detik...
      </div>
      
      <a href="list.php">Kembali ke Daftar Item</a>
  </div>

  <script>
    // JavaScript sebagai backup jika meta refresh gagal
    setTimeout(function() {
      window.location.href = 'list.php';
    }, 3000); // 3000 milidetik = 3 detik
  </script>
</body>
</html>