<!-- FINAL -->
<section class="final">
  <div class="wrap">
    <div class="final-terminal">

      <div class="final-line reveal">
        <span class="final-prompt">$</span>
        <span class="final-cmd"><?php echo esc_html( ai_opt( 'final_exit_cmd' ) ); ?></span>
      </div>

      <div class="final-output reveal">
        <?php foreach ( ai_opt_lines( 'final_outputs' ) as $row ) :
            $line = $row[0] ?? '';
            ?>
          <div class="final-line-comment">
            <span class="final-hash">#</span>
            <span><?php echo esc_html( $line ); ?></span>
          </div>
        <?php endforeach; ?>
      </div>

      <h2 class="final-title reveal">
        <?php echo wp_kses_post( ai_opt( 'final_title' ) ); ?><span class="final-cursor"></span>
      </h2>

      <div class="final-actions reveal">
        <a class="final-btn" href="<?php echo esc_url( ai_opt( 'final_cta_primary_url' ) ); ?>">
          <span class="final-prompt">$</span>
          <span><?php echo esc_html( ai_opt( 'final_cta_primary_text' ) ); ?></span>
          <span class="final-arrow">→</span>
        </a>
        <a class="final-btn-ghost" href="<?php echo esc_url( ai_opt( 'final_cta_ghost_url' ) ); ?>">
          <span class="final-prompt">$</span>
          <span><?php echo esc_html( ai_opt( 'final_cta_ghost_text' ) ); ?></span>
        </a>
      </div>

      <div class="final-status reveal">
        <span class="status-dot"></span>
        <span><?php echo esc_html( ai_opt( 'final_status_1' ) ); ?></span>
        <span class="status-sep">·</span>
        <span><?php echo esc_html( ai_opt( 'final_status_2' ) ); ?></span>
      </div>

    </div>
  </div>
</section>