<?php
/**
 * Title: 05 · Assets 工程资产
 * Slug: ai-coding/assets
 * Categories: ai-coding/sections
 * Description: 可进入项目的工程资产分组展示
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!-- wp:group {"tagName":"section","className":"section","layout":{"type":"default"}} -->
<section class="section" id="assets">
	<!-- wp:group {"className":"wrap section-head","layout":{"type":"default"}} -->
	<div class="wrap section-head">
		<!-- wp:html -->
		<div class="section-tag"><span class="prompt">$</span><span>ls</span><span class="path">./appendix/</span></div>
		<!-- /wp:html -->
		<!-- wp:heading {"level":2,"className":"section-title"} -->
		<h2 class="section-title">不是一堆 PDF<br>是可以<em>进入项目</em>的资产</h2>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"className":"section-desc"} -->
		<p class="section-desc">十二个附录按"基础 / 工程 / 治理"三层分组，每项都标注来源、版本与适用场景。</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
	<!-- wp:html -->
	<div class="wrap">
		<div class="asset-table">
			<div class="asset-group">
				<div class="asset-group-label">foundations · A–D</div>
				<div class="at-row"><span class="at-id">A</span><span class="at-name">ARCHITECTURE.md</span><span class="at-desc">架构规范 + 依赖矩阵 + 架构测试</span></div>
				<div class="at-row"><span class="at-id">B</span><span class="at-name">AGENTS.md</span><span class="at-desc">AI 行为准则 + 路由中枢</span></div>
				<div class="at-row"><span class="at-id">C</span><span class="at-name">SKILL.md</span><span class="at-desc">技能定义 + 反模式拦截 + 模板目录</span></div>
				<div class="at-row"><span class="at-id">D</span><span class="at-name">.tpl 全家桶</span><span class="at-desc">10 个 PHP 代码模板</span></div>
			</div>
			<div class="asset-group">
				<div class="asset-group-label">engineering · E–H</div>
				<div class="at-row"><span class="at-id">E</span><span class="at-name">架构测试模板</span><span class="at-desc">PHPArkitect + 自定义脚本 + CI 集成</span></div>
				<div class="at-row"><span class="at-id">F</span><span class="at-name">MCP 配置模板</span><span class="at-desc">CodeGraph / Filesystem / Git / Database</span></div>
				<div class="at-row"><span class="at-id">G</span><span class="at-name">四会话 Prompt</span><span class="at-desc">完整实录 + 四会话 Prompt 模板</span></div>
				<div class="at-row"><span class="at-id">H</span><span class="at-name">合规自查报告</span><span class="at-desc">分层 / 命名 / 边界 / 测试</span></div>
			</div>
			<div class="asset-group">
				<div class="asset-group-label">governance · I–L</div>
				<div class="at-row"><span class="at-id">I</span><span class="at-name">可继承资产总清单</span><span class="at-desc">五类资产 + 继承 + 演化记录</span></div>
				<div class="at-row"><span class="at-id">J</span><span class="at-name">角色-能力矩阵</span><span class="at-desc">11 个角色的五维配置</span></div>
				<div class="at-row"><span class="at-id">K</span><span class="at-name">AI-CMM 术语表</span><span class="at-desc">核心术语 + 数字分级说明</span></div>
				<div class="at-row"><span class="at-id">L</span><span class="at-name">参考文献与谱系</span><span class="at-desc">方法论谱系 + 引用规范</span></div>
			</div>
		</div>
	</div>
	<!-- /wp:html -->
</section>
<!-- /wp:group -->