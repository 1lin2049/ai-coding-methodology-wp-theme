<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>

<div class="layout-shell">
  <?php if ( ai_layout_has_sidebar() && ai_get_layout() === 'sidebar-left' ) get_sidebar(); ?>

  <main id="top" class="layout-main">
    <article class="section">
      <div class="section-head">
        <div class="section-tag">
          <span class="prompt">$</span>
          <span>ls</span>
          <span class="path">./archive/</span>
        </div>

        <h1 class="section-title"><?php the_archive_title(); ?></h1>

        <?php if ( get_the_archive_description() ) : ?>
          <p class="section-desc"><?php echo wp_kses_post( get_the_archive_description() ); ?></p>
        <?php endif; ?>
      </div>

      <?php if ( have_posts() ) : ?>
        <div class="ls-block">
          <div class="ls-head">
            <span class="ls-h">DATE</span>
            <span class="ls-h">NAME</span>
            <span class="ls-h">TYPE</span>
            <span class="ls-h">TAG</span>
          </div>

          <?php while ( have_posts() ) : the_post();
              $pt_obj = get_post_type_object( get_post_type() );
              $pt_label = $pt_obj ? $pt_obj->labels->singular_name : get_post_type();

              $tag_text = 'ENTRY';
              $tag_cls  = 'tag-ct';
              if ( get_post_type() === 'chapter' ) {
                  $tag_text = 'CHAPTER';
                  $tag_cls  = 'tag-sl';
              } elseif ( get_post_type() === 'post' ) {
                  $tag_text = 'POST';
                  $tag_cls  = 'tag-bd';
              }
              ?>
            <a class="ls-row" href="<?php the_permalink(); ?>">
              <span class="ls-mode"><?php echo esc_html( get_the_date( 'Y-m-d' ) ); ?></span>
              <span class="ls-name"><?php the_title(); ?></span>
              <span class="ls-size"><?php echo esc_html( $pt_label ); ?></span>
              <span class="ls-tag <?php echo esc_attr( $tag_cls ); ?>"><?php echo esc_html( $tag_text ); ?></span>
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
          <p>暂无内容。</p>
        </div>
      <?php endif; ?>
    </article>
  </main>

  <?php if ( ai_layout_has_sidebar() && ai_get_layout() === 'sidebar-right' ) get_sidebar(); ?>
</div>

<?php get_footer(); ?>