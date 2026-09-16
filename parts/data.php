<!-- 06 DATA -->
<section class="section section-alt" id="data">
  <div class="wrap">
    <div class="section-head reveal">
      <div class="section-tag"><span class="prompt">$</span><span>git</span><span class="path">shortlog -sn --since=2025.08</span></div>
      <h2 class="section-title"><?php echo wp_kses_post( ai_opt( 'data_title' ) ); ?></h2>
      <p class="section-desc"><?php echo esc_html( ai_opt( 'data_desc' ) ); ?></p>
    </div>

    <div class="git-stats reveal">
      <?php foreach ( ai_opt_lines( 'data_stats' ) as $row ) :
          $num   = $row[0] ?? '';
          $label = $row[1] ?? '';
          ?>
        <div class="gs-row">
          <span class="gs-num" data-count="<?php echo esc_attr( $num ); ?>">0</span>
          <span class="gs-label"><?php echo esc_html( $label ); ?></span>
        </div>
      <?php endforeach; ?>
    </div>

    <p class="data-note reveal">
      <span class="note-tag"><?php echo esc_html( ai_opt( 'data_note_tag' ) ); ?></span>
      <?php echo esc_html( ai_opt( 'data_note' ) ); ?>
    </p>
  </div>
</section>