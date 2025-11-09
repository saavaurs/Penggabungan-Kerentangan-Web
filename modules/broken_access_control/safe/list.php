<?php
// safe/list.php - VERSI AMAN dengan access control yang benar
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../vuln/login.php');
    exit;
}

$dsn = 'mysql:host=127.0.0.1;port=3306;dbname=praktek_bac;charset=utf8mb4';
$dbUser = 'root';
$dbPass = '';

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    
    // AMAN: Hanya ambil dokumen milik user yang login
    $stmt = $pdo->prepare("
        SELECT d.*, u.username, u.full_name 
        FROM documents d 
        JOIN users u ON d.user_id = u.id 
        WHERE d.user_id = ?
        ORDER BY d.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $error = 'Database error';
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>My Documents (Safe)</title>
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
      max-width: 1000px;
      margin: 0 auto;
    }

    .header {
      background: rgba(26, 31, 58, 0.8);
      backdrop-filter: blur(10px);
      padding: 20px 30px;
      border-radius: 15px;
      border: 1px solid rgba(0, 212, 255, 0.2);
      margin-bottom: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 15px;
    }

    .header h1 {
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      font-size: 24px;
    }

    .header-info {
      display: flex;
      gap: 15px;
      align-items: center;
    }

    .user-badge {
      padding: 8px 15px;
      background: rgba(0, 255, 136, 0.1);
      border: 1px solid var(--success);
      border-radius: 20px;
      font-size: 14px;
      color: var(--success);
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

    .btn-secondary {
      background: rgba(26, 31, 58, 0.6);
      color: var(--text);
      border: 1px solid rgba(0, 212, 255, 0.2);
    }

    .btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
    }

    .panel {
      background: rgba(26, 31, 58, 0.8);
      backdrop-filter: blur(10px);
      padding: 30px;
      border-radius: 15px;
      border: 1px solid rgba(0, 212, 255, 0.2);
    }

    .doc-card {
      background: rgba(26, 31, 58, 0.6);
      padding: 20px;
      border-radius: 12px;
      border: 1px solid rgba(0, 212, 255, 0.1);
      margin-bottom: 15px;
      transition: all 0.3s ease;
    }

    .doc-card:hover {
      border-color: var(--primary);
      transform: translateX(5px);
    }

    .doc-title {
      font-size: 18px;
      font-weight: 700;
      color: var(--primary);
      margin-bottom: 10px;
    }

    .doc-meta {
      font-size: 13px;
      color: rgba(224, 230, 237, 0.6);
      margin-bottom: 15px;
    }

    .doc-content {
      color: var(--text);
      margin-bottom: 15px;
      line-height: 1.6;
    }

    .doc-actions {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }

    .doc-actions .btn {
      padding: 6px 15px;
      font-size: 13px;
    }

    .empty-state {
      text-align: center;
      padding: 60px 20px;
      color: rgba(224, 230, 237, 0.6);
    }

    .empty-state svg {
      width: 100px;
      height: 100px;
      margin-bottom: 20px;
      opacity: 0.3;
    }

    @media (max-width: 768px) {
      .header {
        flex-direction: column;
        text-align: center;
      }
      
      .header-info {
        flex-direction: column;
        width: 100%;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <div>
        <h1>🛡️ My Documents (Safe Version)</h1>
        <p style="margin-top: 5px; font-size: 14px; color: rgba(224, 230, 237, 0.7);">
          Logged in as: <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>
        </p>
      </div>
      
      <div class="header-info">
        <span class="user-badge">✓ Access Controlled</span>
        <a href="create.php" class="btn btn-primary">+ New Document</a>
        <a href="../vuln/logout.php" class="btn btn-danger">Logout</a>
        <a href="../index.php" class="btn btn-secondary">← Back</a>
      </div>
    </div>

    <div class="panel">
      <?php if (empty($documents)): ?>
        <div class="empty-state">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
          </svg>
          <h3>No documents yet</h3>
          <p>Create your first document to get started!</p>
        </div>
      <?php else: ?>
        <?php foreach ($documents as $doc): ?>
          <div class="doc-card">
            <div class="doc-title"><?= htmlspecialchars($doc['title']) ?></div>
            <div class="doc-meta">
              Created: <?= date('d M Y H:i', strtotime($doc['created_at'])) ?> | 
              Privacy: <?= $doc['is_private'] ? '🔒 Private' : '🌐 Public' ?>
            </div>
            <div class="doc-content">
              <?= nl2br(htmlspecialchars(substr($doc['content'], 0, 200))) ?>
              <?= strlen($doc['content']) > 200 ? '...' : '' ?>
            </div>
            <div class="doc-actions">
              <a href="view.php?id=<?= $doc['id'] ?>" class="btn btn-primary">View</a>
              <a href="edit.php?id=<?= $doc['id'] ?>" class="btn btn-secondary">Edit</a>
              <a href="delete.php?id=<?= $doc['id'] ?>" class="btn btn-danger" 
                 onclick="return confirm('Are you sure?')">Delete</a>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>