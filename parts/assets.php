<!-- 05 ASSETS -->
<section class="section" id="assets">
  <div class="wrap">
    <div class="section-head reveal">
      <div class="section-tag"><span class="prompt">$</span><span>ls</span><span class="path">./appendix/</span></div>
      <h2 class="section-title"><?php echo wp_kses_post( ai_opt( 'assets_title' ) ); ?></h2>
    </div>

    <div class="asset-table reveal">
      <?php foreach ( ai_opt_groups( 'assets_groups' ) as $group ) :
          $label = $group['head'][0] ?? '';
          ?>
        <div class="asset-group">
          <div class="asset-group-label"><?php echo esc_html( $label ); ?></div>
          <?php foreach ( $group['items'] as $row ) :
              $id   = $row[0] ?? '';
              $name = $row[1] ?? '';
              $desc = $row[2] ?? '';
              ?>
            <div class="at-row">
              <span class="at-id"><?php echo esc_html( $id ); ?></span>
              <span class="at-name"><?php echo esc_html( $name ); ?></span>
              <span class="at-desc"><?php echo esc_html( $desc ); ?></span>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>