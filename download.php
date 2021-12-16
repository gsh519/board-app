<?php
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
    $option = [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::MYSQL_ATTR_MULTI_STATEMENTS => false,
    ];
    $pdo = new PDO('mysql:charset=UTF8;dbname=board;host=localhost;', 'root', 'root', $option);

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
