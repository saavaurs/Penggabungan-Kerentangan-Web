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
<!doctype html><html><body>
<h2>Create VULN Item</h2>
<form method="post">
  <input name="title" placeholder="title" style="width:300px"><br><br>
  <textarea name="content" placeholder="content" rows=6 cols=60></textarea><br><br>
  <button>Create</button>
</form>
<p><a href="list.php">Back</a></p>
</body></html>
