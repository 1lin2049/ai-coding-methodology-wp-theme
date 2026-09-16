<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

global $wp_query;
$found = (int) $wp_query->found_posts;
?>

<div class="layout-shell">
  <?php if ( ai_layout_has_sidebar() && ai_get_layout() === 'sidebar-left' ) get_sidebar(); ?>

  <main id="top" class="layout-main">
    <article class="section">
      <div class="section-head">
        <div class="section-tag">
          <span class="prompt">$</span>
          <span>grep</span>
          <span class="path">"<?php echo esc_html( get_search_query() ); ?>"</span>
        </div>

        <h1 class="section-title">
          <?php if ( $found > 0 ) : ?>
            找到 <em><?php echo $found; ?></em> 条结果
          <?php else : ?>
            没有找到<em>相关结果</em>
          <?php endif; ?>
        </h1>

        <div class="searchform-wrap">
          <?php get_search_form(); ?>
        </div>
      </div>

      <?php if ( have_posts() ) : ?>
        <div class="preview-list">
          <?php while ( have_posts() ) : the_post();
              $pt_obj = get_post_type_object( get_post_type() );
              $pt_label = $pt_obj ? $pt_obj->labels->singular_name : get_post_type();
              ?>
            <a class="prev-row" href="<?php the_permalink(); ?>">
              <span class="prev-id"><?php echo esc_html( $pt_label ); ?></span>
              <span class="prev-title"><?php the_title(); ?></span>
              <span class="prev-meta"><?php echo esc_html( get_the_date( 'Y-m-d' ) ); ?></span>
              <span class="prev-arrow">→</span>
            </a>
          <?php endwhile; ?>
        </div>

        <?php
        the_posts_pagination( [
            'mid_size'  => 1,
            'prev_text' => '← 上一页',
            'next_text' => '下一页 →',
        ] );
        ?>

      <?php else : ?>
        <div class="entry-content">
          <p>换个关键词试试。</p>
        </div>
      <?php endif; ?>
    </article>
  </main>

  <?php if ( ai_layout_has_sidebar() && ai_get_layout() === 'sidebar-right' ) get_sidebar(); ?>
</div>

<?php get_footer(); ?>