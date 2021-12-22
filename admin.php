<?php
$login_password = $_SERVER['LOGIN_PASSWORD'];
$dbName = $_SERVER['DB_NAME'];
$host = $_SERVER['DB_HOST'];
$user = $_SERVER['DB_USER'];
$pass = $_SERVER['DB_PASS'];

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
  $pdo = new PDO('mysql:charset=UTF8;dbname=' . $dbName . ';host=' . $host, $user, $pass, $option);
} catch (PDOException $e) {
  //接続エラーの時のエラー内容を取得
  $error_message[] = $e->getMessage();
}

if (!empty($_POST['btn_submit'])) {
  if (!empty($_POST['admin_password']) && $_POST['admin_password'] === $login_password) {
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

<?php
$title = '掲示板 管理者ページ';
include('header.php');
?>

<?php
include('./contents/admin-content.php');
?>
