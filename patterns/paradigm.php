<?php
/**
 * Title: 02 · Paradigm 范式
 * Slug: ai-coding/paradigm
 * Categories: ai-coding/sections
 * Description: 传统开发 vs AI 原生工程范式对比
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!-- wp:group {"tagName":"section","className":"section section-alt","layout":{"type":"default"}} -->
<section class="section section-alt" id="paradigm">
	<!-- wp:group {"className":"wrap section-head","layout":{"type":"default"}} -->
	<div class="wrap section-head">
		<!-- wp:html -->
		<div class="section-tag"><span class="prompt">$</span><span>diff</span><span class="path">before.txt after.txt</span></div>
		<!-- /wp:html -->
		<!-- wp:heading {"level":2,"className":"section-title"} -->
		<h2 class="section-title">过去管理<em>代码</em><br>现在管理<em>约束</em></h2>
		<!-- /wp:heading -->
	</div>
	<!-- /wp:group -->
	<!-- wp:html -->
	<div class="wrap">
		<div class="diff-block">
			<div class="diff-side diff-before">
				<div class="diff-label">传统开发</div>
				<div class="diff-flow"><span>需求 → 人 → 代码</span></div>
				<div class="diff-note">人既是设计者也是执行者 · 代码是核心产出</div>
			</div>
			<div class="diff-side diff-after">
				<div class="diff-label">AI 原生工程</div>
				<div class="diff-flow"><span class="accent">人 → 约束 → AI → 代码 → 验证 → 演化</span></div>
				<div class="diff-note">AI 被纳入完整的工程闭环 · 速度可控，质量可验证</div>
			</div>
		</div>
		<p class="quote-large">不要把 AI 当成一个更快的程序员<br>把 AI 纳入完整的<em>约束、验证与反馈系统</em></p>
	</div>
	<!-- /wp:html -->
</section>
<!-- /wp:group -->