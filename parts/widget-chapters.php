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

$prev     = ( $current_index > 0 ) ? $chapters[ $current_index - 1 ] : null;
$next     = ( $current_index >= 0 && $current_index < count( $chapters ) - 1 ) ? $chapters[ $current_index + 1 ] : null;
$prev_num = $prev ? get_post_meta( $prev->ID, 'chapter_number', true ) : '';
$next_num = $next ? get_post_meta( $next->ID, 'chapter_number', true ) : '';
$toc      = home_url( '/preview/' );
$buy      = home_url( '/#pricing' );
?>

<div class="widget widget-chapters">
	<div class="widget-head">
		<h4 class="widget-title">目录</h4>
		<span class="widget-head-meta"><?php echo (int) count( $chapters ); ?> 章</span>
	</div>
	<ul class="widget-list">
		<?php foreach ( $chapters as $c ) :
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
	<div class="widget-foot">
		<?php if ( $prev ) : ?>
			<a href="<?php echo esc_url( get_permalink( $prev ) ); ?>">
				<span class="widget-foot-dir">← 上一章</span>
				<span class="widget-foot-title"><?php echo esc_html( trim( $prev_num . ' · ' . $prev->post_title, ' ·' ) ); ?></span>
			</a>
		<?php else : ?>
			<a href="<?php echo esc_url( $toc ); ?>">
				<span class="widget-foot-dir">← 目录</span>
				<span class="widget-foot-title">返回试读目录</span>
			</a>
		<?php endif; ?>

		<?php if ( $next ) : ?>
			<a href="<?php echo esc_url( get_permalink( $next ) ); ?>">
				<span class="widget-foot-dir">下一章 →</span>
				<span class="widget-foot-title"><?php echo esc_html( trim( $next_num . ' · ' . $next->post_title, ' ·' ) ); ?></span>
			</a>
		<?php else : ?>
			<a href="<?php echo esc_url( $buy ); ?>">
				<span class="widget-foot-dir">解锁全本 →</span>
				<span class="widget-foot-title">获取完整内容</span>
			</a>
		<?php endif; ?>
		<?php if ( count( $chapters ) > 20 ) : ?>
			<a class="widget-foot-more" href="<?php echo esc_url( $toc ); ?>">查看全书目录 →</a>
		<?php endif; ?>
	</div>
</div>