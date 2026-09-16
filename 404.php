<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

/* 请求 URI 安全展示 */
$req = isset( $_SERVER['REQUEST_URI'] )
    ? esc_html( wp_unslash( $_SERVER['REQUEST_URI'] ) )
    : '/';
?>

<div class="layout-shell">
  <main id="top" class="layout-main">
    <article class="section">
      <div class="section-head">
        <div class="section-tag">
          <span class="prompt">$</span>
          <span>cat</span>
          <span class="path">./404.md</span>
        </div>
        <h1 class="section-title">
          找不到这个<em>页面</em>
        </h1>
        <p class="section-desc">请求的资源不存在，或已被移动。</p>
      </div>

      <div class="entry-content">
        <div class="error-terminal">
          <div class="error-line">
            <span class="final-prompt">$</span>
            <span class="final-cmd">curl</span>
            <span class="final-arg"><?php echo $req; ?></span>
          </div>
          <div class="error-output">
            <span class="err">404</span> Not Found
          </div>
        </div>

        <div class="error-actions">
          <a class="btn btn-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>">
            <span class="prompt">$</span>
            <span>cd ~/</span>
            <span class="btn-arrow">→</span>
          </a>
          <a class="btn btn-ghost" href="<?php echo esc_url( home_url( '/preview/' ) ); ?>">
            <span class="prompt">$</span>
            <span>open ./preview/</span>
          </a>
        </div>

        <div class="error-search">
          <p class="error-search-label">// 或试试搜索</p>
          <?php get_search_form(); ?>
        </div>
      </div>
    </article>
  </main>
</div>

<?php get_footer(); ?>