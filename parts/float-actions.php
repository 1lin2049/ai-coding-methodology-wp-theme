<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$items = ai_opt_lines( 'float_actions' );
if ( empty( $items ) ) return;

/* ── 按 scope 过滤 ── */
$is_home     = is_front_page();
$is_singular = is_singular();
$is_archive  = is_archive() || is_home() && ! is_front_page();

$visible_items = [];
foreach ( $items as $item ) {
    list( $type, $icon, $label, $url, $target, $scope ) = array_pad( $item, 6, '' );

    $type  = $type ?: 'link';
    $scope = $scope ?: 'all';

    $visible = false;
    switch ( $scope ) {
        case 'home':
            $visible = $is_home;
            break;
        case 'singular':
            $visible = $is_singular;
            break;
        case 'archive':
            $visible = $is_archive;
            break;
        case 'all':
        default:
            $visible = true;
    }

    if ( ! $visible ) continue;

    $visible_items[] = [ $type, $icon ?: 'arrow-right', $label, $url, $target ];
}

if ( empty( $visible_items ) ) return;
?>
<div class="float-actions" id="floatActions">
  <?php foreach ( $visible_items as $item ) :
      list( $type, $icon, $label, $url, $target ) = $item;
      ?>
      <?php if ( in_array( $type, [ 'top', 'share' ], true ) ) : ?>
        <button type="button" class="float-action" data-action="<?php echo esc_attr( $type ); ?>">
          <?php echo ai_icon( $icon, 14 ); ?>
          <span class="float-action-label"><?php echo esc_html( $label ); ?></span>
        </button>
      <?php else : ?>
        <a class="float-action" href="<?php echo esc_url( $url ); ?>"<?php echo $target === '_blank' ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
          <?php echo ai_icon( $icon, 14 ); ?>
          <span class="float-action-label"><?php echo esc_html( $label ); ?></span>
        </a>
      <?php endif; ?>
  <?php endforeach; ?>
</div>