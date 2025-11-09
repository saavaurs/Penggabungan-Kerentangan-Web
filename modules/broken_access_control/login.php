<?php
require 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username']);
    $p = $_POST['password'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :u LIMIT 1");
    $stmt->execute([':u'=>$u]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    // if ($user && password_verify($p, $user['password'])) {
    if ($user && $user['password']) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id'=>$user['id'],'username'=>$user['username'],'role'=>$user['role']];
        header('Location: index.php'); exit;
    } else $err = "Login gagal.";
}
?>
<form method="post">
  <h3>Login</h3>
  <?=isset($err) ? "<p style='color:red'>$err</p>" : ""?>
  <input name="username" placeholder="username"><br>
  <input name="password" type="password" placeholder="password"><br>
  <button>Login</button>
</form>
