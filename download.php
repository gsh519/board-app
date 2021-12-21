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

session_start();

if (!empty($_GET['limit'])) {
  if ($_GET['limit'] === "10") {
    $limit = 10;
  } elseif ($_GET['limit'] === "20") {
    $limit = 20;
  }
}

if (!empty($_SESSION['admin_login']) && $_SESSION['admin_login'] === true) {
  // 投稿データ取得
  try {
    dbConnect();

    if (!empty($_GET['limit'])) {
      //SQLの作成
      $stmt = $pdo->prepare("SELECT * FROM messages ORDER BY post_date DESC LIMIT :limit");
      $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    } else {
      $stmt = $pdo->prepare("SELECT * FROM messages ORDER BY post_date DESC");
    }

    $stmt->execute();
    $message_data = $stmt->fetchAll();

    $stmt = null;
    $pdo = null;
  } catch (PDOException $e) {
    header("Location: ./admin.php");
    exit;
  }

  //csvファイル出力の設定
  header("Content-Type: text/csv");
  header("Content-Disposition: attachment; filename=メッセージデータ.csv");
  header("Content-Transfer-Encoding: binary");

  //csvデータの作成
  if (!empty($message_data)) {
    $csv_data .= '"ID","表示名","メッセージ","投稿日時"' . "\n";

    foreach ($message_data as $value) {
      $csv_data .= '"' . $value['id'] . '","' . $value['title'] . '","' . $value['message'] . '","' . $value['post_date'] . "\"\n";
    }
  }

  // ファイル出力
  echo $csv_data;
} else {
  // ログインページへリダイレクト
  header("Location: ./admin.php");
  exit;
}

return;
