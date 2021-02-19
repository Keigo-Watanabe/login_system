<?php
session_start();

$_SESSION = array();
?>
<!DOCTYPE html>
<html lang="ja" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>ログアウト画面</title>
    <link rel="stylesheet" href="style.css">
  </head>
  <body>

    <h1>ログアウトしました。</h1>
    <a href="./login.php">ログインする</a>

  </body>
</html>
