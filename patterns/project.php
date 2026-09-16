<?php
/**
 * Title: 04 · Project 项目结构
 * Slug: ai-coding/project
 * Categories: ai-coding/sections
 * Description: 约束落在具体文件上的 ls 展示
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!-- wp:group {"tagName":"section","className":"section section-alt","layout":{"type":"default"}} -->
<section class="section section-alt" id="project">
	<!-- wp:group {"className":"wrap section-head","layout":{"type":"default"}} -->
	<div class="wrap section-head">
		<!-- wp:html -->
		<div class="section-tag"><span class="prompt">$</span><span>ls</span><span class="path">-la ./project-root</span></div>
		<!-- /wp:html -->
		<!-- wp:heading {"level":2,"className":"section-title"} -->
		<h2 class="section-title">约束不是概念<br>它落在一组<em>具体文件</em>上</h2>
		<!-- /wp:heading -->
	</div>
	<!-- /wp:group -->
	<!-- wp:html -->
	<div class="wrap">
		<div class="ls-block">
			<div class="ls-head">
				<span class="ls-h">MODE</span>
				<span class="ls-h">NAME</span>
				<span class="ls-h">SIZE</span>
				<span class="ls-h">TAG</span>
			</div>
			<div class="ls-row"><span class="ls-mode">drwxr-xr-x</span><span class="ls-name">docs/</span><span class="ls-size">—</span><span class="ls-tag">CONSTRAINT SURFACE</span></div>
			<div class="ls-row"><span class="ls-mode">-rw-r--r--</span><span class="ls-name">ARCHITECTURE.md</span><span class="ls-size">2.1k</span><span class="ls-tag tag-bd">BOUNDARY</span></div>
			<div class="ls-row"><span class="ls-mode">-rw-r--r--</span><span class="ls-name">AGENTS.md</span><span class="ls-size">1.4k</span><span class="ls-tag tag-ctx">CONTEXT</span></div>
			<div class="ls-row"><span class="ls-mode">-rw-r--r--</span><span class="ls-name">CONTEXT.md</span><span class="ls-size">0.9k</span><span class="ls-tag tag-ctx">CONTEXT</span></div>
			<div class="ls-row"><span class="ls-mode">drwxr-xr-x</span><span class="ls-name">.agents/skills/</span><span class="ls-size">—</span><span class="ls-tag tag-sl">SLICE</span></div>
			<div class="ls-row"><span class="ls-mode">-rw-r--r--</span><span class="ls-name">SKILL.md</span><span class="ls-size">3.2k</span><span class="ls-tag tag-sl">SLICE</span></div>
			<div class="ls-row"><span class="ls-mode">drwxr-xr-x</span><span class="ls-name">contracts/</span><span class="ls-size">—</span><span class="ls-tag tag-ct">CONTRACT</span></div>
			<div class="ls-row"><span class="ls-mode">drwxr-xr-x</span><span class="ls-name">tasks/current/</span><span class="ls-size">—</span><span class="ls-tag tag-st">STATE</span></div>
			<div class="ls-row"><span class="ls-mode">-rw-r--r--</span><span class="ls-name">plan.json</span><span class="ls-size">0.6k</span><span class="ls-tag tag-st">STATE</span></div>
			<div class="ls-row"><span class="ls-mode">drwxr-xr-x</span><span class="ls-name">tests/architecture/</span><span class="ls-size">—</span><span class="ls-tag tag-bd">BOUNDARY</span></div>
			<div class="ls-row"><span class="ls-mode">drwxr-xr-x</span><span class="ls-name">scripts/</span><span class="ls-size">—</span><span class="ls-tag tag-bd">GATE</span></div>
			<div class="ls-row"><span class="ls-mode">-rwxr-xr-x</span><span class="ls-name">check-dependency.sh</span><span class="ls-size">0.4k</span><span class="ls-tag tag-bd">BOUNDARY</span></div>
			<div class="ls-row"><span class="ls-mode">-rwxr-xr-x</span><span class="ls-name">ci.yml</span><span class="ls-size">0.8k</span><span class="ls-tag tag-bd">GATE</span></div>
		</div>
	</div>
	<!-- /wp:html -->
</section>
<!-- /wp:group -->