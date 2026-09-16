<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>

<div class="layout-shell">
  <?php if ( ai_layout_has_sidebar() && ai_get_layout() === 'sidebar-left' ) get_sidebar(); ?>

  <main id="top" class="layout-main">
    <?php while ( have_posts() ) : the_post();

        $raw_slug = (string) get_post_field( 'post_name' );
        $looks_encoded = ( mb_strlen( $raw_slug ) > 32 )
                      || preg_match( '/[0-9a-f]{8,}/i', $raw_slug )
                      || preg_match( '/[0-9]{6,}/', $raw_slug );
        $slug_display = $looks_encoded ? 'post-' . get_the_ID() : $raw_slug;
        ?>
      <article <?php post_class( 'section' ); ?> id="post-<?php the_ID(); ?>">
        <div class="section-head">
          <div class="section-tag">
            <span class="prompt">$</span>
            <span>cat</span>
            <span class="path">./<?php echo esc_html( $slug_display ); ?>.md</span>
          </div>

          <h1 class="section-title"><?php the_title(); ?></h1>

          <div class="entry-meta">
            <time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>">
              <?php echo esc_html( get_the_date() ); ?>
            </time>
            <span class="dot-sep">·</span>
            <span><?php the_author(); ?></span>
            <?php if ( has_category() ) : ?>
              <span class="dot-sep">·</span>
              <span class="entry-cats"><?php the_category( ', ' ); ?></span>
            <?php endif; ?>
            <?php echo ai_render_meta_extra(); ?>
          </div>
        </div>

        <div class="entry-content">
          <?php the_content(); ?>
          <?php
          wp_link_pages( [
              'before' => '<nav class="page-links"><span class="prompt">$</span> pages ',
              'after'  => '</nav>',
          ] );
          ?>
        </div>
      </article>

      <nav class="post-nav">
        <div class="post-nav-prev">
          <?php previous_post_link( '<span class="post-nav-label">← 上一篇</span>%link' ); ?>
        </div>
        <div class="post-nav-next">
          <?php next_post_link( '<span class="post-nav-label">下一篇 →</span>%link' ); ?>
        </div>
      </nav>

      <div class="post-share" aria-label="分享这篇文章"></div>

      <?php
      if ( comments_open() || get_comments_number() ) {
          comments_template();
      }
      ?>
    <?php endwhile; ?>
  </main>

  <?php if ( ai_layout_has_sidebar() && ai_get_layout() === 'sidebar-right' ) get_sidebar(); ?>
</div>

<?php get_footer(); ?>