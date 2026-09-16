<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/* ═══════════════════════════════════════════════
   首次激活 · 导入示例章节 + 创建 preview 页面
   ═══════════════════════════════════════════════ */
add_action( 'admin_init', function () {
    if ( get_option( 'ai_coding_seeded' ) ) return;
    if ( ! current_user_can( 'manage_options' ) ) return;

    $chapters = [
        [
            'slug'   => '00-preface',
            'number' => '00',
            'title'  => '代码越来越便宜之后',
            'words'  => '4,200 字',
            'time'   => '14 min',
            'lead'   => '当代码的边际成本趋近于零，工程师的价值到底在哪里？',
            'body'   =>
                "<!-- wp:paragraph -->\n" .
                "<p>这是一段占位正文。请在后台「试读章节 → 编辑」中替换为真实内容。</p>\n" .
                "<!-- /wp:paragraph -->\n\n" .
                "<!-- wp:heading -->\n" .
                "<h2>为什么写这本书</h2>\n" .
                "<!-- /wp:heading -->\n\n" .
                "<!-- wp:paragraph -->\n" .
                "<p>2026 年 4 月，我开始重度使用 AI Coding 做开发。从一个真实的退款功能事故出发，我意识到：<strong>真正的瓶颈不在生成，而在控制</strong>。</p>\n" .
                "<!-- /wp:paragraph -->",
        ],
        [
            'slug'   => '01',
            'number' => '01',
            'title'  => 'AI Coding 改变了什么',
            'words'  => '6,800 字',
            'time'   => '22 min',
            'lead'   => '从代码生成到约束工程，工程范式正在迁移。',
            'body'   => "<!-- wp:paragraph --><p>占位正文，请在后台编辑替换。</p><!-- /wp:paragraph -->",
        ],
        [
            'slug'   => '02',
            'number' => '02',
            'title'  => 'AI Coding 的三大死穴',
            'words'  => '7,500 字',
            'time'   => '24 min',
            'lead'   => '架构漂移、上下文失真、经验无法复制。',
            'body'   => "<!-- wp:paragraph --><p>占位正文，请在后台编辑替换。</p><!-- /wp:paragraph -->",
        ],
        [
            'slug'   => '03',
            'number' => '03',
            'title'  => '人也会失控：一次中央治理失败复盘',
            'words'  => '8,200 字',
            'time'   => '26 min',
            'lead'   => '中央治理失败后，我们重新思考了约束的位置。',
            'body'   => "<!-- wp:paragraph --><p>占位正文，请在后台编辑替换。</p><!-- /wp:paragraph -->",
        ],
    ];

    foreach ( $chapters as $i => $c ) {
        if ( get_page_by_path( $c['slug'], OBJECT, 'chapter' ) ) continue;

        $id = wp_insert_post( [
            'post_type'    => 'chapter',
            'post_status'  => 'publish',
            'post_title'   => $c['title'],
            'post_name'    => $c['slug'],
            'post_excerpt' => $c['lead'],
            'post_content' => $c['body'],
            'menu_order'   => $i,
        ] );

        if ( $id && ! is_wp_error( $id ) ) {
            update_post_meta( $id, 'chapter_number', $c['number'] );
            update_post_meta( $id, 'chapter_words',  $c['words'] );
            update_post_meta( $id, 'chapter_time',   $c['time'] );
        }
    }

    update_option( 'ai_coding_seeded', 1 );

    flush_rewrite_rules();
} );


/* ═══════════════════════════════════════════════
   自愈 · preview 页面
   每次进后台都检查，不存在则创建，且强制绑定模板
   ═══════════════════════════════════════════════ */
add_action( 'admin_init', 'ai_ensure_preview_page', 9 );
function ai_ensure_preview_page() {
    if ( ! current_user_can( 'manage_options' ) ) return;

    /* 按 slug 找 */
    $page = get_page_by_path( 'preview' );

    /* 找不到：按已绑定模板找 */
    if ( ! $page ) {
        $found = get_posts( [
            'post_type'      => 'page',
            'posts_per_page' => 1,
            'post_status'    => [ 'publish', 'draft', 'private' ],
            'meta_key'       => '_wp_page_template',
            'meta_value'     => 'page-preview.php',
            'fields'         => 'all',
        ] );
        if ( ! empty( $found ) ) $page = $found[0];
    }

    /* 真没有：创建 */
    if ( ! $page ) {
        $new_id = wp_insert_post( [
            'post_type'    => 'page',
            'post_status'  => 'publish',
            'post_title'   => '试读',
            'post_name'    => 'preview',
            'post_content' => '',
        ] );
        if ( $new_id && ! is_wp_error( $new_id ) ) {
            update_post_meta( $new_id, '_wp_page_template', 'page-preview.php' );
            flush_rewrite_rules();
        }
        return;
    }

    /* 存在：确保模板正确 */
    $current_tpl = get_post_meta( $page->ID, '_wp_page_template', true );
    if ( $current_tpl !== 'page-preview.php' ) {
        update_post_meta( $page->ID, '_wp_page_template', 'page-preview.php' );
    }
}