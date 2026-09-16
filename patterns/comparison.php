<?php
/**
 * Title: 前后对比 Compare
 * Slug: ai-coding/comparison
 * Categories: ai-coding/components
 * Description: 旧方式 vs 新方式 两栏对比
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!-- wp:group {"className":"w-compare","layout":{"type":"default"}} -->
<div class="w-compare">
	<!-- wp:group {"className":"w-compare-card","layout":{"type":"default"}} -->
	<div class="wp-block-group w-compare-card">
		<!-- wp:heading {"level":3} -->
		<h3 class="wp-block-heading">OLD</h3>
		<!-- /wp:heading -->
		<!-- wp:paragraph -->
		<p>旧方式的描述与局限。</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
	<!-- wp:group {"className":"w-compare-card w-compare-new","layout":{"type":"default"}} -->
	<div class="wp-block-group w-compare-card w-compare-new">
		<!-- wp:heading {"level":3} -->
		<h3 class="wp-block-heading"><span class="w-arrow">→</span> NEW</h3>
		<!-- /wp:heading -->
		<!-- wp:paragraph -->
		<p>新方式的描述与优势。</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->