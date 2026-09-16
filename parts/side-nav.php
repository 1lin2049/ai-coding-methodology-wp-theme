<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/* 只在首页和单页/文章上启用 */
if ( ! is_front_page() && ! is_singular( [ 'post', 'page' ] ) ) {
    return;
}

$is_front = is_front_page();
$mode     = $is_front ? 'anchors' : 'toc';

$anchors = [];
if ( $is_front ) {
    $anchors = [
        [ '#top',       '~/' ],
        [ '#problem',   'problem/' ],
        [ '#paradigm',  'paradigm/' ],
        [ '#framework', 'framework/' ],
        [ '#project',   'project/' ],
        [ '#assets',    'assets/' ],
        [ '#data',      'data/' ],
        [ '#author',    'author/' ],
        [ '#pricing',   'pricing/' ],
        [ '#preview',   'preview/' ],
        [ '#faq',       'faq/' ],
    ];
}
?>
<nav class="side-nav" id="sideNav" data-mode="<?php echo esc_attr( $mode ); ?>" aria-label="页面导航">
  <?php foreach ( $anchors as $a ) : ?>
    <a class="side-dot" href="<?php echo esc_attr( $a[0] ); ?>" data-label="<?php echo esc_attr( $a[1] ); ?>"><span></span></a>
  <?php endforeach; ?>
</nav>