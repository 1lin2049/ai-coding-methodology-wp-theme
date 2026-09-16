<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$siblings = get_posts( [
	'post_type'      => 'chapter',
	'posts_per_page' => -1,
	'orderby'        => 'menu_order',
	'order'          => 'ASC',
	'fields'         => 'ids',
] );

$current_id = get_the_ID();
$idx  = array_search( $current_id, $siblings );
$prev = ( $idx !== false && $idx > 0 ) ? get_post( $siblings[ $idx - 1 ] ) : null;
$next = ( $idx !== false && $idx < count( $siblings ) - 1 ) ? get_post( $siblings[ $idx + 1 ] ) : null;
$toc  = home_url( '/preview/' );

if ( ! $prev && ! $next ) return;
?>

<div class="widget">
	<h4 class="widget-title">导航</h4>
	<div class="wp-chapter-nav">
		<?php if ( $prev ) : ?>
		<a class="wp-chapter-nav-item" href="<?php echo esc_url( get_permalink( $prev ) ); ?>">
			<span class="wp-chapter-nav-dir">← 上一章</span>
			<span class="wp-chapter-nav-title"><?php echo esc_html( $prev->post_title ); ?></span>
		</a>
		<?php else : ?>
		<a class="wp-chapter-nav-item" href="<?php echo esc_url( $toc ); ?>">
			<span class="wp-chapter-nav-dir">←</span>
			<span class="wp-chapter-nav-title">返回目录</span>
		</a>
		<?php endif; ?>

		<?php if ( $next ) : ?>
		<a class="wp-chapter-nav-item wp-chapter-nav-next" href="<?php echo esc_url( get_permalink( $next ) ); ?>">
			<span class="wp-chapter-nav-dir">下一章 →</span>
			<span class="wp-chapter-nav-title"><?php echo esc_html( $next->post_title ); ?></span>
		</a>
		<?php else : ?>
		<a class="wp-chapter-nav-item wp-chapter-nav-next" href="<?php echo esc_url( home_url( '/#pricing' ) ); ?>">
			<span class="wp-chapter-nav-dir">解锁全本 →</span>
			<span class="wp-chapter-nav-title">获取完整内容</span>
		</a>
		<?php endif; ?>
	</div>
</div>
