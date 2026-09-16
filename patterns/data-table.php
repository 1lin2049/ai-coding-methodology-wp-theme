<?php
/**
 * Title: 参数表格 Data Table
 * Slug: ai-coding/data-table
 * Categories: ai-coding/components
 * Description: Ant Design 风格参数表格（列：ID / 名称 / 说明）
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!-- wp:table {"className":"w-table"} -->
<figure class="wp-block-table w-table">
	<table class="has-fixed-layout">
		<thead>
			<tr>
				<th>ID</th>
				<th>名称</th>
				<th>说明</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>01</td>
				<td>条目一</td>
				<td>一句话说明。</td>
			</tr>
			<tr>
				<td>02</td>
				<td>条目二</td>
				<td>一句话说明。</td>
			</tr>
			<tr>
				<td>03</td>
				<td>条目三</td>
				<td>一句话说明。</td>
			</tr>
		</tbody>
	</table>
</figure>
<!-- /wp:table -->