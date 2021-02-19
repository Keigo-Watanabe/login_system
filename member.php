<?php
session_start();

// セッション情報がない時はログイン画面にリダイレクトする
if (!isset($_SESSION['email'])) {
  // ログイン後画面にリダイレクトする
  $host = $_SERVER['HTTP_HOST'];
  $url = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
  header("Location: //$host$url/login.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="ja" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>メンバー画面</title>
    <link rel="stylesheet" href="style.css">
  </head>
  <body>

    <h1>メンバー画面</h1>
    <a href="./logout.php">ログアウトする</a>

  </body>
</html>
