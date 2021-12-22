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

<?php
$title = '掲示板 投稿削除';
include('header.php');
?>

<?php
include('./contents/delete-content.php');
?>
