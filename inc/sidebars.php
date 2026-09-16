<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/* ═══════════════════════════════════════════════
   侧栏 Widget 区注册
   sidebar.php 会按布局选择 sidebar-left / sidebar-right，
   sidebar-main 为兼容旧模板保留（若仍有模板直接取用）。
   ═══════════════════════════════════════════════ */
add_action( 'widgets_init', function () {
    $args = [
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ];

    register_sidebar( array_merge( $args, [
        'name'        => '侧栏 · 左',
        'id'          => 'sidebar-left',
        'description' => '左侧栏布局使用',
    ] ) );

    register_sidebar( array_merge( $args, [
        'name'        => '侧栏 · 右',
        'id'          => 'sidebar-right',
        'description' => '右侧栏布局使用',
    ] ) );

    register_sidebar( array_merge( $args, [
        'name'        => '侧栏 · 主',
        'id'          => 'sidebar-main',
        'description' => '兼容旧模板，实际请使用"侧栏 · 左 / 右"',
    ] ) );
} );