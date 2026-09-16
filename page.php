<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>

<div class="layout-shell">
  <?php if ( ai_layout_has_sidebar() && ai_get_layout() === 'sidebar-left' ) get_sidebar(); ?>

  <main id="top" class="layout-main">
    <?php while ( have_posts() ) : the_post(); ?>
      <article <?php post_class( 'section' ); ?> id="post-<?php the_ID(); ?>">
        <div class="section-head">
          <div class="section-tag">
            <span class="prompt">$</span>
            <span>cat</span>
            <span class="path">./<?php echo esc_html( get_post_field( 'post_name' ) ); ?>.md</span>
          </div>
          <h1 class="section-title"><?php the_title(); ?></h1>
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