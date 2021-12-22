<?php

require "./dbconnect.php";

// $dbName = $_SERVER['DB_NAME'];
// $host = $_SERVER['DB_HOST'];
// $user = $_SERVER['DB_USER'];
// $pass = $_SERVER['DB_PASS'];

//タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

$error_message = [];

session_start();

dbConnect($_SERVER['DB_NAME'], $_SERVER['DB_HOST'], $_SERVER['DB_USER'], $_SERVER['DB_PASS']);

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

//投稿表示
if (!empty($pdo)) {
  $sql = "SELECT * FROM messages ORDER BY post_date DESC";
  $message_data = $pdo->query($sql);
}

$pdo = null;
?>

<?php
$title = 'ようこそ掲示板へ';
include('header.php');
include('./contents/index-content.php');
?>
