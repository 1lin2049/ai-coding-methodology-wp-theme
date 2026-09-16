<!-- 09 PREVIEW -->
<section class="section" id="preview">
  <div class="wrap">
    <div class="section-head reveal">
      <div class="section-tag"><span class="prompt">$</span><span>open</span><span class="path">./preview/</span></div>
      <h2 class="section-title"><?php echo wp_kses_post( ai_opt( 'preview_title' ) ); ?></h2>
      <p class="section-desc"><?php echo esc_html( ai_opt( 'preview_desc' ) ); ?></p>
    </div>

    <?php
    $chapters = get_posts( [
        'post_type'      => 'chapter',
        'posts_per_page' => -1,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    ] );
    ?>

    <div class="preview-list reveal">
      <?php if ( ! empty( $chapters ) ) : ?>
        <?php foreach ( $chapters as $c ) :
            $num     = get_post_meta( $c->ID, 'chapter_number', true );
            $words   = get_post_meta( $c->ID, 'chapter_words',  true );
            $minutes = ai_get_reading_minutes( $c->ID );
        ?>
          <a class="prev-row" href="<?php echo esc_url( get_permalink( $c ) ); ?>">
            <span class="prev-id"><?php echo esc_html( $num ); ?></span>
            <span class="prev-title"><?php echo esc_html( $c->post_title ); ?></span>
            <span class="prev-meta"><?php echo esc_html( $words ); ?> · <?php echo esc_html( $minutes ); ?> 分钟</span>
            <span class="prev-arrow">→</span>
          </a>
        <?php endforeach; ?>
      <?php else : ?>
        <p style="padding:24px;color:var(--tx-3);font-family:var(--mono);font-size:13px">
          // 尚未导入章节 · 请在后台「试读章节 → 新增章节」中添加
        </p>
      <?php endif; ?>
    </div>

    <div class="preview-cta reveal">
      <a class="btn btn-primary" href="<?php echo esc_url( home_url( ai_opt( 'preview_cta_url' ) ) ); ?>">
        <span class="prompt">$</span>
        <span><?php echo esc_html( ai_opt( 'preview_cta_text' ) ); ?></span>
        <span class="btn-arrow">→</span>
      </a>
      <div class="preview-note">
        <span><?php echo esc_html( ai_opt( 'preview_note_1' ) ); ?></span>
        <span><?php echo esc_html( ai_opt( 'preview_note_2' ) ); ?></span>
        <span><?php echo esc_html( ai_opt( 'preview_note_3' ) ); ?></span>
      </div>
    </div>
  </div>
</section>