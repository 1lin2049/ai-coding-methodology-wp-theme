<?php
/**
 * Template Name: 关于页
 */
if ( ! defined( 'ABSPATH' ) ) exit;
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
            <span>whoami</span>
          </div>
          <h1 class="section-title"><?php the_title(); ?></h1>
        </div>

        <div class="author-wrap">
          <div class="author-card">
            <div class="author-avatar"><?php echo esc_html( ai_opt( 'author_avatar_char' ) ); ?></div>
            <div class="author-info">
              <div class="author-name"><?php echo esc_html( ai_opt( 'author_name' ) ); ?></div>
              <div class="author-role"><?php echo esc_html( ai_opt( 'author_role' ) ); ?></div>
            </div>
          </div>

          <div class="author-log">
            <?php foreach ( ai_opt_lines( 'author_timeline' ) as $row ) :
                $time   = $row[0] ?? '';
                $msg    = $row[1] ?? '';
                $accent = ( ( $row[2] ?? '' ) === 'accent' ) ? ' accent' : '';
                ?>
              <div class="al-line">
                <span class="al-time"><?php echo esc_html( $time ); ?></span>
                <span class="al-msg<?php echo esc_attr( $accent ); ?>"><?php echo esc_html( $msg ); ?></span>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <?php if ( trim( get_the_content() ) !== '' ) : ?>
          <div class="entry-content">
            <?php the_content(); ?>
          </div>
        <?php else : ?>
          <div class="author-quote">
            <p><?php echo wp_kses_post( ai_opt( 'author_quote' ) ); ?></p>
          </div>
        <?php endif; ?>
      </article>
    <?php endwhile; ?>
  </main>

  <?php if ( ai_layout_has_sidebar() && ai_get_layout() === 'sidebar-right' ) get_sidebar(); ?>
</div>

<?php get_footer(); ?>