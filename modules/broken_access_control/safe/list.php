<?php
// safe/list.php
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['user'])) header('Location: ../login.php');

$stmt = $pdo->prepare("SELECT id, uuid, title, created_at FROM items_safe WHERE user_id = :u ORDER BY created_at DESC");
$stmt->execute([':u' => $_SESSION['user']['id']]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html><html><body>
<h2>SAFE — Items (your items)</h2>
<p><a href="create.php">Create</a> | <a href="../index.php">Back to Dashboard</a></p>
<table border=1 cellpadding=6>
<tr><th>UUID</th><th>Title</th><th>Created</th><th>Action</th></tr>
<?php foreach($rows as $r): ?>
<tr>
  <td><?=htmlspecialchars($r['uuid'])?></td>
  <td><?=htmlspecialchars($r['title'])?></td>
  <td><?=htmlspecialchars($r['created_at'])?></td>
  <td>
    <!-- View requires token (user must provide when clicking) -->
    <a href="view.php?u=<?=urlencode($r['uuid'])?>">View</a> |
    <a href="edit.php?u=<?=urlencode($r['uuid'])?>">Edit</a> |
    <form action="delete.php" method="post" style="display:inline" onsubmit="return confirm('Delete?')">
      <input type="hidden" name="uuid" value="<?=htmlspecialchars($r['uuid'])?>">
      <input type="hidden" name="csrf" value="<?=csrf_token()?>">
      <button>Delete</button>
    </form>
  </td>
</tr>
<?php endforeach; ?>
</table>
</body></html>
