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
