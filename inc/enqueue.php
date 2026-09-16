<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'wp_enqueue_scripts', function () {
    $uri = AI_CODING_URI;
    $ver = AI_CODING_VERSION;

    /* ═══════════════════════════════════════════════
       全站基础样式
       ═══════════════════════════════════════════════ */
    wp_enqueue_style( 'ai-reset',      "$uri/assets/css/reset.css",      [], $ver );
    wp_enqueue_style( 'ai-tokens',     "$uri/assets/css/tokens.css",     [ 'ai-reset' ], $ver );
    wp_enqueue_style( 'ai-components', "$uri/assets/css/components.css", [ 'ai-tokens' ], $ver );
    wp_enqueue_style( 'ai-a11y',       "$uri/assets/css/a11y.css",       [ 'ai-components' ], $ver );

    /* 鼠标光晕 · 全站 */
    wp_enqueue_script( 'ai-mouse-glow', "$uri/assets/js/mouse-glow.js", [], $ver, true );

    /* ═══════════════════════════════════════════════
       环境判定
       ═══════════════════════════════════════════════ */
    $is_preview   = is_page( 'preview' ) || is_page_template( 'page-preview.php' );
    $is_chapter   = is_singular( 'chapter' );
    $is_reader    = $is_chapter || $is_preview;
    $is_trackable = is_singular( [ 'post', 'page', 'chapter' ] );

    /* ═══════════════════════════════════════════════
       所有环境都加载完整 CSS 链路（与文章页一致）
       reader 环境在末尾叠加 reader.css
       ═══════════════════════════════════════════════ */
    wp_enqueue_style( 'ai-main',   "$uri/assets/css/main.css",   [ 'ai-a11y' ], $ver );
    wp_enqueue_style( 'ai-motion', "$uri/assets/css/motion.css", [ 'ai-main' ], $ver );
    wp_enqueue_style( 'ai-mobile', "$uri/assets/css/mobile.css", [ 'ai-motion' ], $ver );

    if ( $is_reader ) {
        /* reader.css 最后加载，覆盖上面的通用样式 */
        wp_enqueue_style( 'ai-reader', "$uri/assets/css/reader.css", [ 'ai-mobile' ], $ver );

        if ( $is_preview ) {
            wp_enqueue_style( 'ai-preview', "$uri/assets/css/preview.css", [ 'ai-reader' ], $ver );
        }

        if ( $is_chapter ) {
            wp_enqueue_style( 'ai-reader-extras',    "$uri/assets/css/reader-extras.css",    [ 'ai-reader' ], $ver );
            wp_enqueue_style( 'ai-chapter-comments', "$uri/assets/css/chapter-comments.css", [ 'ai-reader-extras' ], $ver );
        }

        wp_enqueue_script( 'ai-reader', "$uri/assets/js/reader.js", [], $ver, true );
    } else {
        wp_enqueue_script( 'ai-main',   "$uri/assets/js/main.js",   [], $ver, true );
        wp_enqueue_script( 'ai-motion', "$uri/assets/js/motion.js", [ 'ai-main' ], $ver, true );

        wp_localize_script( 'ai-main', 'AI_CODING', [
            'themeUri' => $uri,
            'homeUrl'  => home_url( '/' ),
        ] );

        if ( is_singular( [ 'post', 'page' ] ) ) {
            wp_enqueue_style( 'ai-reader-extras', "$uri/assets/css/reader-extras.css", [ 'ai-main' ], $ver );
        }
    }

    /* ── 单页通用脚本 ── */
    if ( $is_trackable ) {
        wp_enqueue_script( 'ai-share',            "$uri/assets/js/share.js",            [], $ver, true );
        wp_enqueue_script( 'ai-track',            "$uri/assets/js/track.js",            [], $ver, true );
        wp_enqueue_script( 'ai-reading-position', "$uri/assets/js/reading-position.js", [], $ver, true );

        wp_localize_script( 'ai-track', 'AI_TRACK', [
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'postId'  => get_the_ID(),
            'type'    => get_post_type(),
        ] );
    }

    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
} );

/* ═══════════════════════════════════════════════
   编辑器样式链 · 让 Pattern 在块编辑器中带完整主题样式
   （经典主题不自动注入主题样式，导致插入的样板在编辑器里无样式）
   ═══════════════════════════════════════════════ */
add_action( 'enqueue_block_assets', function () {
    if ( ! is_admin() ) return;
    $uri = AI_CODING_URI;
    $ver = AI_CODING_VERSION;

    wp_enqueue_style( 'ai-editor-reset',      "$uri/assets/css/reset.css",      [], $ver );
    wp_enqueue_style( 'ai-editor-tokens',     "$uri/assets/css/tokens.css",     [ 'ai-editor-reset' ], $ver );
    wp_enqueue_style( 'ai-editor-components', "$uri/assets/css/components.css", [ 'ai-editor-tokens' ], $ver );
    wp_enqueue_style( 'ai-editor-a11y',       "$uri/assets/css/a11y.css",       [ 'ai-editor-components' ], $ver );
    wp_enqueue_style( 'ai-editor-main',       "$uri/assets/css/main.css",       [ 'ai-editor-a11y' ], $ver );
    wp_enqueue_style( 'ai-editor-motion',     "$uri/assets/css/motion.css",     [ 'ai-editor-main' ], $ver );
} );