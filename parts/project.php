<!-- 04 PROJECT -->
<section class="section section-alt" id="project">
  <div class="wrap">
    <div class="section-head reveal">
      <div class="section-tag"><span class="prompt">$</span><span>ls</span><span class="path">-la ./project-root</span></div>
      <h2 class="section-title"><?php echo wp_kses_post( ai_opt( 'project_title' ) ); ?></h2>
    </div>

    <div class="ls-block reveal">
      <div class="ls-head">
        <span class="ls-h">MODE</span>
        <span class="ls-h">NAME</span>
        <span class="ls-h">SIZE</span>
        <span class="ls-h">TAG</span>
      </div>
      <?php foreach ( ai_opt_lines( 'project_files' ) as $row ) :
          $mode = $row[0] ?? '';
          $name = $row[1] ?? '';
          $size = $row[2] ?? '';
          $tag  = $row[3] ?? '';
          $cls  = $row[4] ?? 'plain';
          $cls  = ( $cls === 'plain' || $cls === '' ) ? '' : ' ' . $cls;
          ?>
        <div class="ls-row">
          <span class="ls-mode"><?php echo esc_html( $mode ); ?></span>
          <span class="ls-name"><?php echo esc_html( $name ); ?></span>
          <span class="ls-size"><?php echo esc_html( $size ); ?></span>
          <span class="ls-tag<?php echo esc_attr( $cls ); ?>"><?php echo esc_html( $tag ); ?></span>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>