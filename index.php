<?php
// ファイルのパス設定
define('FILENAME', './message.txt');

//タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

$message = [];
$message_data = [];
$error_message = [];
$clean = [];


if (!empty($_POST['btn_submit'])) {
  //投稿バリデーション
  //表示名のフォームチェック
  if (empty($_POST['title'])) {
    $error_message[] = '表示名を入力してください';
  } else {
    $clean['title'] = htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8');
    $clean['title'] = preg_replace('/\\r\\n|\\n|\\r/', '', $clean['title']);
  }

  //メッセージのフォームチェック
  if (empty($_POST['message'])) {
    $error_message[] = 'メッセージを入力してください';
  } else {
    $clean['message'] = htmlspecialchars($_POST['message'], ENT_QUOTES, 'UTF-8');
    $clean['message'] = preg_replace('/\\r\\n|\\n|\\r/', '<br>', $clean['message']);
  }


  if (empty($error_message)) {
    if ($file_handle = fopen(FILENAME, "a")) {
      $current_date = date("Y-m-d H:i:s");

      $data =
        "'" . $clean['title'] . "','" . $clean['message'] . "','" . $current_date . "'\n";

      fwrite($file_handle, $data);
      fclose($file_handle);

      $success_message = 'メッセージを書き込みました';
    }
  }

  if ($file_handle = fopen(FILENAME, "r")) {
    while ($data = fgets($file_handle)) {
      $split_data = preg_split('/\'/', $data);

      $message = [
        'title' => $split_data[1],
        'message' => $split_data[3],
        'post_data' => $split_data[5],
      ];

      array_unshift($message_data, $message);
    }

    fclose($file_handle);
  }
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>掲示板</title>
  <link rel="stylesheet" href="./css/reset.css">
  <link rel="stylesheet" href="./css/style.css">
</head>

<body>
  <header class="header">
    <h1>ようこそ掲示板へ</h1>
  </header>
  <div class="form-area">
    <div class="wrapper form-area__inner">
      <form action="" method="POST">
        <div class="title-area">
          <label for="title">表示名</label>
          <input type="text" id="title" name="title" value="">
        </div>
        <div class="message-area">
          <label for="message">メッセージ</label>
          <textarea id="message" name="message"></textarea>
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
      <?php if (!empty($success_message)) : ?>
        <p class="success-message"><?php echo $success_message; ?></p>
      <?php endif; ?>
      <ul>
        <?php if (!empty($message_data)) : ?>
          <?php foreach ($message_data as $value) : ?>
            <li class="list">
              <p class="head">
                <span class="title"><?php echo $value['title']; ?></span><span class="time"><?php echo $value['post_data']; ?></span>
              </p>
              <p class="message">
                <?php echo $value['message']; ?>
              </p>
            </li>
          <?php endforeach; ?>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</body>

</html>
