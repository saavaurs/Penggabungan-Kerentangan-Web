<?php
// safe/edit.php
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['user'])) header('Location: ../login.php');

$uuid = $_GET['u'] ?? ($_POST['uuid'] ?? '');
if (!$uuid) { http_response_code(400); exit('Missing uuid'); }

$stmt = $pdo->prepare("SELECT * FROM items_safe WHERE uuid = :u LIMIT 1");
$stmt->execute([':u'=>$uuid]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$item) { http_response_code(404); exit('Not found'); }

if ($item['user_id'] != $_SESSION['user']['id']) {
    http_response_code(403); exit('Forbidden: not owner');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!check_csrf($_POST['csrf'] ?? '')) { http_response_code(400); exit('CSRF fail'); }
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $stmt = $pdo->prepare("UPDATE items_safe SET title = :t, content = :c WHERE uuid = :u");
    $stmt->execute([':t'=>$title, ':c'=>$content, ':u'=>$uuid]);
    header('Location: list.php'); exit;
}
?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Edit SAFE Item</title>
<style>
<?php include __DIR__ . '/../inline_style.php'; ?>
</style>
</head>
<body>
<div class="container">
<h2>Edit SAFE Item</h2>
<form method="post">
  <input name="title" value="<?=htmlspecialchars($item['title'])?>">
  <textarea name="content" rows="6"><?=htmlspecialchars($item['content'])?></textarea>
  <input type="hidden" name="uuid" value="<?=htmlspecialchars($item['uuid'])?>">
  <input type="hidden" name="csrf" value="<?=csrf_token()?>">
  <button>Save</button>
</form>
<p><a href="list.php">Back</a></p>
</div>
</body>
</html>
