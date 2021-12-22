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
