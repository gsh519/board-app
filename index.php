<?php
function dbConnect()
{
  $db = parse_url($_SERVER['CLEARDB_DATABASE_URL']);
  $db['dbname'] = ltrim($db['path'], '/');
  $dsn = "mysql:host={$db['host']};dbname={$db['dbname']};charset=utf8";
  $user = $db['user'];
  $password = $db['pass'];
  $options = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
  );
  $pdo = new PDO($dsn, $user, $password, $options);
  return $pdo;
}

//タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

$error_message = [];

session_start();

//データベースに接続
try {
  dbConnect();
  var_dump($_SERVER['CLEARDB_DATABASE_URL']);
} catch (PDOException $e) {
  //接続エラーの時のエラー内容を取得
  $error_message[] = $e->getMessage();
}

if (!empty($_POST['btn_submit'])) {
  //投稿バリデーション
  $title = preg_replace('/\A[\p{C}\p{Z}]++|[\p{C}\p{Z}]++\z/u', '', $_POST['title']);

  $message = preg_replace('/\A[\p{C}\p{Z}]++|[\p{C}\p{Z}]++\z/u', '', $_POST['message']);

  //表示名のフォームチェック
  if (empty($title)) {
    $error_message[] = '表示名を入力してください';
  } else {
    $_SESSION['title'] = $title;
  }

  //メッセージのフォームチェック
  if (empty($message)) {
    $error_message[] = 'メッセージを入力してください';
  } else {
    if (mb_strlen($message, 'UTF-8') > 100) {
      $error_message[] = '一言メッセージは100文字以内にしてください';
    }
  }


  if (empty($error_message)) {
    //日付取得
    $current_date = date("Y-m-d H:i:s");

    $pdo->beginTransaction();

    try {
      //SQL文を作成
      $stmt = $pdo->prepare("INSERT INTO messages (title, message, post_date) VALUES (:title, :message, :current_date)");

      //値をセット
      $stmt->bindParam(':title', $title, PDO::PARAM_STR);
      $stmt->bindParam(':message', $message, PDO::PARAM_STR);
      $stmt->bindParam(':current_date', $current_date, PDO::PARAM_STR);

      // SQLクエリの実行
      $stmt->execute();

      //コミット
      $res = $pdo->commit();
    } catch (Exception $e) {
      //エラーが有った場合
      $pdo->rollBack();
      var_dump($e->getMessage());
    }

    if ($res) {
      $_SESSION['success_message'] = 'メッセージを書き込みました';
    } else {
      $error_message[] = '書き込みに失敗しました';
    }

    $stmt = null;

    header("Location: ./");
    exit;
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
  <title>掲示板</title>
  <link rel="stylesheet" href="./css/reset.css">
  <link rel="stylesheet" href="./css/style.min.css">
</head>

<body>
  <header class="header">
    <h1>ようこそ掲示板へ</h1>
  </header>
  <div class="form-area">
    <div class="wrapper form-area__inner">
      <a class="btn_gray" href="./admin.php">管理者ページへ</a>
      <form action="" method="POST">
        <div class="title-area">
          <label for="title">表示名</label>
          <input type="text" id="title" name="title" value="<?php if (!empty($_SESSION['title'])) {
                                                              echo htmlspecialchars($_SESSION['title'], ENT_QUOTES, 'UTF-8');
                                                            } ?>">
        </div>
        <div class="message-area">
          <label for="message">メッセージ</label>
          <textarea id="message" name="message"><?php if (!empty($message)) {
                                                  echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
                                                } ?></textarea>
        </div>
        <input type="submit" id="btn_submit" name="btn_submit" value="書き込む">
      </form>
      <?php if (!empty($error_message)) : ?>
        <ul>
          <?php foreach ($error_message as $value) : ?>
            <li class="error-list"><?php echo $value; ?></li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
  </div>
  <div class="comment-area">
    <div class="wrapper">
      <?php if (empty($_POST['btn_submit']) && !empty($_SESSION['success_message'])) : ?>
        <p class="success-message"><?php echo htmlspecialchars($_SESSION['success_message'], ENT_QUOTES, 'UTF-8'); ?></p>
        <?php unset($_SESSION['success_message']); ?>
      <?php endif; ?>
      <ul>
        <?php if (!empty($message_data)) : ?>
          <?php foreach ($message_data as $value) : ?>
            <li class="list">
              <p class="head">
                <span class="title"><?php echo htmlspecialchars($value['title'], ENT_QUOTES, 'UTF-8'); ?></span><span class="time"><?php echo $value['post_data']; ?></span>
              </p>
              <p class="message">
                <?php echo nl2br(htmlspecialchars($value['message'], ENT_QUOTES, 'UTF-8')); ?>
              </p>
            </li>
          <?php endforeach; ?>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</body>

</html>
