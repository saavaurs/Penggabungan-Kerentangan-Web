<?php
// safe/view.php
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['user'])) header('Location: ../login.php');

$uuid = $_GET['u'] ?? '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uuid = $_POST['u'] ?? '';
    $token = $_POST['token'] ?? '';
} else {
    $token = $_GET['t'] ?? '';
}

if (!$uuid) { http_response_code(400); exit('Missing uuid'); }

$stmt = $pdo->prepare("SELECT * FROM items_safe WHERE uuid = :u LIMIT 1");
$stmt->execute([':u'=>$uuid]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$item) { http_response_code(404); exit('Not found'); }

if ($item['user_id'] != $_SESSION['user']['id']) {
    http_response_code(403); exit('Forbidden: not owner');
}

if (!$token) {
    ?>
    <!doctype html>
    <html>
    <head>
    <meta charset="UTF-8">
    <title>Enter Token</title>
    <style>
    <?php include __DIR__ . '/../inline_style.php'; ?>
    </style>
    </head>
    <body>
    <div class="container">
    <h2>Enter Access Token</h2>
    <form method="post">
      <input type="hidden" name="u" value="<?=htmlspecialchars($uuid)?>">
      <input name="token" placeholder="Paste token here">
      <button>View</button>
    </form>
    <p><a href="list.php">Back</a></p>
    </div>
    </body>
    </html>
    <?php
    exit;
}

$provided_hash = token_hash($token);
if (!hash_equals($item['token_hash'], $provided_hash)) {
    http_response_code(403); exit('Invalid token');
}
?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>View SAFE Item</title>
<style>
<?php include __DIR__ . '/../inline_style.php'; ?>
</style>
</head>
<body>
<div class="container">
<h2><?=htmlspecialchars($item['title'])?></h2>
<p><?=nl2br(htmlspecialchars($item['content']))?></p>
<p><i>UUID: <?=htmlspecialchars($item['uuid'])?></i></p>
<p><a href="list.php">Back</a></p>
</div>
</body>
</html>
