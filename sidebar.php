<?php
/**
 * 侧栏渲染 —— 混合模式：
 *   1. 有动态小工具 → dynamic_sidebar()（管理员自定义）
 *   2. 无 → 读者页面渲染主题默认四组件
 *   3. 非读者 → 空状态引导
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$layout    = function_exists( 'ai_get_layout' ) ? ai_get_layout() : null;
$sidebar_id = ( function_exists( 'ai_layout_sidebar_id' ) && $layout )
	? ai_layout_sidebar_id( $layout )
	: 'sidebar-right';

$is_reader = is_singular( 'chapter' ) || is_page_template( 'page-preview.php' );
?>

<aside class="sidebar" role="complementary" aria-label="侧栏">
	<div class="sidebar-inner">

		<?php if ( is_active_sidebar( $sidebar_id ) ) : ?>

			<?php dynamic_sidebar( $sidebar_id ); ?>

		<?php elseif ( $is_reader ) : ?>

			<?php get_template_part( 'parts/widget-chapters' ); ?>
			<?php get_template_part( 'parts/widget-prevnext' ); ?>
			<?php get_template_part( 'parts/widget-search' ); ?>
			<?php get_template_part( 'parts/widget-tags' ); ?>

		<?php else : ?>

			<div class="widget sidebar-hint">
				<p>在 <a href="<?php echo esc_url( admin_url( 'widgets.php' ) ); ?>">外观 → 小工具</a> 中配置侧栏内容。</p>
			</div>

		<?php endif; ?>

	</div>
</aside>
