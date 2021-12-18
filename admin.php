<?php
define('PASSWORD', 'adminPassword');

//タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

$error_message = [];

session_start();

if (!empty($_GET['btn_logout'])) {
  unset($_SESSION['admin_login']);
}

//データベースに接続
try {
  $option = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::MYSQL_ATTR_MULTI_STATEMENTS => false,
  ];
  $pdo = new PDO('mysql:charset=UTF8;dbname=board;host=localhost;', 'root', 'root', $option);
} catch (PDOException $e) {
  //接続エラーの時のエラー内容を取得
  $error_message[] = $e->getMessage();
}

if (!empty($_POST['btn_submit'])) {
  if (!empty($_POST['admin_password']) && $_POST['admin_password'] === PASSWORD) {
    $_SESSION['admin_login'] = true;
  } else {
    $error_message[] = 'ログインに失敗しました';
  }
}

if (!empty($pdo)) {
  $sql = "SELECT * FROM messages ORDER BY post_date DESC";
  $message_data = $pdo->query($sql);
}

$pdo = null;

?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>掲示板 管理者ページ</title>
  <link rel="stylesheet" href="./css/reset.css">
  <link rel="stylesheet" href="./css/style.min.css">
</head>

<body>
  <header class="header">
    <h1>
      <a class="admin_ttl" href="./index.php">
        掲示板 管理者ページ
      </a>
    </h1>
  </header>
  <div class="form-area">
    <div class="wrapper form-area__inner">
      <?php if (!empty($_SESSION['admin_login']) && $_SESSION['admin_login'] === true) : ?>
        <form action="" method="get">
          <input type="submit" id="btn_logout" name="btn_logout" value="ログアウト">
        </form>
      <?php endif; ?>
      <?php if (!empty($error_message)) : ?>
        <ul>
          <?php foreach ($error_message as $value) : ?>
            <li class="error-list"><?php echo $value; ?></li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
      <form action="./download.php" method="get">
        <select class="limit" name="limit">
          <option value="">全て</option>
          <option value="10">10件</option>
          <option value="20">20件</option>
        </select>
        <input type="submit" id="btn_download" name="btn_download" value="ダウンロード">
      </form>
    </div>
  </div>
  <div class="comment-area">
    <div class="wrapper">
      <ul>
        <?php if (!empty($_SESSION['admin_login']) && $_SESSION['admin_login'] === true) : ?>
          <?php if (!empty($message_data)) : ?>
            <?php foreach ($message_data as $value) : ?>
              <li class="list">
                <p class="list__touch"><a class="btn_blue" href="edit.php?message_id=<?php echo $value['id']; ?>">編集</a> <a class="btn_blue" href="delete.php?message_id=<?php echo $value['id']; ?>">削除</a></p>
                <p class="head">
                  <span class="title"><?php echo $value['title']; ?></span><span class="time"><?php echo $value['post_data']; ?></span>
                </p>
                <p class="message">
                  <?php echo nl2br($value['message']); ?>
                </p>
              </li>
            <?php endforeach; ?>
          <?php endif; ?>
        <?php else : ?>
          <!-- ログインフォームの設置 -->
          <form action="" method="post">
            <!-- パスワードのみ -->
            <div class="login-form">
              <label for="admin_password">パスワード</label>
              <input type="password" id="admin_password" name="admin_password" value="">
            </div>
            <input type="submit" name="btn_submit" value="ログイン" id="btn_submit">
          </form>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</body>

</html>
