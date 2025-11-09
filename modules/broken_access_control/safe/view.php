<?php
// safe/view.php - VERSI AMAN dengan access control
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../vuln/login.php');
    exit;
}

$dsn = 'mysql:host=127.0.0.1;port=3306;dbname=praktek_bac;charset=utf8mb4';
$dbUser = 'root';
$dbPass = '';

$error = '';
$document = null;

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    try {
        $pdo = new PDO($dsn, $dbUser, $dbPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        
        // AMAN: Cek ownership sebelum menampilkan
        $stmt = $pdo->prepare("
            SELECT d.*, u.username, u.full_name 
            FROM documents d 
            JOIN users u ON d.user_id = u.id 
            WHERE d.id = ? AND d.user_id = ?
        ");
        $stmt->execute([$id, $_SESSION['user_id']]);
        $document = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$document) {
            $error = '🚫 Access Denied! You can only view your own documents.';
        }
        
    } catch (PDOException $e) {
        $error = 'Database error occurred.';
    }
} else {
    $error = 'No document ID specified.';
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>View Document (Safe)</title>
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
      max-width: 800px;
      margin: 0 auto;
    }

    .back {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      color: var(--text);
      text-decoration: none;
      margin-bottom: 20px;
      font-weight: 600;
      padding: 10px 16px;
      border-radius: 10px;
      background: rgba(26, 31, 58, 0.6);
      border: 1px solid rgba(0, 212, 255, 0.2);
      transition: all 0.3s ease;
    }

    .back:hover {
      color: var(--primary);
      transform: translateX(-5px);
      border-color: var(--primary);
    }

    .panel {
      background: rgba(26, 31, 58, 0.8);
      backdrop-filter: blur(10px);
      padding: 40px;
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

    .doc-title {
      font-size: 32px;
      font-weight: 700;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 20px;
    }

    .doc-meta {
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
      margin-bottom: 30px;
      padding-bottom: 20px;
      border-bottom: 1px solid rgba(0, 212, 255, 0.2);
      font-size: 14px;
      color: rgba(224, 230, 237, 0.7);
    }

    .doc-meta span {
      display: flex;
      align-items: center;
      gap: 5px;
    }

    .badge {
      padding: 5px 12px;
      border-radius: 15px;
      font-size: 12px;
      font-weight: 600;
    }

    .badge-private {
      background: rgba(255, 71, 87, 0.2);
      color: var(--danger);
      border: 1px solid var(--danger);
    }

    .badge-public {
      background: rgba(0, 255, 136, 0.2);
      color: var(--success);
      border: 1px solid var(--success);
    }

    .doc-content {
      font-size: 16px;
      line-height: 1.8;
      color: var(--text);
      margin-bottom: 30px;
      white-space: pre-wrap;
      word-wrap: break-word;
    }

    .actions {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }

    .btn {
      padding: 10px 20px;
      border-radius: 10px;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s ease;
      border: none;
      cursor: pointer;
      font-size: 14px;
    }

    .btn-primary {
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      color: white;
    }

    .btn-danger {
      background: linear-gradient(135deg, var(--danger), #cc0050);
      color: white;
    }

    .btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
    }

    .error {
      background: rgba(255, 71, 87, 0.1);
      border-left: 4px solid var(--danger);
      padding: 20px;
      border-radius: 10px;
      color: var(--danger);
      font-weight: 600;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="container">
    <a href="list.php" class="back">← Back to List</a>

    <div class="panel">
      <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
      <?php elseif ($document): ?>
        <h1 class="doc-title"><?= htmlspecialchars($document['title']) ?></h1>
        
        <div class="doc-meta">
          <span>👤 By: <strong><?= htmlspecialchars($document['full_name']) ?></strong></span>
          <span>📅 Created: <?= date('d M Y H:i', strtotime($document['created_at'])) ?></span>
          <span>
            <?php if ($document['is_private']): ?>
              <span class="badge badge-private">🔒 Private</span>
            <?php else: ?>
              <span class="badge badge-public">🌐 Public</span>
            <?php endif; ?>
          </span>
        </div>

        <div class="doc-content">
          <?= htmlspecialchars($document['content']) ?>
        </div>

        <div class="actions">
          <a href="edit.php?id=<?= $document['id'] ?>" class="btn btn-primary">✏️ Edit</a>
          <a href="delete.php?id=<?= $document['id'] ?>" class="btn btn-danger" 
             onclick="return confirm('Are you sure you want to delete this document?')">🗑️ Delete</a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>