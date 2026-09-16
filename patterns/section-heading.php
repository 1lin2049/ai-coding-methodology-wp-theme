<?php
/**
 * Title: 区块标题 Section Heading
 * Slug: ai-coding/section-heading
 * Categories: ai-coding/sections
 * Description: 终端标签 + 标题 + 描述，任意章节小节开头复用
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!-- wp:group {"className":"wrap section-head","layout":{"type":"default"}} -->
<div class="wrap section-head">
	<!-- wp:html -->
	<div class="section-tag"><span class="prompt">$</span><span>cmd</span><span class="path">args</span></div>
	<!-- /wp:html -->
	<!-- wp:heading {"level":2,"className":"section-title"} -->
	<h2 class="section-title">区块标题 <em>强调词</em></h2>
	<!-- /wp:heading -->
	<!-- wp:paragraph {"className":"section-desc"} -->
	<p class="section-desc">一句话说明本节讲什么 · 通用、可迁移、可验证。</p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->