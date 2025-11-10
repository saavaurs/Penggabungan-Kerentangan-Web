<?php
// safe/create.php
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['user'])) header('Location: ../login.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!check_csrf($_POST['csrf'] ?? '')) { http_response_code(400); exit('CSRF fail'); }
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    if ($title === '') { $err = "Title required"; }
    if (empty($err)) {
        $uuid = uuid4();
        $token = token_generate();
        $hash = token_hash($token);
        $stmt = $pdo->prepare("INSERT INTO items_safe (uuid, token_hash, token_expires_at, user_id, title, content)
                               VALUES (:uuid, :th, NULL, :uid, :t, :c)");
        $stmt->execute([
            ':uuid'=>$uuid, ':th'=>$hash, ':uid'=>$_SESSION['user']['id'],
            ':t'=>$title, ':c'=>$content
        ]);
        echo "<div class='container'>";
        echo "<h2>Item created</h2>";
        echo "<p><b>UUID:</b> ".htmlspecialchars($uuid)."</p>";
        echo "<p><b>ACCESS TOKEN (save this now):</b><br><pre>".htmlspecialchars($token)."</pre></p>";
        echo '<p><a href="list.php">Back</a></p>';
        echo "</div>";
        exit;
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Create SAFE Item</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    :root {
      --primary: #00d4ff; --secondary: #ff00ff; --dark: #0a0e27; --darker: #060818;
      --light: #1a1f3a; --text: #e0e6ed; --danger: #ff4757; --success: #00ff88; --warning: #ffa502;
    }
    body {
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, var(--darker) 0%, var(--dark) 100%);
      color: var(--text);
      min-height: 100vh;
      display: flex; justify-content: center; align-items: center; padding: 20px;
    }
    body::before {
      content: ""; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
      background: radial-gradient(circle at 20% 50%, rgba(0,212,255,0.1) 0%, transparent 50%),
                  radial-gradient(circle at 80% 80%, rgba(255,0,255,0.1) 0%, transparent 50%),
                  radial-gradient(circle at 40% 20%, rgba(0,255,136,0.05) 0%, transparent 50%);
      pointer-events: none; z-index: 1;
    }
    .container {
      position: relative; z-index: 2; width: 100%; max-width: 500px;
      background: rgba(26, 31, 58, 0.8); backdrop-filter: blur(10px);
      padding: 3rem; border-radius: 20px; border: 1px solid rgba(0,212,255,0.2);
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5); text-align: center;
    }
    h2 {
      margin-bottom: 1.5rem;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      -webkit-background-clip: text; -webkit-text-fill-color: transparent;
      font-size: 2.5rem; font-weight: bold;
    }
    p { font-size: 18px; color: var(--text); margin: 15px 0; line-height: 1.6; }
    a {
      display: inline-block; margin-top: 20px; text-decoration: none;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      color: white; padding: 12px 25px; border-radius: 10px; font-weight: bold;
      transition: all 0.3s ease; position: relative; overflow: hidden;
    }
    a::before {
      content: ""; position: absolute; top: 0; left: -100%; width: 100%; height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
      transition: left 0.5s ease;
    }
    a:hover::before { left: 100%; }
    a:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,212,255,0.4); }
    input, textarea, button {
      width: 100%; padding: 10px; margin-top: 10px; border: none;
      border-radius: 8px; font-size: 16px; outline: none;
    }
    button {
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      color: #fff; font-weight: bold; cursor: pointer; margin-top: 15px;
    }
</style>
</head>
<body>
<div class="container">
<h2>Create SAFE Item</h2>
<?php if (!empty($err)) echo "<p style='color:red'>".htmlspecialchars($err)."</p>"; ?>
<form method="post">
  <input name="title" placeholder="Title" value="<?=htmlspecialchars($_POST['title'] ?? '')?>">
  <textarea name="content" rows="6"><?=htmlspecialchars($_POST['content'] ?? '')?></textarea>
  <input type="hidden" name="csrf" value="<?=csrf_token()?>">
  <button>Create</button>
</form>
<p><a href="list.php">Back</a></p>
</div>
</body>
</html>
