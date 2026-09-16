<?php
/**
 * Template Name: 联系页
 *
 * 联系方式通过后台「外观 → 自定义 → AI Coding 主题 → ⑬ 浮动操作按钮」
 * 中 scope=all 的 link 类型项自动生成。
 * 也可直接在页面正文中添加自定义内容。
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/* 从 float_actions 过滤出 link 类型作为联系卡片 */
$contacts = [];
foreach ( ai_opt_lines( 'float_actions' ) as $item ) {
    list( $type, $icon, $label, $url, $target, $scope ) = array_pad( $item, 6, '' );
    if ( $type !== 'link' ) continue;
    if ( ! $url ) continue;
    /* 排除首页锚点 */
    if ( strpos( $url, '#' ) === 0 ) continue;

    $contacts[] = [
        'icon'   => $icon ?: 'link',
        'label'  => $label,
        'url'    => $url,
        'target' => $target,
    ];
}

get_header();
?>

<div class="layout-shell">
  <?php if ( ai_layout_has_sidebar() && ai_get_layout() === 'sidebar-left' ) get_sidebar(); ?>

  <main id="top" class="layout-main">
    <?php while ( have_posts() ) : the_post(); ?>
      <article <?php post_class( 'section' ); ?>>
        <div class="section-head">
          <div class="section-tag">
            <span class="prompt">$</span>
            <span>mail</span>
            <span class="path">--to author</span>
          </div>
          <h1 class="section-title"><?php the_title(); ?></h1>
          <?php if ( get_the_excerpt() ) : ?>
            <p class="section-desc"><?php echo esc_html( get_the_excerpt() ); ?></p>
          <?php endif; ?>
        </div>

        <?php if ( trim( get_the_content() ) !== '' ) : ?>
          <div class="entry-content" style="margin-bottom:var(--space-12)">
            <?php the_content(); ?>
          </div>
        <?php endif; ?>

        <?php if ( ! empty( $contacts ) ) : ?>
          <div class="contact-grid">
            <?php foreach ( $contacts as $c ) : ?>
              <a class="contact-card"
                 href="<?php echo esc_url( $c['url'] ); ?>"
                 <?php echo $c['target'] === '_blank' ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
                <span class="contact-icon"><?php echo ai_icon( $c['icon'], 20 ); ?></span>
                <div class="contact-body">
                  <strong><?php echo esc_html( $c['label'] ); ?></strong>
                  <span><?php echo esc_html( $c['url'] ); ?></span>
                </div>
                <span class="contact-arrow">→</span>
              </a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </article>
    <?php endwhile; ?>
  </main>

  <?php if ( ai_layout_has_sidebar() && ai_get_layout() === 'sidebar-right' ) get_sidebar(); ?>
</div>

<?php get_footer(); ?>