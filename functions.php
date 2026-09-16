<?php
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'AI_CODING_VERSION', '1.1.0' );
define( 'AI_CODING_DIR', get_template_directory() );
define( 'AI_CODING_URI', get_template_directory_uri() );

/* ── 核心 ── */
require_once AI_CODING_DIR . '/inc/db.php';
require_once AI_CODING_DIR . '/inc/layout.php';
require_once AI_CODING_DIR . '/inc/sidebars.php';
require_once AI_CODING_DIR . '/inc/taxonomies.php';
require_once AI_CODING_DIR . '/inc/enqueue.php';
require_once AI_CODING_DIR . '/inc/blocks.php';
require_once AI_CODING_DIR . '/inc/cpt.php';
require_once AI_CODING_DIR . '/inc/seed.php';
require_once AI_CODING_DIR . '/inc/icons.php';
require_once AI_CODING_DIR . '/inc/customizer.php';
require_once AI_CODING_DIR . '/inc/comments.php';
require_once AI_CODING_DIR . '/inc/content-io.php';
require_once AI_CODING_DIR . '/inc/cli.php';

/* ── 质量层 ── */
require_once AI_CODING_DIR . '/inc/seo.php';
require_once AI_CODING_DIR . '/inc/performance.php';
require_once AI_CODING_DIR . '/inc/a11y.php';

/* ── 统计 ── */
require_once AI_CODING_DIR . '/inc/track.php';
require_once AI_CODING_DIR . '/inc/cron.php';
require_once AI_CODING_DIR . '/inc/stats-admin.php';

/* ── 前台永久隐藏 admin bar ── */
add_filter( 'show_admin_bar', '__return_false' );

add_action( 'init', function () {
    if ( ! is_admin() ) {
        remove_action( 'wp_head',   '_admin_bar_bump_cb' );
        remove_action( 'wp_footer', 'wp_admin_bar_render', 1000 );
    }
}, 20 );

add_action( 'after_setup_theme', function () {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ] );
    add_theme_support( 'custom-logo', [ 'height' => 32, 'width' => 240, 'flex-height' => true, 'flex-width' => true ] );
    add_theme_support( 'editor-styles' );
    add_theme_support( 'wp-block-styles' );
    add_theme_support( 'responsive-embeds' );
    add_editor_style( [ 'assets/css/tokens.css', 'assets/css/main.css' ] );

    register_nav_menus( [
        'primary'         => __( 'Primary Nav', 'ai-coding' ),
        'footer-content'  => __( 'Footer · Content', 'ai-coding' ),
        'footer-purchase' => __( 'Footer · Purchase', 'ai-coding' ),
        'footer-other'    => __( 'Footer · Other', 'ai-coding' ),
    ] );
} );

remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );

add_filter( 'document_title_separator', fn() => '·' );