<?php
$errormessage = array();
$complete = false;

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

  // 2. ログインID、パスワードが一致しているかどうかチェック
  $userfile = '../userinfo.txt';

  if (file_exists($userfile)) {
    $users = file_get_contents($userfile);
    $users = explode("\n", $users);

    foreach ($users as $key => $value){
      $value_array = str_getcsv($value);

      // メールアドレスが一致しているかどうか
      if ($value_array[0] == $_POST['email']) {
        // パスワードを生成
        $pass = bin2hex(random_bytes(5));

        // メール送信
        $message = "パスワードを変更しました。\r\n"
                   .$pass."\r\n";
        mail($_POST['email'], 'パスワードを変更しました', $message);

        // userinfo.txt ファイル更新
        $password_hash = password_hash($pass, PASSWORD_DEFAULT);
        $line = '"'.$_POST['email'].'","'.$password_hash.'"';
        $users[$key] = $line;
        $userinfo = implode("\n", $users);
        $ret = file_put_contents($userfile, $userinfo);
        $complete = true;
        break;
      }
    }
    if (!$complete) {
      $errormessage[] = 'ユーザー名が正しくありません。';
    }

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
    <title>パスワード再発行画面</title>
    <link rel="stylesheet" href="style.css">
  </head>
  <body>

    <h1>パスワード再発行画面</h1>

    <?php
    if ($errormessage) {
      echo '<div class="errormessage">';
      echo implode('<br>', $errormessage);
      echo '</div>';
    }
    ?>

    <?php if ($complete) { ?>
      <p>パスワードを再発行しました。</p>
    <?php } else { ?>
      <form action="./forgetpassword.php" method="post">
        <label>メールアドレス</label><br>
        <input type="email" name="email" value="<?php echo htmlspecialchars($_POST['email']); ?>"><br>

        <input type="submit" name="recreate" value="再発行">
      </form>
    <?php } ?>

  </body>
</html>
