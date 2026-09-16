<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * 样板（Block Patterns）分类注册。
 *
 * WordPress 6.1+ 会自动扫描主题根目录 `patterns/` 下的 PHP 文件，
 * 读取文件头注释（Title / Slug / Categories / Description）并执行
 * 文件内容作为样板的静态展示，因此这里只需注册分类即可。
 *
 * 双轨原则：
 *   - parts/  → 前台动态渲染源（ai_opt() 自定义器数据，唯一真值）
 *   - patterns/ → 编辑器可插入的静态展示样板（硬编码示例数据）
 */
add_action( 'init', function () {
    register_block_pattern_category( 'ai-coding/sections', [
        'label' => __( 'AI Coding · 区块', 'ai-coding' ),
    ] );
    register_block_pattern_category( 'ai-coding/components', [
        'label' => __( 'AI Coding · 组件', 'ai-coding' ),
    ] );
    register_block_pattern_category( 'ai-coding/cta', [
        'label' => __( 'AI Coding · 行动区', 'ai-coding' ),
    ] );
} );