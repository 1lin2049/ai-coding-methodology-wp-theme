<!-- 07 AUTHOR -->
<section class="section" id="author">
  <div class="wrap">
    <div class="section-head reveal">
      <div class="section-tag"><span class="prompt">$</span><span>whoami</span></div>
      <h2 class="section-title"><?php echo esc_html( ai_opt( 'author_title' ) ); ?></h2>
    </div>

    <div class="author-wrap reveal">
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

    <div class="author-quote reveal">
      <p><?php echo wp_kses_post( ai_opt( 'author_quote' ) ); ?></p>
    </div>
  </div>
</section>