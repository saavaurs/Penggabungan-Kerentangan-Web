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
        // Show token only once
        echo "<h3>Item created</h3>";
        echo "<p><b>UUID:</b> ".htmlspecialchars($uuid)."</p>";
        echo "<p><b>ACCESS TOKEN (save this now):</b><br><pre>".htmlspecialchars($token)."</pre></p>";
        echo '<p><a href="list.php">Back</a></p>';
        exit;
    }
}
?>
<!doctype html><html><body>
<h2>Create SAFE Item</h2>
<?php if (!empty($err)) echo "<p style='color:red'>".htmlspecialchars($err)."</p>"; ?>
<form method="post">
  <input name="title" placeholder="title" style="width:300px" value="<?=htmlspecialchars($_POST['title'] ?? '')?>"><br><br>
  <textarea name="content" rows=6 cols=60><?=htmlspecialchars($_POST['content'] ?? '')?></textarea><br><br>
  <input type="hidden" name="csrf" value="<?=csrf_token()?>">
  <button>Create</button>
</form>
<p><a href="list.php">Back</a></p>
</body></html>
