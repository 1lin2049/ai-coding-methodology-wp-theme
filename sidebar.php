<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! is_active_sidebar( 'sidebar-main' ) ) return;
?>
<aside class="sidebar" role="complementary">
  <div class="sidebar-inner">
    <?php dynamic_sidebar( 'sidebar-main' ); ?>
  </div>
</aside>