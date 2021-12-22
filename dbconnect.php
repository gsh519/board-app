<?php
//データベースに接続
function dbConnect($dbName, $host, $user, $pass)
{
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

  return $pdo;
}
