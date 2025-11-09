<?php
// safe/edit.php - VERSI AMAN
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../vuln/login.php');
    exit;
}

$dsn = 'mysql:host=127.0.0.1;port=3306;dbname=praktek_bac;charset=utf8mb4';
$dbUser = 'root';
$dbPass = '';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = '';
$errors = [];
$document = null;

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    
    // AMAN: Cek ownership
    $stmt = $pdo->prepare("SELECT * FROM documents WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $_SESSION['user_id']]);
    $document = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$document) {
        $errors[] = '🚫 Access Denied! You can only edit your own documents.';
    }
    
} catch (PDOException $e) {
    $errors[] = 'Database error occurred.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $document) {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        $errors[] = 'Invalid CSRF token.';
    }

    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $is_private = isset($_POST['is_private']) ? 1 : 0;

    if ($title === '') {
        $errors[] = 'Title is required.';
    }
    if ($content === '') {
        $errors[] = 'Content is required.';
    }

    if (empty($errors)) {
        try {
            // AMAN: Update hanya jika milik user yang login
            $stmt = $pdo->prepare("UPDATE documents SET title = ?, content = ?, is_private = ? WHERE id = ? AND user_id = ?");
            $stmt->execute([$title, $content, $is_private, $id, $_SESSION['user_id']]);

            if ($stmt->rowCount() > 0) {
                $message = '✅ Document updated successfully!';
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                
                // Refresh data
                $stmt = $pdo->prepare("SELECT * FROM documents WHERE id = ? AND user_id = ?");
                $stmt->execute([$id, $_SESSION['user_id']]);
                $document = $stmt->fetch(PDO::FETCH_ASSOC);
                
                header("refresh:2;url=view.php?id=$id");
            } else {
                $errors[] = 'Failed to update document.';
            }
        } catch (PDOException $e) {
            $errors[] = 'Database error occurred.';
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Edit Document (Safe)</title>
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
      max-width: 600px;
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
    }

    input, textarea {
      width: 100%;
      padding: 12px 15px;
      border-radius: 10px;
      border: 1px solid rgba(0, 212, 255, 0.2);
      background: rgba(26, 31, 58, 0.6);
      color: var(--text);
      margin-bottom: 20px;
      font-size: 16px;
      transition: all 0.3s ease;
      font-family: inherit;
    }

    textarea {
      min-height: 150px;
      resize: vertical;
    }

    input:focus, textarea:focus {
      outline: none;
      border-color: var(--primary);
      background: rgba(26, 31, 58, 0.8);
      box-shadow: 0 0 15px rgba(0, 212, 255, 0.3);
    }

    .checkbox-group {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 20px;
      padding: 15px;
      background: rgba(26, 31, 58, 0.4);
      border-radius: 10px;
      border: 1px solid rgba(0, 212, 255, 0.1);
    }

    .checkbox-group input[type="checkbox"] {
      width: auto;
      margin: 0;
      cursor: pointer;
    }

    .checkbox-group label {
      margin: 0;
      cursor: pointer;
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
    }

    button:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 20px rgba(0, 212, 255, 0.4);
    }

    .msg {
      background: rgba(0, 255, 136, 0.1);
      border-left: 4px solid var(--success);
      padding: 15px;
      border-radius: 10px;
      margin-bottom: 20px;
      color: var(--success);
      font-weight: 600;
    }

    .errbox {
      background: rgba(255, 71, 87, 0.1);
      border-left: 4px solid var(--danger);
      padding: 15px;
      border-radius: 10px;
      margin-bottom: 20px;
      color: var(--danger);
    }

    .errbox ul {
      margin-left: 20px;
    }
  </style>
</head>
<body>
  <div class="container">
    <a href="list.php" class="back">← Back to List</a>

    <h2>✏️ Edit Document</h2>

    <?php if ($message): ?>
      <div class="msg"><?= $message ?></div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
      <div class="errbox">
        <ul>
          <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <?php if ($document): ?>
      <form method="post" action="">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        
        <label>Document Title *</label>
        <input type="text" name="title" required value="<?= htmlspecialchars($document['title']) ?>">

        <label>Content *</label>
        <textarea name="content" required><?= htmlspecialchars($document['content']) ?></textarea>

        <div class="checkbox-group">
          <input type="checkbox" name="is_private" id="is_private" <?= $document['is_private'] ? 'checked' : '' ?>>
          <label for="is_private">🔒 Make this document private</label>
        </div>

        <button type="submit">Update Document</button>
      </form>
    <?php endif; ?>
  </div>
</body>
</html>