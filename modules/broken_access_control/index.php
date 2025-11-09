<?php
// vuln/index.php - VERSI RENTAN (VULNERABLE)
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$message = '';
$errors = [];

// Handle CREATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $is_private = isset($_POST['is_private']) ? 1 : 0;

    if ($title && $content) {
        try {
            $stmt = $pdo->prepare("INSERT INTO documents (user_id, title, content, is_private) VALUES (?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $title, $content, $is_private]);
            $message = '✅ Document created successfully!';
        } catch (PDOException $e) {
            $errors[] = 'Failed to create document.';
        }
    } else {
        $errors[] = 'Title and content are required.';
    }
}

// Handle UPDATE - VULNERABLE: Tidak cek ownership!
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = (int)$_POST['id'];
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $is_private = isset($_POST['is_private']) ? 1 : 0;

    if ($title && $content) {
        try {
            // VULNERABLE: Tidak ada WHERE user_id = ?
            $stmt = $pdo->prepare("UPDATE documents SET title = ?, content = ?, is_private = ? WHERE id = ?");
            $stmt->execute([$title, $content, $is_private, $id]);
            $message = '✅ Document updated!';
        } catch (PDOException $e) {
            $errors[] = 'Failed to update document.';
        }
    }
}

// Handle DELETE - VULNERABLE: Tidak cek ownership!
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        // VULNERABLE: Tidak ada WHERE user_id = ?
        $stmt = $pdo->prepare("DELETE FROM documents WHERE id = ?");
        $stmt->execute([$id]);
        $message = '✅ Document deleted!';
    } catch (PDOException $e) {
        $errors[] = 'Failed to delete document.';
    }
}

// Fetch document for editing - VULNERABLE: Bisa lihat dokumen siapa saja!
$editDoc = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    try {
        // VULNERABLE: Tidak ada WHERE user_id = ?
        $stmt = $pdo->prepare("SELECT * FROM documents WHERE id = ?");
        $stmt->execute([$id]);
        $editDoc = $stmt->fetch();
    } catch (PDOException $e) {
        $errors[] = 'Failed to fetch document.';
    }
}

