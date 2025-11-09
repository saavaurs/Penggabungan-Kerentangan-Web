<?php
// vuln/list.php
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['user'])) header('Location: ../login.php');

$res = $pdo->query("SELECT items_vuln.*, users.username FROM items_vuln JOIN users ON items_vuln.user_id = users.id ORDER BY items_vuln.id DESC");
?>
<!doctype html><html><body>
<h2>VULN — Items</h2>
<p><a href="create.php">Create</a> | <a href="../index.php">Back to Dashboard</a></p>
<table border=1 cellpadding=6>
<tr><th>ID</th><th>Title</th><th>Content</th><th>Author</th><th>Action</th></tr>
<?php foreach($res as $r): ?>
<tr>
  <td><?= $r['id'] ?></td>
  <td><?= $r['title'] ?></td>
  <!-- intentionally not escaped (stored XSS demonstration) -->
  <td><?= $r['content'] ?></td>
  <td><?= $r['username'] ?></td>
  <td>
    <a href="edit.php?id=<?= $r['id'] ?>">Edit</a> |
    <a href="delete.php?id=<?= $r['id'] ?>" onclick="return confirm('Delete?')">Delete</a>
  </td>
</tr>
<?php endforeach; ?>
</table>
</body></html>
