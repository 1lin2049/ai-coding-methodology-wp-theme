<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if ( post_password_required() ) return;
?>
<section class="comments-area" id="comments">

  <?php if ( have_comments() ) : ?>
    <header class="comments-head">
      <span class="comments-prompt">$</span>
      <span class="comments-cmd">comments</span>
      <span class="comments-count">--count <?php echo (int) get_comments_number(); ?></span>
    </header>

    <ol class="comment-list">
      <?php
      wp_list_comments( [
          'style'       => 'ol',
          'short_ping'  => true,
          'avatar_size' => 32,
          'callback'    => 'ai_comment_template',
      ] );
      ?>
    </ol>

    <?php
    the_comments_pagination( [
        'prev_text' => '← 上一页',
        'next_text' => '下一页 →',
    ] );
    ?>
  <?php endif; ?>

  <?php if ( ! is_user_logged_in()
          && ai_comment_policy_login_required()
          && ! ai_comment_guest_allowed( get_the_ID() ) ) : ?>

    <div class="comments-locked">
      <div class="comments-locked-icon">
        <?php echo ai_icon( 'user', 20 ); ?>
      </div>
      <div class="comments-locked-body">
        <strong>登录后可参与评论</strong>
        <span>本站仅向已登录用户开放评论</span>
      </div>
      <a class="comments-locked-btn" href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>">
        <span class="prompt">$</span>
        <span>login</span>
        <span class="arrow">→</span>
      </a>
    </div>

  <?php elseif ( ! comments_open() && get_comments_number() ) : ?>

    <p class="comments-closed">
      <span class="comments-hash">#</span>评论已关闭
    </p>

  <?php else : ?>

    <?php
    comment_form( [
        'title_reply'          => '<span class="cf-prompt">$</span><span class="cf-cmd">new comment</span>',
        'title_reply_before'   => '<h3 class="comment-reply-title">',
        'title_reply_after'    => '</h3>',
        'comment_notes_before' => '',
        'comment_notes_after'  => '',
        'label_submit'         => '提交评论',
        'comment_field'        => '<p class="comment-form-comment"><label for="comment" class="screen-reader-text">评论内容</label><textarea id="comment" name="comment" rows="6" required></textarea></p>',
        'fields'               => [
            'author' => '<p class="comment-form-author"><label for="author" class="screen-reader-text">昵称</label><input id="author" name="author" type="text" placeholder="昵称 *" required></p>',
            'email'  => '<p class="comment-form-email"><label for="email" class="screen-reader-text">邮箱</label><input id="email" name="email" type="email" placeholder="邮箱 *" required></p>',
            'url'    => '<p class="comment-form-url"><label for="url" class="screen-reader-text">网站</label><input id="url" name="url" type="url" placeholder="网站"></p>',
        ],
    ] );
    ?>

  <?php endif; ?>

</section>