<?php
session_start();

$errormessage = array();

if ($_POST) {
  // POST情報があるとき
  // 1. 入力チェック
  if (!$_POST['email']){
    $errormessage[] = 'メールアドレスを入力してください。';
  } else if (mb_strlen($_POST['email']) > 200) {
    $errormessage[] = 'メールアドレスは200文字以内にしてください。';
  } else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
    $errormessage[] = 'メールアドレスが不正です。';
  }

  if (!$_POST['password']) {
    $errormessage[] = 'パスワードを入力してください。';
  } else if (mb_strlen($_POST['password']) > 100) {
    $errormessage[] = 'パスワードは100文字以内で入力してください。';
  }

  // 2. ログインID、パスワードが一致しているかどうかチェック
  $userfile = '../userinfo.txt';

  if (file_exists($userfile)) {
    $users = file_get_contents($userfile);
    $users = explode("\n", $users);

    foreach ($users as $key => $value){
      $value_array = str_getcsv($value);

      // メールアドレスが一致しているかどうか
      if ($value_array[0] == $_POST['email']) {
        // パスワードが一致しているかどうか
        if (password_verify($_POST['password'], $value_array[1])) {
          // パスワードが一致していた時
          $_SESSION['email'] = $_POST['email'];
          // 3. ログイン後画面にリダイレクトする
          $host = $_SERVER['HTTP_HOST'];
          $url = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
          header("Location: //$host$url/member.php");
          exit;
        }
      }
    }
    $errormessage[] = 'ユーザー名またはパスワードが正しくありません。';

  } else {
    $errormessage[] = 'ユーザーファイルリストが見つかりません。';
  }

} else {
  // POST情報がないとき（GETのとき）

  // セッション情報があるときはログイン後画面にリダイレクトする
  if (isset($_SESSION['email']) && $_SESSION['email']) {
    // ログイン後画面にリダイレクトする
    $host = $_SERVER['HTTP_HOST'];
    $url = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    header("Location: //$host$url/member.php");
    exit;
  }
  
  $_POST = array();
  $_POST['email'] = '';
}
?>
<!DOCTYPE html>
<html lang="ja" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>ログイン機能</title>
    <link rel="stylesheet" href="style.css">
  </head>
  <body>

    <h1>ログイン画面</h1>

    <?php
    if ($errormessage) {
      echo '<div class="errormessage">';
      echo implode('<br>', $errormessage);
      echo '</div>';
    }
    ?>

    <form action="./login.php" method="post">
      <label>メールアドレス</label><br>
      <input type="email" name="email" value="<?php echo htmlspecialchars($_POST['email']); ?>"><br>

      <label>パスワード</label><br>
      <input type="password" name="password" value=""><br>

      <input type="submit" name="login" value="ログイン">
    </form>

  </body>
</html>
