<?php
// safe/edit.php
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['user'])) header('Location: ../login.php');

$uuid = $_GET['u'] ?? ($_POST['uuid'] ?? '');
if (!$uuid) { http_response_code(400); exit('Missing uuid'); }

$stmt = $pdo->prepare("SELECT * FROM items_safe WHERE uuid = :u LIMIT 1");
$stmt->execute([':u'=>$uuid]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$item) { http_response_code(404); exit('Not found'); }

// Ownership check
if ($item['user_id'] != $_SESSION['user']['id']) {
    http_response_code(403); exit('Forbidden: not owner');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!check_csrf($_POST['csrf'] ?? '')) { http_response_code(400); exit('CSRF fail'); }
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $stmt = $pdo->prepare("UPDATE items_safe SET title = :t, content = :c WHERE uuid = :u");
    $stmt->execute([':t'=>$title, ':c'=>$content, ':u'=>$uuid]);
    header('Location: list.php'); exit;
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Edit SAFE Item</title>
  
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    :root {
      --primary: #00d4ff; /* Biru "Aman" */
      --success: #00ff88; /* Hijau "Sukses" */
      --dark: #0a0e27;
      --darker: #060818;
      --light: #1a1f3a;
      --text: #e0e6ed;
      --danger: #ff4757; 
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

    /* Latar belakang animasi "Aman" */
    body::before {
      content: "";
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: radial-gradient(
          circle at 20% 50%,
          rgba(0, 212, 255, 0.1) 0%, /* --primary */
          transparent 50%
        ),
        radial-gradient(
          circle at 80% 80%,
          rgba(0, 255, 136, 0.1) 0%, /* --success */
          transparent 50%
        );
      pointer-events: none;
      z-index: 1;
    }

    /* Wrapper Konten */
    .container {
      position: relative;
      z-index: 2;
      width: 100%;
      max-width: 600px;
      background: rgba(26, 31, 58, 0.8);
      backdrop-filter: blur(10px);
      padding: 2.5rem;
      border-radius: 20px;
      border: 1px solid rgba(0, 212, 255, 0.3); /* Border --primary */
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

    /* Styling Judul */
    h2 {
      text-align: center;
      margin-bottom: 2rem;
      background: linear-gradient(
        135deg,
        var(--primary),
        var(--success)
      ); /* Gradien Aman */
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      font-size: 2rem;
      font-weight: bold;
      position: relative;
      word-break: break-all; /* Mencegah UUID meluap */
    }

    h2::after {
      content: "";
      position: absolute;
      bottom: -10px;
      left: 50%;
      transform: translateX(-50%);
      width: 100px;
      height: 3px;
      background: linear-gradient(
        90deg,
        var(--primary),
        var(--success)
      ); /* Gradien Aman */
      border-radius: 3px;
    }

    /* Styling Form */
    form {
      display: flex;
      flex-direction: column;
    }

    input[type="text"],
    textarea {
      width: 100%;
      padding: 12px 15px;
      border-radius: 10px;
      border: 1px solid rgba(0, 212, 255, 0.3); /* Border --primary */
      background: rgba(26, 31, 58, 0.6);
      color: var(--text);
      font-size: 16px;
      transition: all 0.3s ease;
      font-family: "Courier New", Courier, monospace;
      margin-bottom: 1rem;
    }

    input[type="text"]:focus,
    textarea:focus {
      outline: none;
      border-color: var(--primary);
      background: rgba(26, 31, 58, 0.8);
      box-shadow: 0 0 15px rgba(0, 212, 255, 0.3); /* Glow --primary */
    }

    textarea {
      resize: vertical;
      min-height: 120px;
    }

    button {
      width: 100%;
      background: linear-gradient(
        135deg,
        var(--primary),
        var(--success)
      ); /* Gradien Aman */
      border: none;
      padding: 14px;
      border-radius: 10px;
      color: var(--darker); /* Teks gelap agar kontras di tombol terang */
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
      background: linear-gradient(
        90deg,
        transparent,
        rgba(255, 255, 255, 0.3),
        transparent
      );
      transition: left 0.5s ease;
    }

    button:hover::before {
      left: 100%;
    }

    button:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 20px rgba(0, 212, 255, 0.4); /* Shadow --primary */
    }

    /* Link "Back" */
    a {
      display: inline-block;
      margin-top: 20px;
      text-align: center;
      width: 100%;
      padding: 10px;
      color: var(--text);
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s ease;
      border-radius: 8px;
      border: 1px solid rgba(0, 212, 255, 0.2);
    }

    a:hover {
      color: var(--primary);
      background: rgba(0, 212, 255, 0.1);
      transform: translateY(-2px);
    }
  </style>
</head>
<body>

  <div class="container">
    <h2>Edit SAFE Item (<?=htmlspecialchars($item['uuid'])?>)</h2>
    <form method="post">
      <input name="title" value="<?=htmlspecialchars($item['title'])?>" placeholder="Title">
      <textarea name="content" placeholder="Content..."><?=htmlspecialchars($item['content'])?></textarea>
      
      <input type="hidden" name="uuid" value="<?=htmlspecialchars($item['uuid'])?>">
      <input type="hidden" name="csrf" value="<?=csrf_token()?>">
      <button>Save</button>
    </form>
    
    <a href="list.php">Back</a>
  </div>
  
</body>
</html>