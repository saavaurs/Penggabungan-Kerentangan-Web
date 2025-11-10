<?php
// vuln/list.php
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['user'])) header('Location: ../login.php');

// VULNERABLE: Fetch all items and display content without escaping (Stored XSS demo)
 $res = $pdo->query("SELECT items_vuln.*, users.username FROM items_vuln JOIN users ON items_vuln.user_id = users.id ORDER BY items_vuln.id DESC");
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>VULN — Items</title>
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

    /* Header */
    .header {
      text-align: center;
      margin-bottom: 2rem;
      padding: 1.5rem 0;
      animation: slideDown 0.5s ease;
    }

    @keyframes slideDown {
      from {
        opacity: 0;
        transform: translateY(-20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    h2 {
      font-size: 2.5rem;
      font-weight: 700;
      background: linear-gradient(135deg, var(--danger), var(--warning));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 0.5rem;
    }

    .header-actions {
      display: flex;
      justify-content: center;
      gap: 20px;
      flex-wrap: wrap;
    }

    .header-actions a {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 10px 20px;
      border-radius: 10px;
      color: var(--text);
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s ease;
      border: 1px solid rgba(224, 230, 237, 0.3);
    }

    .header-actions a:hover {
      background: rgba(255, 71, 87, 0.1);
      transform: translateY(-2px);
    }

    .header-actions .create-link {
      background: linear-gradient(135deg, var(--danger), var(--warning));
      color: white;
      border-color: var(--danger);
    }

    /* Main Content */
    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px 40px;
    }

    /* Table Styling */
    .vuln-table {
      width: 100%;
      border-collapse: collapse;
      background: rgba(26, 31, 58, 0.6);
      backdrop-filter: blur(10px);
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
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

    .vuln-table th,
    .vuln-table td {
      padding: 15px;
      text-align: left;
      border-bottom: 1px solid rgba(255, 71, 87, 0.1);
    }

    .vuln-table th {
      background: rgba(255, 71, 87, 0.1);
      font-weight: 700;
      color: var(--danger);
      text-transform: uppercase;
      letter-spacing: 1px;
      font-size: 0.8rem;
    }

    .vuln-table tr:last-child td {
      border-bottom: none;
    }

    .vuln-table tr:hover {
      background: rgba(255, 71, 87, 0.05);
    }

    .vuln-table td {
      color: var(--text);
      vertical-align: top;
      line-height: 1.6;
    }

    /* Content Column (Vulnerable to XSS) */
    .vuln-table .content-cell {
      max-width: 300px; /* Limit width */
      word-wrap: break-word;
    }

    /* Action Links */
    .action-links {
      display: flex;
      gap: 10px;
      align-items: center;
    }

    .action-links a {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: 6px 12px;
      border-radius: 6px;
      font-size: 0.8rem;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.2s ease;
    }

    .action-links .edit-link {
      color: var(--warning);
      border: 1px solid var(--warning);
    }

    .action-links .edit-link:hover {
      background: rgba(255, 165, 2, 0.1);
    }

    .action-links .delete-link {
      color: var(--danger);
      border: 1px solid var(--danger);
    }

    .action-links .delete-link:hover {
      background: rgba(255, 71, 87, 0.1);
    }

    /* Footer */
    .footer {
      margin-top: 2rem;
      text-align: center;
      padding: 1.5rem;
      background: rgba(255, 71, 87, 0.05);
      border-radius: 10px;
      border: 1px solid rgba(255, 71, 87, 0.2);
    }

    .footer p {
      font-size: 0.9rem;
      color: rgba(224, 230, 237, 0.7);
      line-height: 1.5;
    }

    .footer p strong {
      color: var(--danger);
    }

    /* Responsive */
    @media (max-width: 768px) {
      .header-actions {
        flex-direction: column;
        align-items: stretch;
      }

      .vuln-table {
        font-size: 0.9rem;
      }

      .vuln-table th,
      .vuln-table td {
        padding: 10px;
      }

      .action-links {
        flex-direction: column;
        gap: 5px;
      }
    }
  </style>
</head>
<body>
  <div class="header">
    <h2>⚠️ VULN — Items</h2>
    <div class="header-actions">
      <a href="create.php" class="create-link">+ Create New Item</a>
      <a href="../index.php">← Back to Dashboard</a>
    </div>
  </div>

  <div class="container">
    <table class="vuln-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Title</th>
          <th>Content</th>
          <th>Author</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($res as $r): ?>
        <tr>
          <td><?= $r['id'] ?></td>
          <td><?= htmlspecialchars($r['title']) ?></td>
          <!-- intentionally not escaped (stored XSS demonstration) -->
          <td class="content-cell"><?= $r['content'] ?></td>
          <td><?= htmlspecialchars($r['username']) ?></td>
          <td class="action-links">
            <a href="edit.php?id=<?= $r['id'] ?>" class="edit-link">Edit</a>
            <a href="delete.php?id=<?= $r['id'] ?>" class="delete-link" onclick="return confirm('Delete this item?'); return false;">Delete</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="footer">
    <p><strong>⚠️ Peringatan Keamanan:</strong> Halaman ini sengaja dibuat rentan untuk demonstrasi.</p>
    <p>• Konten <strong>tidak di-escape</strong>, sehingga rentan terhadap <strong>Stored XSS</strong>.</p>
    <p>• Operasi hapus tidak memiliki pemeriksaan kepemilikan (ownership check).</p>
  </div>

  <script>
    // Optional: Add some interactivity, like highlighting rows on hover
    document.querySelectorAll('.vuln-table tbody tr').forEach(row => {
      row.addEventListener('mouseenter', () => {
        row.style.backgroundColor = 'rgba(255, 71, 87, 0.05)';
      });
      row.addEventListener('mouseleave', () => {
        row.style.backgroundColor = 'transparent';
      });
    });
  </script>
</body>
</html>