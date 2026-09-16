<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/* ═══════════════════════════════════════════════
   评论策略 · 判定
   ═══════════════════════════════════════════════ */

/**
 * 全局策略：未登录是否被禁止评论
 */
function ai_comment_policy_login_required() {
    return get_theme_mod( 'ai_comment_login_required', '1' ) === '1';
}

/**
 * 判断当前 post 是否在白名单中
 * 支持：ID 列表 / 分类 slug / 标签 slug
 */
function ai_comment_guest_allowed( $post_id ) {
    $post_id = (int) $post_id;
    if ( ! $post_id ) return false;

    /* ID 白名单 */
    $ids_raw = (string) get_theme_mod( 'ai_comment_guest_allow_ids', '' );
    if ( $ids_raw !== '' ) {
        $ids = array_filter( array_map( 'absint', preg_split( '/[\s,，;；]+/', $ids_raw ) ) );
        if ( in_array( $post_id, $ids, true ) ) {
            return true;
        }
    }

    /* 分类 slug 白名单 */
    $cats_raw = trim( (string) get_theme_mod( 'ai_comment_guest_allow_cats', '' ) );
    if ( $cats_raw !== '' && has_category( $cats_raw, $post_id ) ) {
        return true;
    }

    /* 标签 slug 白名单 */
    $tags_raw = trim( (string) get_theme_mod( 'ai_comment_guest_allow_tags', '' ) );
    if ( $tags_raw !== '' && has_tag( $tags_raw, $post_id ) ) {
        return true;
    }

    return false;
}

/**
 * comments_open filter 主入口
 */
function ai_comment_policy_filter( $open, $post_id ) {
    if ( is_admin() ) return $open;
    if ( is_user_logged_in() ) return $open;

    /* 策略未启用 → 保持 WordPress 原生行为 */
    if ( ! ai_comment_policy_login_required() ) return $open;

    /* 命中白名单 → 放行 */
    if ( ai_comment_guest_allowed( $post_id ) ) return $open;

    return false;
}
add_filter( 'comments_open', 'ai_comment_policy_filter', 10, 2 );
add_filter( 'pings_open',    'ai_comment_policy_filter', 10, 2 );


/* ═══════════════════════════════════════════════
   评论渲染回调 · Ant Design Comment 结构
   ═══════════════════════════════════════════════ */
if ( ! function_exists( 'ai_comment_template' ) ) {
    function ai_comment_template( $comment, $args, $depth ) {
        ?>
        <li <?php comment_class( 'comment-item' ); ?> id="comment-<?php comment_ID(); ?>">
          <article class="comment-body">

            <?php
            $avatar = get_avatar( $comment, 32, '', '', [ 'class' => 'avatar' ] );
            if ( $avatar ) echo $avatar;
            ?>

            <div class="comment-content-wrap">

              <div class="comment-author">
                <cite class="fn"><?php echo get_comment_author_link(); ?></cite>
                <span class="says">说：</span>
              </div>

              <div class="comment-metadata">
                <a href="<?php echo esc_url( get_comment_link( $comment ) ); ?>">
                  <time datetime="<?php comment_time( 'c' ); ?>">
                    <?php echo esc_html( get_comment_date() . ' ' . get_comment_time() ); ?>
                  </time>
                </a>
                <?php edit_comment_link( '编辑', '<span class="edit-link">', '</span>' ); ?>
              </div>

              <div class="comment-content">
                <?php comment_text(); ?>
              </div>

              <?php if ( comments_open() ) : ?>
                <div class="comment-actions">
                  <?php
                  comment_reply_link( array_merge( $args, [
                      'depth'      => $depth,
                      'max_depth'  => $args['max_depth'] ?? 5,
                      'reply_text' => '回复',
                  ] ) );
                  ?>
                </div>
              <?php endif; ?>

            </div>
          </article>
        <?php
    }
}