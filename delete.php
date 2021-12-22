<?php

$dbName = $_SERVER['DB_NAME'];
$host = $_SERVER['DB_HOST'];
$user = $_SERVER['DB_USER'];
$pass = $_SERVER['DB_PASS'];

//タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

$error_message = [];

session_start();

//管理者としてログインしているか確認
if (empty($_SESSION['admin_login']) || $_SESSION['admin_login'] !== true) {
  // ログインページへリダイレクト
  header("Location: ./admin.php");
  exit;
}

//データベースに接続
try {
  $option = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::MYSQL_ATTR_MULTI_STATEMENTS => false,
  ];
  $pdo = new PDO('mysql:charset=UTF8;dbname=' . $dbName . ';host=' . $host, $user, $pass, $option);
} catch (PDOException $e) {
  //接続エラーの時のエラー内容を取得
  $error_message[] = $e->getMessage();
}

if (!empty($_GET['message_id']) && empty($_POST['message_id'])) {
  // SQL作成
  $stmt = $pdo->prepare("SELECT * FROM messages WHERE id = :id");

  // 値をセット
  $stmt->bindValue(':id', $_GET['message_id'], PDO::PARAM_INT);

  // SQLクエリの実行
  $stmt->execute();

  // 表示するデータを取得
  $message_data = $stmt->fetch();

  // 投稿データが取得できないときは管理ページに戻る
  if (empty($message_data)) {
    header("Location: ./admin.php");
    exit;
  }
} elseif (!empty($_POST['message_id'])) {
  // トランザクションの開始
  $pdo->beginTransaction();

  try {
    // SQL作成
    $stmt = $pdo->prepare("DELETE FROM messages WHERE id = :id");

    $stmt->bindValue(':id', $_POST['message_id'], PDO::PARAM_INT);

    $stmt->execute();

    $res = $pdo->commit();
  } catch (Exception $e) {
    $pdo->rollBack();
  }

  if ($res) {
    header("Location: ./admin.php");
    exit;
  }
}

$stmt = null;
$pdo = null;

?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>掲示板 投稿削除</title>
  <link rel="stylesheet" href="./css/reset.css">
  <link rel="stylesheet" href="./css/style.min.css">
</head>

<body>
  <header class="header">
    <h1>掲示板 投稿削除</h1>
  </header>
  <div class="form-area">
    <div class="wrapper form-area__inner">
      <p>以下の投稿を削除しますよろしいですか？</p>
      <form action="" method="POST">
        <div class="title-area">
          <label for="title">表示名</label>
          <input disabled type="text" id="title" name="title" value="<?php if (!empty($message_data['title'])) {
                                                                        echo $message_data['title'];
                                                                      } elseif (!empty($title)) {
                                                                        echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
                                                                      } ?>">
        </div>
        <div class="message-area">
          <label for="message">メッセージ</label>
          <textarea disabled id="message" name="message"><?php if (!empty($message_data['message'])) {
                                                            echo $message_data['message'];
                                                          } elseif (!empty($message)) {
                                                            echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
                                                          } ?></textarea>
        </div>
        <a href="./admin.php" id="btn_cancel">キャンセル</a>
        <input type="submit" id="btn_submit" name="btn_submit" value="削除">
        <input type="hidden" name="message_id" value="<?php if (!empty($message_data['id'])) {
                                                        echo $message_data['id'];
                                                      } elseif (!empty($_POST['message_id'])) {
                                                        echo htmlspecialchars($_POST['message_id'], ENT_QUOTES, 'UTF-8');
                                                      } ?>">
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
</body>

</html>
