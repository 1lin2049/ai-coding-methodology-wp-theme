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

<?php
/* 全站导航（primary 菜单，兜底为站内混合路由：主页锚点 + 真实页面） */
$nav_links = '';
if ( has_nav_menu( 'primary' ) ) {
    $nav_links = wp_nav_menu( [
        'theme_location' => 'primary',
        'container'      => false,
        'menu_class'     => 'navlinks-menu',
        'depth'          => 1,
        'fallback_cb'    => false,
        'echo'           => false,
    ] );
} else {
    $nav_items = [
        [ 'terminal', '首页', home_url( '/' ) . '#top' ],
        [ 'layers',   '方法', home_url( '/' ) . '#framework' ],
        [ 'book',     '试读', home_url( '/preview/' ) ],
        [ 'key',      '购买', home_url( '/purchase/' ) ],
        [ 'user',     '关于', home_url( '/about/' ) ],
    ];
    foreach ( $nav_items as $it ) {
        $nav_links .= '<a href="' . esc_url( $it[2] ) . '">' . ai_icon( $it[0], 13 ) . '<span>' . esc_html( $it[1] ) . '</span></a>';
    }
}
?>

<nav class="nav" id="nav">
  <div class="navin">
    <a class="logo" href="<?php echo esc_url( home_url( '/' ) ); ?>#top">
      <span class="logo-prompt">$</span>
      <span class="logo-path">~/ai-coding-methodology</span>
      <span class="logo-cursor"></span>
    </a>

    <div class="navlinks"><?php echo $nav_links; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>

    <div class="nav-actions">
      <button class="theme-toggle" id="themeToggle" aria-label="<?php esc_attr_e( '切换主题', 'ai-coding' ); ?>">
        <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
        <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
      </button>
      <a class="nav-cta" href="<?php echo esc_url( home_url( '/#pricing' ) ); ?>"><span class="prompt-inline">$</span> install</a>
      <button class="nav-burger" id="navBurger" type="button" aria-expanded="false" aria-controls="navPanel" aria-label="<?php esc_attr_e( '打开菜单', 'ai-coding' ); ?>">
        <span class="nav-burger-bar" aria-hidden="true"></span>
        <span class="nav-burger-bar" aria-hidden="true"></span>
        <span class="nav-burger-bar" aria-hidden="true"></span>
      </button>
    </div>
  </div>
</nav>

<aside class="nav-panel" id="navPanel" aria-hidden="true">
  <div class="nav-panel-inner">
    <nav aria-label="<?php esc_attr_e( '站点导航', 'ai-coding' ); ?>"><?php echo $nav_links; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></nav>
    <div class="nav-panel-foot">
      <a class="nav-cta" href="<?php echo esc_url( home_url( '/#pricing' ) ); ?>"><span class="prompt-inline">$</span> install</a>
    </div>
  </div>
</aside>

<?php get_template_part( 'parts/side-nav' ); ?>
<?php get_template_part( 'parts/float-actions' ); ?>