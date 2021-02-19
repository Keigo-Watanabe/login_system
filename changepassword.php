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

$errormessage = array();
$complete = false;

if ($_POST) {
  // POST情報があるとき
  // 1. 入力チェック

  if (!$_POST['password']) {
    $errormessage[] = '現在のパスワードを入力してください。';
  } else if (mb_strlen($_POST['password']) > 100) {
    $errormessage[] = '現在のパスワードは100文字以内で入力してください。';
  }

  if (!$_POST['newpassword']) {
    $errormessage[] = '新しいパスワードを入力してください。';
  } else if (mb_strlen($_POST['newpassword']) > 100) {
    $errormessage[] = '新しいパスワードは100文字以内で入力してください。';
  }

  // 2. ログインID、パスワードが一致しているかどうかチェック
  $userfile = '../userinfo.txt';

  if (file_exists($userfile)) {
    $users = file_get_contents($userfile);
    $users = explode("\n", $users);

    foreach ($users as $key => $value){
      $value_array = str_getcsv($value);

      // メールアドレスが一致しているかどうか
      if ($value_array[0] == $_SESSION['email']) {
        // パスワードが一致しているかどうか
        if (password_verify($_POST['password'], $value_array[1])) {
          // 3.新しいパスワードのハッシュ値を生成
          $password_hash = password_hash($_POST['newpassword'], PASSWORD_DEFAULT);

          // 4.ユーザー情報ファイルを更新
          $line = '"'.$_SESSION['email'].'","'.$password_hash.'"';
          $users[$key] = $line;
          $userinfo = implode("\n", $users);
          $ret = file_put_contents($userfile, $userinfo);
          $complete = true;
          break;
        }
      }
    }
    if (!$complete) {
      $errormessage[] = '現在のパスワードが正しくありません。';
    }

  } else {
    $errormessage[] = 'ユーザーファイルリストが見つかりません。';
  }

} else {
  // POST情報がないとき（GETのとき）
  $_POST = array();
}
?>
<!DOCTYPE html>
<html lang="ja" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>パスワード変更</title>
    <link rel="stylesheet" href="style.css">
  </head>
  <body>

    <h1>パスワード変更</h1>

    <?php
    if ($errormessage) {
      echo '<div class="errormessage">';
      echo implode('<br>', $errormessage);
      echo '</div>';
    }
    ?>

    <?php if ($complete) { ?>
      <p>パスワードを変更しました。</p>
      <a href="./member.php">メンバー画面へ</a>
    <?php } else { ?>
      <form action="./changepassword.php" method="post">
        <label>現在のパスワード</label><br>
        <input type="password" name="password" value=""><br>

        <label>新しいパスワード</label><br>
        <input type="password" name="newpassword" value=""><br>

        <input type="submit" name="chenge" value="変更">
      </form>
    <?php } ?>

  </body>
</html>
