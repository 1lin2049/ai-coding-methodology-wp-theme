<?php
while ( have_posts() ) : the_post();

    $number = get_post_meta( get_the_ID(), 'chapter_number', true );

    $siblings = get_posts( [
        'post_type'      => 'chapter',
        'posts_per_page' => -1,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
        'fields'         => 'ids',
    ] );
    $idx  = array_search( get_the_ID(), $siblings );
    $prev = ( $idx !== false && $idx > 0 ) ? get_post( $siblings[ $idx - 1 ] ) : null;
    $next = ( $idx !== false && $idx < count( $siblings ) - 1 ) ? get_post( $siblings[ $idx + 1 ] ) : null;
    $prev_num = $prev ? get_post_meta( $prev->ID, 'chapter_number', true ) : '';
    $next_num = $next ? get_post_meta( $next->ID, 'chapter_number', true ) : '';
    $toc_url  = home_url( '/preview/' );
    $buy_url  = home_url( '/#pricing' );

    /* ★ 布局 */
    $layout       = ai_get_layout();
    $has_sidebar  = ai_layout_has_sidebar( $layout );
    $sidebar_id   = ai_layout_sidebar_id( $layout );
    $sidebar_left = ( $layout === 'sidebar-left' );
?>
<!doctype html>
<html <?php language_attributes(); ?> data-theme="dark">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="theme-color" content="#08080a">
<meta name="ai-chapter-slug" content="<?php echo esc_attr( get_post_field( 'post_name' ) ); ?>">
<script>
(function(){
  try {
    var t = localStorage.getItem('theme');
    if (t !== 'light' && t !== 'dark') {
      t = (window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches)
            ? 'light' : 'dark';
    }
    document.documentElement.setAttribute('data-theme', t);
  } catch (e) {}
})();
</script>
<?php wp_head(); ?>
</head>
<body <?php body_class( 'r-reader r-single-chapter' ); ?>>
<?php wp_body_open(); ?>

<div class="r-progress-bar" id="rProgressBar"></div>
<nav class="r-toc" id="rToc" aria-label="章节导航"></nav>

<div class="r-resume-prompt" id="rResumePrompt" hidden role="status" aria-live="polite">
  <span class="r-resume-text"></span>
  <button type="button" class="r-resume-continue">继续阅读</button>
  <button type="button" class="r-resume-restart">从头开始</button>
</div>

<header class="r-header">
  <a href="<?php echo esc_url( $toc_url ); ?>" class="r-back">← 返回试读</a>
  <div class="r-title"><?php echo esc_html( $number . ' · ' . get_the_title() ); ?></div>
  <div class="r-actions">
    <button class="theme-toggle" id="themeToggle" aria-label="切换主题">
      <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
      <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
    </button>
  </div>
</header>

<div class="r-layout-shell">

  <?php if ( $has_sidebar && $sidebar_left ) get_sidebar(); ?>

  <main id="top" class="r-main-col">
    <article class="r-article-wrap" data-print-src="<?php echo esc_attr( home_url( '/' ) ); ?>">
      <div class="r-article-head">
        <div class="r-article-ch">CHAPTER <?php echo esc_html( $number ); ?></div>
        <h1 class="r-article-title"><?php the_title(); ?></h1>

        <?php if ( get_the_excerpt() ) : ?>
          <p class="r-article-lead"><?php echo esc_html( get_the_excerpt() ); ?></p>
        <?php endif; ?>

        <div class="entry-meta">
          <?php echo ai_render_meta_extra(); ?>
        </div>
      </div>

      <div class="r-article">
        <?php the_content(); ?>
      </div>

      <div class="r-end">
        <div class="r-end-label">END OF <?php echo esc_html( $number ); ?></div>
        <p class="r-end-text">
          <?php echo $next ? '下一章：' . esc_html( $next->post_title ) : '本书试读部分到此结束。'; ?>
        </p>
        <div class="r-end-actions">
          <?php if ( $prev ) : ?>
            <a class="r-end-btn r-end-btn-ghost" href="<?php echo esc_url( get_permalink( $prev ) ); ?>">← <?php echo esc_html( $prev_num ); ?></a>
          <?php else : ?>
            <a class="r-end-btn r-end-btn-ghost" href="<?php echo esc_url( $toc_url ); ?>">返回目录</a>
          <?php endif; ?>

          <?php if ( $next ) : ?>
            <a class="r-end-btn r-end-btn-primary" href="<?php echo esc_url( get_permalink( $next ) ); ?>"><?php echo esc_html( $next_num ); ?> →</a>
          <?php else : ?>
            <a class="r-end-btn r-end-btn-primary" href="<?php echo esc_url( $buy_url ); ?>">解锁全本 →</a>
          <?php endif; ?>
        </div>
      </div>

      <?php if ( comments_open() || get_comments_number() ) : ?>
        <section class="r-comments">
          <?php comments_template(); ?>
        </section>
      <?php endif; ?>
    </article>
  </main>

  <?php if ( $has_sidebar && ! $sidebar_left ) get_sidebar(); ?>

</div>

<div class="r-bottom">
  <div class="r-progress-txt" id="rProgressTxt">完成 0%</div>
  <div class="r-bottom-nav">
    <?php if ( $prev ) : ?>
      <a href="<?php echo esc_url( get_permalink( $prev ) ); ?>">← <?php echo esc_html( $prev_num ); ?></a>
    <?php else : ?>
      <a href="<?php echo esc_url( $toc_url ); ?>">← 目录</a>
    <?php endif; ?>

    <a href="<?php echo esc_url( $toc_url ); ?>">目录</a>

    <?php if ( $next ) : ?>
      <a href="<?php echo esc_url( get_permalink( $next ) ); ?>"><?php echo esc_html( $next_num ); ?> →</a>
    <?php else : ?>
      <a href="<?php echo esc_url( $buy_url ); ?>">解锁全本 →</a>
    <?php endif; ?>
  </div>
  <a href="<?php echo esc_url( $buy_url ); ?>" class="r-bottom-unlock">解锁全本 →</a>
</div>

<?php wp_footer(); ?>
</body>
</html>
<?php endwhile; ?>