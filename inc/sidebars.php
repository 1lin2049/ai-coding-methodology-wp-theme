<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$layout = ai_get_layout();
if ( ! ai_layout_has_sidebar( $layout ) ) return;

$sidebar_id = ai_layout_sidebar_id( $layout );
if ( ! is_active_sidebar( $sidebar_id ) ) {
    // 空位占位（开发时看得见）
    if ( current_user_can( 'manage_options' ) ) {
        ?>
        <aside class="sidebar sidebar-empty" role="complementary">
          <div class="widget">
            <h4 class="widget-title"><?php echo esc_html( $sidebar_id === 'sidebar-left' ? '左侧栏' : '右侧栏' ); ?></h4>
            <p style="font-size:13px;color:var(--tx-3);margin:0">
              尚未添加小工具。前往 <a href="<?php echo esc_url( admin_url( 'widgets.php' ) ); ?>">外观 → 小工具</a> 添加。
            </p>
          </div>
        </aside>
        <?php
    }
    return;
}
?>
<aside class="sidebar" role="complementary">
  <div class="sidebar-inner">
    <?php dynamic_sidebar( $sidebar_id ); ?>
  </div>
</aside>