// Fetch ALL documents - VULNERABLE: Menampilkan SEMUA dokumen, bukan hanya milik user
try {
    $stmt = $pdo->query("
        SELECT d.*, u.username, u.full_name 
        FROM documents d 
        JOIN users u ON d.user_id = u.id 
        ORDER BY d.created_at DESC
    ");
    $documents = $stmt->fetchAll();
} catch (PDOException $e) {
    $errors[] = 'Database error.';
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Document Management (VULNERABLE)</title>
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
      background: radial-gradient(circle at 20% 50%, rgba(255, 71, 87, 0.1) 0%, transparent 50%);
      pointer-events: none;
      z-index: 1;
    }

    .container {
      position: relative;
      z-index: 2;
      max-width: 1200px;
      margin: 0 auto;
    }

    .header {
      background: rgba(26, 31, 58, 0.8);
      backdrop-filter: blur(10px);
      padding: 20px 30px;
      border-radius: 15px;
      border: 1px solid rgba(255, 71, 87, 0.3);
      margin-bottom: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 15px;
    }

    .header h1 {
      background: linear-gradient(135deg, var(--danger), var(--warning));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      font-size: 24px;
    }

    .warning-badge {
      padding: 8px 15px;
      background: rgba(255, 71, 87, 0.2);
      border: 1px solid var(--danger);
      border-radius: 20px;
      font-size: 14px;
      color: var(--danger);
      font-weight: 700;
      animation: blink 2s infinite;
    }

    @keyframes blink {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.7; }
    }

    .header-info {
      display: flex;
      gap: 15px;
      align-items: center;
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

    .btn-warning {
      background: linear-gradient(135deg, var(--warning), #ff6b00);
      color: white;
    }

    .btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
    }

    .grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      margin-bottom: 20px;
    }

    .panel {
      background: rgba(26, 31, 58, 0.8);
      backdrop-filter: blur(10px);
      padding: 30px;
      border-radius: 15px;
      border: 1px solid rgba(255, 71, 87, 0.2);
    }

    .panel h2 {
      margin-bottom: 20px;
      color: var(--primary);
      font-size: 20px;
    }

    label {
      display: block;
      font-weight: 600;
      margin-bottom: 8px;
      margin-top: 10px;
      color: var(--text);
      font-size: 0.9rem;
    }

    input, textarea {
      width: 100%;
      padding: 10px;
      border-radius: 8px;
      border: 1px solid rgba(255, 71, 87, 0.3);
      background: rgba(26, 31, 58, 0.6);
      color: var(--text);
      font-size: 14px;
      font-family: inherit;
    }

    textarea {
      min-height: 100px;
      resize: vertical;
    }

    input:focus, textarea:focus {
      outline: none;
      border-color: var(--danger);
      box-shadow: 0 0 10px rgba(255, 71, 87, 0.3);
    }

    .checkbox-group {
      display: flex;
      align-items: center;
      gap: 10px;
      margin: 15px 0;
    }

    .checkbox-group input[type="checkbox"] {
      width: auto;
      cursor: pointer;
    }

    .checkbox-group label {
      margin: 0;
      cursor: pointer;
    }

    button[type="submit"] {
      width: 100%;
      margin-top: 15px;
    }

    .doc-card {
      background: rgba(26, 31, 58, 0.6);
      padding: 20px;
      border-radius: 12px;
      border: 1px solid rgba(255, 71, 87, 0.2);
      margin-bottom: 15px;
      transition: all 0.3s ease;
    }

    .doc-card:hover {
      border-color: var(--danger);
      transform: translateX(5px);
    }

    .doc-card.owned {
      border-color: rgba(0, 255, 136, 0.3);
    }

    .doc-card.owned:hover {
      border-color: var(--success);
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

    .owner-badge {
      display: inline-block;
      padding: 4px 10px;
      border-radius: 12px;
      font-size: 11px;
      font-weight: 600;
      margin-left: 10px;
    }

    .owner-badge.yours {
      background: rgba(0, 255, 136, 0.2);
      color: var(--success);
      border: 1px solid var(--success);
    }

    .owner-badge.others {
      background: rgba(255, 71, 87, 0.2);
      color: var(--danger);
      border: 1px solid var(--danger);
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

    .vuln-notice {
      background: rgba(255, 165, 2, 0.1);
      border: 2px solid var(--warning);
      padding: 20px;
      border-radius: 12px;
      margin-bottom: 20px;
      color: var(--warning);
    }

    .vuln-notice h3 {
      margin-bottom: 10px;
      font-size: 18px;
    }

    .vuln-notice ul {
      margin-left: 20px;
      margin-top: 10px;
    }

    @media (max-width: 1024px) {
      .grid {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 768px) {
      .header {
        flex-direction: column;
        text-align: center;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <div>
        <h1>⚠️ Document Management (VULNERABLE)</h1>
        <p style="margin-top: 5px; font-size: 14px; color: rgba(224, 230, 237, 0.7);">
          Logged in as: <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>
        </p>
      </div>
      
      <div class="header-info">
        <span class="warning-badge">⚠️ NO ACCESS CONTROL</span>
        <a href="../safe/list.php" class="btn btn-primary">Switch to Safe Version</a>
        <a href="logout.php" class="btn btn-danger">Logout</a>
        <a href="../index.php" class="btn btn-secondary">← Back</a>
      </div>
    </div>

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

    <div class="vuln-notice">
      <h3>🔴 Vulnerability Demo</h3>
      <p><strong>This version is intentionally vulnerable!</strong> Issues include:</p>
      <ul>
        <li>❌ No ownership verification - You can see <strong>ALL</strong> documents from all users</li>
        <li>❌ You can <strong>EDIT</strong> documents that don't belong to you</li>
        <li>❌ You can <strong>DELETE</strong> documents created by other users</li>
        <li>❌ No CSRF protection</li>
        <li>❌ IDOR (Insecure Direct Object Reference) vulnerability</li>
      </ul>
      <p style="margin-top: 10px;"><strong>Try it:</strong> Edit or delete a document owned by another user!</p>
    </div>

    <div class="grid">
      <div class="panel">
        <h2><?= $editDoc ? '✏️ Edit Document' : '➕ Create New Document' ?></h2>
        <form method="post" action="">
          <input type="hidden" name="action" value="<?= $editDoc ? 'update' : 'create' ?>">
          <?php if ($editDoc): ?>
            <input type="hidden" name="id" value="<?= $editDoc['id'] ?>">
          <?php endif; ?>

          <label>Title *</label>
          <input type="text" name="title" required value="<?= $editDoc ? htmlspecialchars($editDoc['title']) : '' ?>">

          <label>Content *</label>
          <textarea name="content" required><?= $editDoc ? htmlspecialchars($editDoc['content']) : '' ?></textarea>

          <div class="checkbox-group">
            <input type="checkbox" name="is_private" id="is_private" <?= ($editDoc && $editDoc['is_private']) || !$editDoc ? 'checked' : '' ?>>
            <label for="is_private">🔒 Private document</label>
          </div>

          <button type="submit" class="btn <?= $editDoc ? 'btn-warning' : 'btn-primary' ?>">
            <?= $editDoc ? 'Update Document' : 'Create Document' ?>
          </button>
          
          <?php if ($editDoc): ?>
            <a href="index.php" class="btn btn-secondary" style="display: block; text-align: center; margin-top: 10px;">Cancel Edit</a>
          <?php endif; ?>
        </form>
      </div>

      <div class="panel">
        <h2>📂 All Documents (Everyone's!)</h2>
        <?php if (empty($documents)): ?>
          <p style="text-align: center; color: rgba(224, 230, 237, 0.6);">No documents yet.</p>
        <?php else: ?>
          <?php foreach ($documents as $doc): ?>
            <?php $isOwned = $doc['user_id'] == $_SESSION['user_id']; ?>
            <div class="doc-card <?= $isOwned ? 'owned' : '' ?>">
              <div class="doc-title">
                <?= htmlspecialchars($doc['title']) ?>
                <span class="owner-badge <?= $isOwned ? 'yours' : 'others' ?>">
                  <?= $isOwned ? 'YOUR DOC' : 'By ' . htmlspecialchars($doc['username']) ?>
                </span>
              </div>
              <div class="doc-meta">
                👤 Owner: <strong><?= htmlspecialchars($doc['full_name']) ?></strong> | 
                📅 <?= date('d M Y', strtotime($doc['created_at'])) ?> | 
                <?= $doc['is_private'] ? '🔒 Private' : '🌐 Public' ?>
              </div>
              <div class="doc-content">
                <?= nl2br(htmlspecialchars(substr($doc['content'], 0, 100))) ?>
                <?= strlen($doc['content']) > 100 ? '...' : '' ?>
              </div>
              <div class="doc-actions">
                <a href="?edit=<?= $doc['id'] ?>" class="btn btn-warning">✏️ Edit</a>
                <a href="?delete=<?= $doc['id'] ?>" class="btn btn-danger" 
                   onclick="return confirm('Delete this document?')">🗑️ Delete</a>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</body>
</html>