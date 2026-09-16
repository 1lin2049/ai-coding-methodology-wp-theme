<?php
/**
 * Template Name: 购买页
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
            <span>cat</span>
            <span class="path">./package.json</span>
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

        <div class="pricing-cmds">
          <?php foreach ( ai_opt_groups( 'pricing_packages' ) as $group ) :
              $head     = $group['head'];
              $name     = $head[0] ?? '';
              $price    = $head[1] ?? '';
              $desc     = $head[2] ?? '';
              $is_rec   = ( ( $head[3] ?? '0' ) === '1' );
              $card_cls = 'pkg-card' . ( $is_rec ? ' pkg-rec' : '' );
              $btn_cls  = 'pkg-btn'  . ( $is_rec ? ' pkg-btn-primary' : '' );
              ?>
            <div class="<?php echo esc_attr( $card_cls ); ?>">
              <?php if ( $is_rec ) : ?>
                <div class="pkg-badge">RECOMMENDED</div>
              <?php endif; ?>

              <div class="pkg-head">
                <span class="pkg-name"><?php echo esc_html( $name ); ?></span>
                <span class="pkg-price"><?php echo esc_html( $price ); ?></span>
              </div>
              <div class="pkg-desc"><?php echo esc_html( $desc ); ?></div>

              <div class="pkg-body">
                <?php foreach ( $group['items'] as $row ) :
                    $key = $row[0] ?? '';
                    $val = $row[1] ?? '';
                    $ok  = ( ( $row[2] ?? 'ok' ) === 'ok' );
                    ?>
                  <div class="pkg-line">
                    <span class="pkg-key"><?php echo esc_html( $key ); ?></span>
                    <span class="pkg-val <?php echo $ok ? 'ok' : 'no'; ?>"><?php echo $ok ? esc_html( $val ) : '—'; ?></span>
                  </div>
                <?php endforeach; ?>
              </div>

              <a class="<?php echo esc_attr( $btn_cls ); ?>" href="#buy">
                <span class="prompt">$</span> install <?php echo esc_html( $name ); ?><?php if ( $is_rec ) : ?> <span class="caret">_</span><?php endif; ?>
              </a>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="buy-note" id="buy">
          <span class="prompt">$</span>
          <span class="buy-text"><?php echo esc_html( ai_opt( 'pricing_buy_text' ) ); ?></span>
          <a class="buy-link" href="<?php echo esc_url( ai_opt( 'pricing_buy_link_url' ) ); ?>"><?php echo esc_html( ai_opt( 'pricing_buy_link_text' ) ); ?></a>
        </div>
      </article>
    <?php endwhile; ?>
  </main>

  <?php if ( ai_layout_has_sidebar() && ai_get_layout() === 'sidebar-right' ) get_sidebar(); ?>
</div>

<?php get_footer(); ?>