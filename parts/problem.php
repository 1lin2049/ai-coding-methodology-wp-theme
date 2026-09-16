<!-- 01 PROBLEM -->
<section class="section" id="problem">
  <div class="wrap">
    <div class="section-head reveal">
      <div class="section-tag"><span class="prompt">$</span><span>cat</span><span class="path">./01-problem.md</span></div>
      <h2 class="section-title"><?php echo wp_kses_post( ai_opt( 'problem_title' ) ); ?></h2>
      <p class="section-desc"><?php echo esc_html( ai_opt( 'problem_desc' ) ); ?></p>
    </div>

    <div class="log reveal">
      <?php foreach ( ai_opt_lines( 'problem_log' ) as $row ) :
          $time = $row[0] ?? '';
          $msg  = $row[1] ?? '';
          ?>
        <div class="log-line">
          <span class="log-time"><?php echo esc_html( $time ); ?></span>
          <span class="log-msg"><?php echo wp_kses_post( $msg ); ?></span>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="divider-label reveal"><span>// common symptoms</span></div>

    <div class="symptom-grid">
      <?php foreach ( ai_opt_lines( 'problem_symptoms' ) as $row ) :
          $num  = $row[0] ?? '';
          $text = $row[1] ?? '';
          ?>
        <div class="symptom reveal">
          <span class="snum"><?php echo esc_html( $num ); ?></span>
          <span class="stext"><?php echo esc_html( $text ); ?></span>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>