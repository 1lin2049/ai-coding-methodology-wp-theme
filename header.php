<!doctype html>
<html <?php language_attributes(); ?> data-theme="dark">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="theme-color" content="#08080a">
<script>
/* 防闪烁：HTML 解析前锁定主题 */
(function(){
  try {
    var t = localStorage.getItem('theme');
    if (t !== 'light' && t !== 'dark') {
      t = (window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches)
            ? 'light' : 'dark';
    }
    document.documentElement.setAttribute('data-theme', t);
  } catch (e) {}
})();
</script>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div class="progress" id="progress"></div>

<nav class="nav" id="nav">
  <div class="navin">
    <a class="logo" href="<?php echo esc_url( home_url( '/' ) ); ?>#top">
      <span class="logo-prompt">$</span>
      <span class="logo-path">~/ai-coding-methodology</span>
      <span class="logo-cursor"></span>
    </a>

    <?php if ( has_nav_menu( 'primary' ) ) : ?>
      <?php wp_nav_menu( [
          'theme_location'  => 'primary',
          'container'       => 'div',
          'container_class' => 'navlinks',
          'depth'           => 1,
          'fallback_cb'     => false,
      ] ); ?>
    <?php else : ?>
      <div class="navlinks">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>#problem"><?php echo ai_icon( 'file-text', 13 ); ?><span>问题</span></a>
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>#paradigm"><?php echo ai_icon( 'git-branch', 13 ); ?><span>范式</span></a>
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>#framework"><?php echo ai_icon( 'layers', 13 ); ?><span>方法</span></a>
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>#assets"><?php echo ai_icon( 'package', 13 ); ?><span>资产</span></a>
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>#data"><?php echo ai_icon( 'git-commit', 13 ); ?><span>数据</span></a>
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>#pricing"><?php echo ai_icon( 'key', 13 ); ?><span>版本</span></a>
        <a href="<?php echo esc_url( home_url( '/preview/' ) ); ?>"><?php echo ai_icon( 'book', 13 ); ?><span>试读</span></a>
      </div>
    <?php endif; ?>

    <div class="nav-actions">
      <button class="theme-toggle" id="themeToggle" aria-label="<?php esc_attr_e( '切换主题', 'ai-coding' ); ?>">
        <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
        <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
      </button>
      <a class="nav-cta" href="<?php echo esc_url( home_url( '/#pricing' ) ); ?>"><span class="prompt-inline">$</span> install</a>
    </div>
  </div>
</nav>

<?php get_template_part( 'parts/side-nav' ); ?>
<?php get_template_part( 'parts/float-actions' ); ?>