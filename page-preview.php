<?php
/**
 * Template Name: 试读目录
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$chapters = get_posts( [
    'post_type'      => 'chapter',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
] );

$total_words   = 0;
$total_minutes = 0;
foreach ( $chapters as $c ) {
    $total_words   += ai_count_words( $c->ID );
    $total_minutes += ai_get_reading_minutes( $c->ID );
}

$grouped   = [];
$ungrouped = [];
foreach ( $chapters as $c ) {
    $terms = wp_get_post_terms( $c->ID, 'chapter_section', [ 'fields' => 'all' ] );
    if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
        $term = $terms[0];
        $grouped[ $term->slug ]['name']    = $term->name;
        $grouped[ $term->slug ]['items'][] = $c;
    } else {
        $ungrouped[] = $c;
    }
}

$show_section_title = ! empty( $grouped ) && count( $grouped ) > 1;

$home = home_url( '/' );
$buy  = home_url( '/#pricing' );
?>
<!doctype html>
<html <?php language_attributes(); ?> data-theme="dark">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="theme-color" content="#08080a">
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
<body <?php body_class( 'r-reader r-preview-index' ); ?>>
<?php wp_body_open(); ?>

<header class="r-header">
  <a class="r-back" href="<?php echo esc_url( $home ); ?>">← 返回首页</a>
  <div class="r-title">试读目录</div>
  <div class="r-actions">
    <button class="theme-toggle" id="themeToggle" aria-label="切换主题">
      <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
      <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
    </button>
  </div>
</header>

<main class="r-main">
  <div class="r-wrap">

    <div class="r-head">
      <div class="r-eyebrow">
        <span class="r-prompt">$</span>
        <span>open</span>
        <span class="r-path">./preview/</span>
      </div>
      <h1 class="r-h1">先从问题本身<em>开始</em></h1>

      <!-- ★ 卡片化概览 -->
      <div class="r-overview">
        <div class="r-ov-item">
          <span class="r-ov-num"><?php echo count( $chapters ); ?></span>
          <span class="r-ov-label">章节</span>
        </div>
        <div class="r-ov-item">
          <span class="r-ov-num"><?php echo number_format( $total_words ); ?></span>
          <span class="r-ov-label">总字数</span>
        </div>
        <div class="r-ov-item">
          <span class="r-ov-num"><?php echo $total_minutes; ?></span>
          <span class="r-ov-label">分钟</span>
        </div>
      </div>
    </div>

    <?php if ( $show_section_title ) : ?>
      <?php foreach ( $grouped as $slug => $group ) : ?>
        <div class="r-divider">
          <span class="r-divider-label"><?php echo esc_html( $group['name'] ); ?></span>
        </div>
        <div class="r-list">
          <?php foreach ( $group['items'] as $c ) :
              $num     = get_post_meta( $c->ID, 'chapter_number', true );
              $words   = ai_count_words( $c->ID );
              $minutes = ai_get_reading_minutes( $c->ID );
          ?>
            <a class="r-card" href="<?php echo esc_url( get_permalink( $c ) ); ?>" data-chapter-id="<?php echo esc_attr( $c->ID ); ?>">
              <div class="r-card-num"><?php echo esc_html( $num ); ?></div>
              <div class="r-card-body">
                <div class="r-card-title"><?php echo esc_html( $c->post_title ); ?></div>
                <div class="r-card-meta">
                  <?php echo number_format( $words ); ?> 字 · <?php echo $minutes; ?> 分钟
                </div>
              </div>
              <div class="r-card-arrow">→</div>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <?php if ( ! empty( $ungrouped ) ) : ?>
      <?php if ( $show_section_title ) : ?>
        <div class="r-divider">
          <span class="r-divider-label">其他章节</span>
        </div>
      <?php endif; ?>
      <div class="r-list">
        <?php foreach ( $ungrouped as $c ) :
            $num     = get_post_meta( $c->ID, 'chapter_number', true );
            $words   = ai_count_words( $c->ID );
            $minutes = ai_get_reading_minutes( $c->ID );
        ?>
          <a class="r-card" href="<?php echo esc_url( get_permalink( $c ) ); ?>" data-chapter-id="<?php echo esc_attr( $c->ID ); ?>">
            <div class="r-card-num"><?php echo esc_html( $num ); ?></div>
            <div class="r-card-body">
              <div class="r-card-title"><?php echo esc_html( $c->post_title ); ?></div>
              <div class="r-card-meta">
                <?php echo number_format( $words ); ?> 字 · <?php echo $minutes; ?> 分钟
              </div>
            </div>
            <div class="r-card-arrow">→</div>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if ( empty( $chapters ) ) : ?>
      <p class="r-empty">// 尚未导入章节</p>
    <?php endif; ?>

    <div class="r-unlock">
      <div class="r-unlock-text">
        <strong>解锁全本 20 章 + 12 附录</strong>
        <span>获取工程资产包 · 四会话 Prompt · 角色-能力矩阵</span>
      </div>
      <a class="r-unlock-btn" href="<?php echo esc_url( $buy ); ?>">
        <span class="r-prompt">$</span>
        <span>unlock --all</span>
        <span class="r-unlock-arrow">→</span>
      </a>
    </div>

    <div class="r-foot">
      <span>FREE PREVIEW</span>
      <span class="r-foot-sep">·</span>
      <span>无需注册</span>
      <span class="r-foot-sep">·</span>
      <span>无需付费</span>
      <span class="r-foot-sep">·</span>
      <span class="r-foot-quote">代码是结果，约束才是生产系统</span>
    </div>
  </div>
</main>

<?php wp_footer(); ?>
</body>
</html>