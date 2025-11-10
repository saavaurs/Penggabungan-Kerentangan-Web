<?php
// safe/list.php
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['user'])) header('Location: ../login.php');

$stmt = $pdo->prepare("SELECT id, uuid, title, created_at FROM items_safe WHERE user_id = :u ORDER BY created_at DESC");
$stmt->execute([':u' => $_SESSION['user']['id']]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>SAFE — Items</title>

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
      --warning: #ffa502; /* Kuning untuk "Edit" */
    }

    body {
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, var(--darker) 0%, var(--dark) 100%);
      color: var(--text);
      min-height: 100vh;
      overflow-x: hidden;
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

    /* Header (Judul & Navigasi) */
    .header {
      text-align: center;
      margin-bottom: 2rem;
      padding: 2rem 1.5rem 1.5rem;
      animation: slideDown 0.5s ease;
      position: relative;
      z-index: 2;
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
      background: linear-gradient(135deg, var(--primary), var(--success));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 1.5rem;
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
      border: 1px solid rgba(0, 212, 255, 0.3);
    }

    .header-actions a:hover {
      background: rgba(0, 212, 255, 0.1);
      transform: translateY(-2px);
    }

    .header-actions .create-link {
      background: linear-gradient(135deg, var(--primary), var(--success));
      color: var(--darker);
      border-color: var(--primary);
    }

    /* Konten Utama (Wrapper Tabel) */
    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px 40px;
      position: relative;
      z-index: 2;
    }

    /* Styling Tabel */
    .safe-table {
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
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .safe-table th,
    .safe-table td {
      padding: 15px;
      text-align: left;
      border-bottom: 1px solid rgba(0, 212, 255, 0.1); /* Garis batas --primary */
    }

    .safe-table th {
      background: rgba(0, 212, 255, 0.1); /* Latar belakang header --primary */
      font-weight: 700;
      color: var(--primary);
      text-transform: uppercase;
      letter-spacing: 1px;
      font-size: 0.8rem;
    }

    .safe-table tr:last-child td {
      border-bottom: none;
    }

    .safe-table tr:hover {
      background: rgba(0, 212, 255, 0.05); /* Efek hover --primary */
    }

    .safe-table td {
      color: var(--text);
      vertical-align: middle;
      line-height: 1.6;
      /* Memastikan UUID tidak merusak layout */
      word-break: break-all; 
    }

    /* Styling Kolom Aksi */
    .action-links {
      display: flex;
      gap: 10px;
      align-items: center;
      flex-wrap: wrap; /* Untuk layar kecil */
    }

    .action-links a,
    .action-links button {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: 6px 12px;
      border-radius: 6px;
      font-size: 0.8rem;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.2s ease;
      cursor: pointer;
    }
    
    .action-links .view-link {
      color: var(--primary);
      border: 1px solid var(--primary);
    }
    .action-links .view-link:hover {
      background: rgba(0, 212, 255, 0.1);
    }
    
    .action-links .edit-link {
      color: var(--warning);
      border: 1px solid var(--warning);
    }
    .action-links .edit-link:hover {
      background: rgba(255, 165, 2, 0.1);
    }

    .action-links form {
      display: inline;
      margin: 0;
      padding: 0;
    }
    
    .action-links button {
      color: var(--danger);
      border: 1px solid var(--danger);
      background: transparent;
      font-family: inherit; /* Pastikan font sama */
    }
    .action-links button:hover {
      background: rgba(255, 71, 87, 0.1);
    }

    /* Responsive */
    @media (max-width: 768px) {
      .safe-table { font-size: 0.9rem; }
      .safe-table th,
      .safe-table td { padding: 10px; }
      .action-links { flex-direction: column; align-items: flex-start; }
    }
  </style>
</head>
<body>

  <div class="header">
    <h2>SAFE — Items</h2>
    <div class="header-actions">
      <a href="create.php" class="create-link">+ Create New Item</a>
      <a href="../index.php">← Back to Dashboard</a>
    </div>
  </div>

  <div class="container">
    <table class="safe-table"> <thead> <tr>
          <th>UUID</th>
          <th>Title</th>
          <th>Created</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach($rows as $r): ?>
        <tr>
          <td><?=htmlspecialchars($r['uuid'])?></td>
          <td><?=htmlspecialchars($r['title'])?></td>
          <td><?=htmlspecialchars($r['created_at'])?></td>
          
          <td class="action-links">
            <a href="view.php?u=<?=urlencode($r['uuid'])?>" class="view-link">View</a>
            <a href="edit.php?u=<?=urlencode($r['uuid'])?>" class="edit-link">Edit</a>
            
            <form action="delete.php" method="post" onsubmit="return confirm('Delete?')">
              <input type="hidden" name="uuid" value="<?=htmlspecialchars($r['uuid'])?>">
              <input type="hidden" name="csrf" value="<?=csrf_token()?>">
              <button type="submit">Delete</button> </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  
</body>
</html>