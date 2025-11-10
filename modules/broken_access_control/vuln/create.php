<?php
// vuln/create.php
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['user'])) header('Location: ../login.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $uid = (int)$_SESSION['user']['id'];
    // VULNERABLE: string concatenation into SQL (demonstrate SQLi)
    $sql = "INSERT INTO items_vuln (user_id, title, content) VALUES ($uid, '{$title}', '{$content}')";
    $pdo->exec($sql);
    header('Location: list.php'); exit;
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Create VULN Item</title>
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
      max-width: 600px;
      background: rgba(26, 31, 58, 0.8);
      backdrop-filter: blur(10px);
      padding: 2.5rem;
      border-radius: 20px;
      border: 1px solid rgba(255, 71, 87, 0.3);
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
      background: linear-gradient(135deg, var(--danger), var(--warning));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      font-size: 2rem;
      font-weight: bold;
      position: relative;
    }

    h2::after {
      content: "";
      position: absolute;
      bottom: -10px;
      left: 50%;
      transform: translateX(-50%);
      width: 100px;
      height: 3px;
      background: linear-gradient(90deg, var(--danger), var(--warning));
      border-radius: 3px;
    }

    /* Warning Badge */
    .vuln-badge {
      text-align: center;
      margin-bottom: 2rem;
    }

    .vuln-badge span {
      display: inline-block;
      background: rgba(255, 71, 87, 0.2);
      color: var(--danger);
      padding: 8px 15px;
      border-radius: 999px;
      border: 1px solid var(--danger);
      font-weight: 700;
      font-size: 0.8rem;
      animation: blink 2s infinite;
    }

    @keyframes blink {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.7; }
    }

    /* Form Elements */
    form {
      display: flex;
      flex-direction: column;
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    label {
      display: block;
      font-weight: 600;
      margin-bottom: 8px;
      color: var(--text);
      font-size: 0.9rem;
    }

    input[type="text"],
    textarea {
      width: 100%;
      padding: 12px 15px;
      border-radius: 10px;
      border: 1px solid rgba(255, 71, 87, 0.3);
      background: rgba(26, 31, 58, 0.6);
      color: var(--text);
      font-size: 16px;
      transition: all 0.3s ease;
      font-family: 'Courier New', Courier, monospace; /* Font monospace untuk menyoroti input kode */
    }

    input[type="text"]:focus,
    textarea:focus {
      outline: none;
      border-color: var(--danger);
      background: rgba(26, 31, 58, 0.8);
      box-shadow: 0 0 15px rgba(255, 71, 87, 0.3);
    }

    textarea {
      resize: vertical;
      min-height: 120px;
    }

    button {
      width: 100%;
      background: linear-gradient(135deg, var(--danger), var(--warning));
      border: none;
      padding: 14px;
      border-radius: 10px;
      color: white;
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
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      transition: left 0.5s ease;
    }

    button:hover::before {
      left: 100%;
    }

    button:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 20px rgba(255, 71, 87, 0.4);
    }

    /* Code Preview */
    .code-preview {
      margin-top: 1.5rem;
      padding: 15px;
      background: rgba(26, 31, 58, 0.4);
      border-radius: 10px;
      border-left: 3px solid var(--danger);
      font-family: 'Courier New', Courier, monospace;
      font-size: 0.85rem;
      color: rgba(224, 230, 237, 0.8);
      line-height: 1.5;
    }

    .code-preview strong {
      color: var(--warning);
    }

    /* Back Link */
    .back-link {
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
      border: 1px solid rgba(255, 71, 87, 0.2);
    }

    .back-link:hover {
      color: var(--danger);
      background: rgba(255, 71, 87, 0.1);
      transform: translateY(-2px);
    }

    /* Responsive */
    @media (max-width: 600px) {
      .container {
        padding: 1.5rem;
      }
      
      h2 {
        font-size: 1.5rem;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>⚠️ Create VULN Item</h2>

    <div class="vuln-badge">
      <span>DEMO: INTENTIONALLY VULNERABLE</span>
    </div>

    <form method="post">
      <div class="form-group">
        <label for="title">Title</label>
        <input type="text" id="title" name="title" placeholder="Masukkan title item" required>
      </div>
      
      <div class="form-group">
        <label for="content">Content</label>
        <textarea id="content" name="content" placeholder="Masukkan konten (bisa berupa script)" required></textarea>
      </div>
      
      <button type="submit">Create Item</button>
    </form>

    <div class="code-preview">
      <strong>Query yang akan dieksekusi:</strong><br>
      INSERT INTO items_vuln (user_id, title, content) VALUES (<?php echo (int)$_SESSION['user']['id']; ?>, '<strong>{$_POST['title']}</strong>', '<strong>{$_POST['content']}</strong>')
    </div>

    <a href="list.php" class="back-link">← Kembali ke Daftar Item</a>
  </div>

  <script>
    // Update code preview dynamically as user types
    document.getElementById('title').addEventListener('input', updateCodePreview);
    document.getElementById('content').addEventListener('input', updateCodePreview);

    function updateCodePreview() {
      const title = document.getElementById('title').value || '...';
      const content = document.getElementById('content').value || '...';
      const uid = <?php echo (int)$_SESSION['user']['id']; ?>;
      
      // This is just for display, DO NOT use this in production!
      const query = `INSERT INTO items_vuln (user_id, title, content) VALUES (${uid}, '${title}', '${content}')`;
      
      // Find the code-preview element and update its content
      // Note: In a real app, you would not display this to the user
      const previewElement = document.querySelector('.code-preview');
      if (previewElement) {
        // We'll just update the strong part to avoid issues
        const strongElement = previewElement.querySelector('strong');
        if(strongElement) {
            strongElement.innerHTML = query;
        }
      }
    }
  </script>
</body>
</html>