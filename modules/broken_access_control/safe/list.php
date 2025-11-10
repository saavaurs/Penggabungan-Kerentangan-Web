<?php
// safe/list.php
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['user'])) header('Location: ../login.php');

$stmt = $pdo->prepare("SELECT id, uuid, title, created_at FROM items_safe WHERE user_id = :u ORDER BY created_at DESC");
$stmt->execute([':u' => $_SESSION['user']['id']]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>SAFE Items</title>
<style>
<?php include __DIR__ . '/../inline_style.php'; ?>
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}
th, td {
  border: 1px solid rgba(0,212,255,0.3);
  padding: 10px;
}
th {
  background: linear-gradient(135deg, var(--primary), var(--secondary));
  color: white;
}
td {
  background-color: rgba(26,31,58,0.7);
}
form { display: inline; }
</style>
</head>
<body>
<div class="container">
<h2>SAFE — Your Items</h2>
<p><a href="create.php">Create</a> | <a href="../index.php">Dashboard</a></p>
<table>
<tr><th>UUID</th><th>Title</th><th>Created</th><th>Action</th></tr>
<?php foreach($rows as $r): ?>
<tr>
  <td><?=htmlspecialchars($r['uuid'])?></td>
  <td><?=htmlspecialchars($r['title'])?></td>
  <td><?=htmlspecialchars($r['created_at'])?></td>
  <td>
    <a href="view.php?u=<?=urlencode($r['uuid'])?>">View</a> |
    <a href="edit.php?u=<?=urlencode($r['uuid'])?>">Edit</a> |
    <form action="delete.php" method="post" onsubmit="return confirm('Delete?')">
      <input type="hidden" name="uuid" value="<?=htmlspecialchars($r['uuid'])?>">
      <input type="hidden" name="csrf" value="<?=csrf_token()?>">
      <button>Delete</button>
    </form>
  </td>
</tr>
<?php endforeach; ?>
</table>
</div>
</body>
</html>
