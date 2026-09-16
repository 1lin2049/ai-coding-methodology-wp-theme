<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>

<div class="layout-shell">
  <?php if ( ai_layout_has_sidebar() && ai_get_layout() === 'sidebar-left' ) get_sidebar(); ?>

  <main id="top" class="layout-main">
    <?php if ( have_posts() ) : ?>
      <?php while ( have_posts() ) : the_post(); ?>
        <article <?php post_class( 'section' ); ?> id="post-<?php the_ID(); ?>">
          <div class="section-head">
            <div class="section-tag">
              <span class="prompt">$</span>
              <span>cat</span>
              <span class="path">./<?php echo esc_html( get_post_field( 'post_name' ) ); ?>.md</span>
            </div>
            <h1 class="section-title">
              <?php if ( is_singular() ) : ?>
                <?php the_title(); ?>
              <?php else : ?>
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
              <?php endif; ?>
            </h1>

            <div class="entry-meta">
              <time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>">
                <?php echo esc_html( get_the_date() ); ?>
              </time>
              <span class="dot-sep">·</span>
              <span><?php the_author(); ?></span>
            </div>
          </div>

          <div class="entry-content">
            <?php if ( is_singular() ) : ?>
              <?php the_content(); ?>
            <?php else : ?>
              <?php the_excerpt(); ?>
              <p style="margin-top:var(--space-4)">
                <a href="<?php the_permalink(); ?>">继续阅读 →</a>
              </p>
            <?php endif; ?>
          </div>
        </article>
      <?php endwhile; ?>

      <?php
      the_posts_pagination( [
          'mid_size'  => 1,
          'prev_text' => '← 上一页',
          'next_text' => '下一页 →',
      ] );
      ?>
    <?php else : ?>
      <article class="section">
        <div class="section-head">
          <div class="section-tag">
            <span class="prompt">$</span>
            <span>cat</span>
            <span class="path">./404.md</span>
          </div>
          <h1 class="section-title">没有内容</h1>
        </div>
        <div class="entry-content">
          <p>没有找到匹配的内容。</p>
        </div>
      </article>
    <?php endif; ?>
  </main>

  <?php if ( ai_layout_has_sidebar() && ai_get_layout() === 'sidebar-right' ) get_sidebar(); ?>
</div>

<?php get_footer(); ?>