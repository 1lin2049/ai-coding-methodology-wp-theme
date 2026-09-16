<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$chapters = get_posts( [
	'post_type'      => 'chapter',
	'posts_per_page' => -1,
	'orderby'        => 'menu_order',
	'order'          => 'ASC',
] );

if ( empty( $chapters ) ) return;

$current_id    = get_the_ID();
$current_index = array_search( $current_id, wp_list_pluck( $chapters, 'ID' ) );
if ( $current_index === false ) $current_index = -1;
?>

<div class="widget">
	<h4 class="widget-title">章节目录</h4>
	<ul>
		<?php foreach ( $chapters as $i => $c ) :
			$num       = get_post_meta( $c->ID, 'chapter_number', true );
			$is_active = ( $c->ID === $current_id );
			$url       = get_permalink( $c );
		?>
		<li>
			<a href="<?php echo esc_url( $url ); ?>"<?php if ( $is_active ) echo ' aria-current="true"'; ?>>
				<span class="ch-num"><?php echo esc_html( $num ); ?></span>
				<span class="ch-title"><?php echo esc_html( $c->post_title ); ?></span>
			</a>
		</li>
		<?php endforeach; ?>
	</ul>
</div>
