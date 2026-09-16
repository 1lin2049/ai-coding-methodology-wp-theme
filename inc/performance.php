<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Performance · 精简 head 与资源
 */

/* ═══════════════════════════════════════════════
   移除 emoji
   ═══════════════════════════════════════════════ */
remove_action( 'wp_head',             'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles',     'print_emoji_styles' );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles',  'print_emoji_styles' );
remove_filter( 'the_content_feed',    'wp_staticize_emoji' );
remove_filter( 'comment_text_rss',    'wp_staticize_emoji' );
remove_filter( 'wp_mail',             'wp_staticize_emoji_for_email' );

add_filter( 'tiny_mce_plugins', function ( $plugins ) {
    return is_array( $plugins ) ? array_diff( $plugins, [ 'wpemoji' ] ) : [];
} );
add_filter( 'emoji_svg_url', '__return_false' );


/* ═══════════════════════════════════════════════
   移除 wp-embed
   ═══════════════════════════════════════════════ */
add_action( 'wp_footer', function () {
    wp_deregister_script( 'wp-embed' );
}, 1 );


/* ═══════════════════════════════════════════════
   前台未登录：移除 dashicons
   ═══════════════════════════════════════════════ */
add_action( 'wp_enqueue_scripts', function () {
    if ( ! is_user_logged_in() ) {
        wp_deregister_style( 'dashicons' );
    }
}, 100 );


/* ═══════════════════════════════════════════════
   精简 oEmbed
   ═══════════════════════════════════════════════ */
add_action( 'init', function () {
    remove_action( 'rest_api_init', 'wp_oembed_register_route' );
    remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );
    remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
    remove_action( 'wp_head', 'wp_oembed_add_host_js' );
    remove_filter( 'pre_oembed_result', 'wp_filter_pre_oembed_result', 10 );
    add_filter( 'embed_oembed_discover', '__return_false' );
    add_filter( 'tiny_mce_plugins', function ( $plugins ) {
        return array_diff( $plugins, [ 'wpembed' ] );
    } );
} );


/* ═══════════════════════════════════════════════
   关闭 XML-RPC / Pingback
   ═══════════════════════════════════════════════ */
add_filter( 'xmlrpc_enabled', '__return_false' );

add_filter( 'wp_headers', function ( $headers ) {
    unset( $headers['X-Pingback'] );
    return $headers;
} );


/* ═══════════════════════════════════════════════
   移除 jQuery Migrate
   ═══════════════════════════════════════════════ */
add_action( 'wp_default_scripts', function ( $scripts ) {
    if ( is_admin() || empty( $scripts->registered['jquery'] ) ) return;

    $jq = $scripts->registered['jquery'];
    if ( ! empty( $jq->deps ) ) {
        $jq->deps = array_diff( $jq->deps, [ 'jquery-migrate' ] );
    }
} );


/* ═══════════════════════════════════════════════
   移除头部冗余 link
   ═══════════════════════════════════════════════ */
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );


/* ═══════════════════════════════════════════════
   归档标题去掉 "Category:" 等前缀
   ═══════════════════════════════════════════════ */
add_filter( 'get_the_archive_title_prefix', '__return_empty_string' );