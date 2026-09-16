<!-- 10 FAQ -->
<section class="section section-alt" id="faq">
  <div class="wrap">
    <div class="section-head reveal">
      <div class="section-tag"><span class="prompt">$</span><span>man</span><span class="path">ai-coding-methodology</span></div>
      <h2 class="section-title"><?php echo esc_html( ai_opt( 'faq_title' ) ); ?></h2>
      <p class="section-desc"><?php echo esc_html( ai_opt( 'faq_desc' ) ); ?></p>
    </div>

    <?php
    /**
     * 把 FAQ 答案文本转成 HTML：
     *   - "- " 开头行 → <li>（包在 <ul class="faq-list"> 里）
     *   - 其他行 → <p>
     *   - [text](url) → <a class="faq-link">
     */
    if ( ! function_exists( 'ai_faq_answer_html' ) ) {
        function ai_faq_answer_html( $raw ) {
            $raw_lines = preg_split( '/\r\n|\r|\n/', trim( $raw ) );
            $html      = '';
            $ul_open   = false;

            foreach ( $raw_lines as $line ) {
                $line = trim( $line );
                if ( $line === '' ) continue;

                if ( preg_match( '/^-\s*(.+)$/', $line, $m ) ) {
                    if ( ! $ul_open ) { $html .= '<ul class="faq-list">'; $ul_open = true; }
                    $html .= '<li>' . esc_html( $m[1] ) . '</li>';
                } else {
                    if ( $ul_open ) { $html .= '</ul>'; $ul_open = false; }
                    $html .= '<p>' . esc_html( $line ) . '</p>';
                }
            }
            if ( $ul_open ) $html .= '</ul>';

            /* markdown 链接 → <a> */
            $html = preg_replace_callback(
                '/\[(.+?)\]\((.+?)\)/',
                function ( $m ) {
                    return '<a href="' . esc_url( $m[2] ) . '" class="faq-link">' . $m[1] . '</a>';
                },
                $html
            );

            return $html;
        }
    }
    ?>

    <div class="faq reveal">
      <?php foreach ( ai_opt_blocks( 'faq_items' ) as $i => $block ) :
          $head_id   = $block['head'][0] ?? '';
          $head_meta = $block['head'][1] ?? '';
          $q         = $block['q'];
          $a_html    = ai_faq_answer_html( $block['a'] );
          ?>
        <details<?php echo $i === 0 ? ' open' : ''; ?>>
          <summary>
            <span class="faq-id"><?php echo esc_html( $head_id ); ?></span>
            <span class="faq-q"><?php echo esc_html( $q ); ?></span>
            <span class="faq-meta"><?php echo esc_html( $head_meta ); ?></span>
            <span class="faq-icon">
              <svg viewBox="0 0 12 12" aria-hidden="true">
                <path d="M2 6h8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                <path d="M6 2v8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" class="faq-icon-v"/>
              </svg>
            </span>
          </summary>
          <div class="faq-a">
            <span class="faq-a-prefix">A:</span>
            <div class="faq-a-body"><?php echo wp_kses_post( $a_html ); ?></div>
          </div>
        </details>
      <?php endforeach; ?>
    </div>
  </div>
</section>