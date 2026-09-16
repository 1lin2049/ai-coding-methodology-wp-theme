<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/* ═══════════════════════════════════════════════
   注册章节 CPT
   ★ supports 加 comments → 编辑页出现"讨论"面板
   ═══════════════════════════════════════════════ */
add_action( 'init', function () {
    register_post_type( 'chapter', [
        'labels' => [
            'name'               => '试读章节',
            'singular_name'      => '章节',
            'menu_name'          => '试读章节',
            'add_new'            => '新增章节',
            'add_new_item'       => '新增章节',
            'edit_item'          => '编辑章节',
            'new_item'           => '新章节',
            'view_item'          => '查看章节',
            'search_items'       => '搜索章节',
            'not_found'          => '没有章节',
            'not_found_in_trash' => '回收站里没有章节',
        ],
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_rest'       => true,
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => 20,
        'menu_icon'          => 'dashicons-book-alt',
        'supports'           => [ 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'custom-fields', 'comments' ],
        'rewrite'            => [
            'slug'       => 'preview',
            'with_front' => false,
        ],
    ] );
} );

/* ═══════════════════════════════════════════════
   ★ 新建章节时默认关闭评论
   ═══════════════════════════════════════════════ */
add_filter( 'wp_insert_post_data', function ( $data, $postarr ) {
    if ( ! isset( $data['post_type'] ) || $data['post_type'] !== 'chapter' ) {
        return $data;
    }

    /* 只在新建时（无 ID）强制 closed */
    if ( empty( $postarr['ID'] ) ) {
        $data['comment_status'] = 'closed';
    }

    return $data;
}, 10, 2 );

/* ═══════════════════════════════════════════════
   ★ 一次性迁移 · 关闭已有章节的评论
   只在首次执行，之后用户手动开的不会被覆盖
   ═══════════════════════════════════════════════ */
add_action( 'admin_init', function () {
    if ( get_option( 'ai_chapter_comments_default_closed' ) ) return;
    if ( ! current_user_can( 'manage_options' ) ) return;

    global $wpdb;
    $wpdb->query(
        "UPDATE {$wpdb->posts}
         SET comment_status = 'closed'
         WHERE post_type = 'chapter'
           AND comment_status = 'open'"
    );

    update_option( 'ai_chapter_comments_default_closed', 1 );
} );

/* ═══════════════════════════════════════════════
   章节元数据 · chapter_number
   ═══════════════════════════════════════════════ */
add_action( 'init', function () {
    register_post_meta( 'chapter', 'chapter_number', [
        'type'         => 'string',
        'single'       => true,
        'show_in_rest' => true,
        'default'      => '',
    ] );
} );

/* ═══════════════════════════════════════════════
   后台列表按章节序号排序
   ═══════════════════════════════════════════════ */
add_action( 'pre_get_posts', function ( $q ) {
    if ( is_admin() && $q->get( 'post_type' ) === 'chapter' && ! $q->get( 'orderby' ) ) {
        $q->set( 'orderby', 'menu_order' );
        $q->set( 'order',   'ASC' );
    }
} );

/* ═══════════════════════════════════════════════
   章节信息侧栏
   ═══════════════════════════════════════════════ */
add_action( 'add_meta_boxes', function () {
    add_meta_box(
        'ai-chapter-meta',
        '章节信息',
        function ( $post ) {
            wp_nonce_field( 'ai_chapter_meta', 'ai_chapter_meta_nonce' );
            $num = get_post_meta( $post->ID, 'chapter_number', true );
            ?>
            <p style="margin-top:0">
                <label><strong>章节序号</strong>（如 00 / 01 / 02）<br>
                <input type="text" name="chapter_number" value="<?php echo esc_attr( $num ); ?>" style="width:100%" placeholder="留空则不显示编号"></label>
            </p>
            <p style="margin:12px 0 0;color:#666;font-size:12px;line-height:1.6">
              字数与阅读时长由正文自动计算，无需手动填写。
            </p>
            <?php
        },
        'chapter',
        'side',
        'default'
    );
} );

add_action( 'save_post_chapter', function ( $post_id ) {
    if ( ! isset( $_POST['ai_chapter_meta_nonce'] ) ) return;
    if ( ! wp_verify_nonce( $_POST['ai_chapter_meta_nonce'], 'ai_chapter_meta' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    if ( isset( $_POST['chapter_number'] ) ) {
        update_post_meta( $post_id, 'chapter_number', sanitize_text_field( wp_unslash( $_POST['chapter_number'] ) ) );
    }
} );