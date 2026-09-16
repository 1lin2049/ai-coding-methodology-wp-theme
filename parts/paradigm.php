<!-- 02 PARADIGM -->
<section class="section section-alt" id="paradigm">
  <div class="wrap">
    <div class="section-head reveal">
      <div class="section-tag"><span class="prompt">$</span><span>diff</span><span class="path">before.txt after.txt</span></div>
      <h2 class="section-title"><?php echo wp_kses_post( ai_opt( 'paradigm_title' ) ); ?></h2>
    </div>

    <div class="diff-block reveal">
      <div class="diff-side diff-before">
        <div class="diff-label"><?php echo esc_html( ai_opt( 'paradigm_before_label' ) ); ?></div>
        <div class="diff-flow">
          <span><?php echo esc_html( ai_opt( 'paradigm_before_flow' ) ); ?></span>
        </div>
        <div class="diff-note"><?php echo esc_html( ai_opt( 'paradigm_before_note' ) ); ?></div>
      </div>
      <div class="diff-side diff-after">
        <div class="diff-label"><?php echo esc_html( ai_opt( 'paradigm_after_label' ) ); ?></div>
        <div class="diff-flow">
          <span class="accent"><?php echo esc_html( ai_opt( 'paradigm_after_flow' ) ); ?></span>
        </div>
        <div class="diff-note"><?php echo esc_html( ai_opt( 'paradigm_after_note' ) ); ?></div>
      </div>
    </div>

    <p class="quote-large reveal"><?php echo wp_kses_post( ai_opt( 'paradigm_quote' ) ); ?></p>
  </div>
</section>