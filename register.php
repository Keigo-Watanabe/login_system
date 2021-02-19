<?php
$errormessage = array();

if ($_POST) {
  // POST情報があるとき

  // 1. 入力チェック
  // メールアドレスチェック
  if (!$_POST['email']) {
    $errormessage[] = 'メールアドレスを入力してください。';
  } else if (strlen($_POST['email']) > 200) {
    $errormessage[] = 'メールアドレスは200文字以内で入力してください。';
  } else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $errormessage[] = 'メールアドレスが不正です。';
  }

  // パスワードチェック
  if (!$_POST['password']) {
    $errormessage[] = 'パスワードを入力してください。';
  } else if (strlen($_POST['email']) > 100) {
    $errormessage[] = 'パスワードは100文字以内で入力してください。';
  }

  if ($_POST['password'] != $_POST['password-2']) {
    $errormessage[] = '確認用パスワードが一致しません。';
  }

  $userfile = '../userinfo.txt';
  $users = array();

  if (file_exists($userfile)) {
    $users = file_get_contents($userfile);
    $users = explode('\n', $users);

    foreach ($users as $key => $value) {
      $value_array = str_getcsv($value);

      if ($value_array[0] == $_POST['email']) {
        $errormessage[] = 'そのメールアドレスはすでに登録されています。';
        break;
      }
    }
  }

  // 2. 新規ユーザー登録処理
  if (!$errormessage) {
    $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $line = '"'.$_POST['email'].'","'.$password_hash.'"'."\n";
    $ret = file_put_contents($userfile, $line, FILE_APPEND);
  }

  // 3. ログイン後画面にリダイレクトする
  if (!$errormessage) {
    $host = $_SERVER['HTTP_HOST'];
    $url = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    header("Location: //$host$url/login.php");
    exit;
  }

} else {
  // POST情報がないとき（GETのとき）
  $_POST['email'] = '';
}
?>
<!DOCTYPE html>
<html lang="ja" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>新規登録画面</title>
    <link rel="stylesheet" href="style.css">
  </head>
  <body>

    <h1>ユーザー新規登録</h1>

    <?php
    if ($errormessage) {
      echo '<div class="errormessage">';
      echo implode('<br>', $errormessage);
      echo '</div>';
    }
    ?>

    <form action="./register.php" method="post">
      <label>メールアドレス</label><br>
      <input type="email" name="email" value="<?php echo htmlspecialchars($_POST['email']); ?>"><br>

      <label>パスワード</label><br>
      <input type="password" name="password" value=""><br>

      <label>パスワード（確認）</label><br>
      <input type="password" name="password-2" value=""><br>

      <input type="submit" name="register" value="登録">
    </form>

  </body>
</html>
