<footer>
  <div class="foot-inner">
    <div class="foot-top">
      <div>
        <a class="logo" href="<?php echo esc_url( home_url( '/' ) ); ?>#top">
          <span class="logo-prompt">$</span>
          <span class="logo-path">~/ai-coding-methodology</span>
        </a>
        <p class="foot-desc">
          AI 原生软件工程实践者<br>
          从代码生成到约束工程：构建 AI 原生软件系统<br>
          李咏燊 著
        </p>
        <div class="foot-meta">
          <span class="foot-meta-item">
            <span class="foot-meta-key">version</span>
            <span class="foot-meta-val">v2026.09</span>
          </span>
          <span class="foot-meta-item">
            <span class="foot-meta-key">license</span>
            <span class="foot-meta-val">all rights reserved</span>
          </span>
        </div>
      </div>

      <?php
      $foot_menus = [
          'footer-content'  => [ '// content',  [ '问题' => home_url( '/' ) . '#problem', '方法论' => home_url( '/' ) . '#framework', '工程结构' => home_url( '/' ) . '#project', '工程资产' => home_url( '/' ) . '#assets' ] ],
          'footer-purchase' => [ '// purchase', [ '三种版本' => home_url( '/' ) . '#pricing', '免费试读' => home_url( '/preview/' ), '购买' => home_url( '/purchase/' ), '常见问题' => home_url( '/' ) . '#faq' ] ],
          'footer-other'    => [ '// other',    [ '作者' => home_url( '/about/' ), '联系' => home_url( '/contact/' ), '实践数据' => home_url( '/' ) . '#data', '返回顶部' => '#top' ] ],
      ];
      foreach ( $foot_menus as $loc => $data ) :
          list( $heading, $fallback ) = $data;
      ?>
        <div class="foot-col">
          <h5><?php echo esc_html( $heading ); ?></h5>
          <?php if ( has_nav_menu( $loc ) ) : ?>
            <?php wp_nav_menu( [
                'theme_location' => $loc,
                'container'      => false,
                'depth'          => 1,
                'fallback_cb'    => false,
            ] ); ?>
          <?php else : ?>
            <?php foreach ( $fallback as $label => $href ) : ?>
              <a href="<?php echo esc_attr( $href ); ?>"><?php echo esc_html( $label ); ?></a>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>

    <nav class="foot-links" aria-label="<?php esc_attr_e( '底部快捷导航', 'ai-coding' ); ?>">
      <a href="<?php echo esc_url( home_url( '/' ) . '#framework' ); ?>">方法</a>
      <a href="<?php echo esc_url( home_url( '/preview/' ) ); ?>">试读</a>
      <a href="<?php echo esc_url( home_url( '/purchase/' ) ); ?>">购买</a>
      <a href="<?php echo esc_url( home_url( '/about/' ) ); ?>">关于</a>
      <a href="#top">返回顶部</a>
    </nav>

    <div class="foot-bot">
      <span>© <?php echo esc_html( wp_date( 'Y' ) ); ?> 李咏燊</span>
      <span class="foot-sep">·</span>
      <span class="foot-quote">代码是结果，约束才是生产系统</span>
    </div>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>