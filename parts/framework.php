<!-- 03 FRAMEWORK -->
<section class="section" id="framework">
  <div class="wrap">
    <div class="section-head reveal">
      <div class="section-tag"><span class="prompt">$</span><span>tree</span><span class="path">framework/ -L 1</span></div>
      <h2 class="section-title"><?php echo wp_kses_post( ai_opt( 'framework_title' ) ); ?></h2>
    </div>

    <div class="framework-table reveal">
      <div class="ft-head">
        <span class="ft-h">ID</span>
        <span class="ft-h">NAME</span>
        <span class="ft-h">DESC</span>
      </div>

      <?php
      $items = ai_opt_lines( 'framework_items' );
      foreach ( $items as $i => $row ) :
          /* 在第 4 项之后插入分隔条 */
          if ( $i === 4 ) :
              ?>
              <div class="framework-divider">
                <span class="fw-label on"><?php echo esc_html( ai_opt( 'framework_divider_on' ) ); ?></span>
                <span class="fw-hr"></span>
                <span class="fw-label"><?php echo esc_html( ai_opt( 'framework_divider_label' ) ); ?></span>
              </div>
              <?php
          endif;

          $id   = $row[0] ?? '';
          $name = $row[1] ?? '';
          $desc = $row[2] ?? '';
          ?>
          <div class="ft-row">
            <span class="ft-id"><?php echo esc_html( $id ); ?></span>
            <span class="ft-name"><?php echo esc_html( $name ); ?></span>
            <span class="ft-desc"><?php echo esc_html( $desc ); ?></span>
          </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>