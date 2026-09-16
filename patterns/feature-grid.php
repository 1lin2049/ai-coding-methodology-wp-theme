<?php
/**
 * Title: 卡片栅格 Feature Grid
 * Slug: ai-coding/feature-grid
 * Categories: ai-coding/components
 * Description: 2–3 列卡片栅格，用于能力 / 机制 / 维度展示
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!-- wp:group {"className":"feature-grid","layout":{"type":"default"}} -->
<div class="feature-grid">
	<!-- wp:columns -->
	<div class="wp-block-columns">
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:group {"className":"w-card"} -->
			<div class="wp-block-group w-card">
				<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"15px"}}} -->
				<h3 class="wp-block-heading" style="font-size:15px">维度 <em>一</em></h3>
				<!-- /wp:heading -->
				<!-- wp:paragraph -->
				<p>这一条的作用与适用范围，一句话说清。</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:group {"className":"w-card"} -->
			<div class="wp-block-group w-card">
				<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"15px"}}} -->
				<h3 class="wp-block-heading" style="font-size:15px">维度 <em>二</em></h3>
				<!-- /wp:heading -->
				<!-- wp:paragraph -->
				<p>这一条的作用与适用范围，一句话说清。</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:group {"className":"w-card"} -->
			<div class="wp-block-group w-card">
				<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"15px"}}} -->
				<h3 class="wp-block-heading" style="font-size:15px">维度 <em>三</em></h3>
				<!-- /wp:heading -->
				<!-- wp:paragraph -->
				<p>这一条的作用与适用范围，一句话说清。</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->