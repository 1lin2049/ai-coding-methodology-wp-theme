<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/* ═══════════════════════════════════════════════
   注册 chapter_section · 章节分组
   ★ show_in_quick_edit => false 关闭原生快速编辑控件
   ═══════════════════════════════════════════════ */
add_action( 'init', function () {
    register_taxonomy( 'chapter_section', [ 'chapter' ], [
        'labels' => [
            'name'              => '章节分组',
            'singular_name'     => '分组',
            'search_items'      => '搜索分组',
            'all_items'         => '所有分组',
            'parent_item'       => '父分组',
            'parent_item_colon' => '父分组：',
            'edit_item'         => '编辑分组',
            'update_item'       => '更新分组',
            'add_new_item'      => '添加分组',
            'new_item_name'     => '新分组名称',
            'menu_name'         => '章节分组',
        ],
        'public'              => true,
        'publicly_queryable'  => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => false,
        'show_in_rest'        => true,
        'show_admin_column'   => false,
        'show_in_quick_edit'  => false,   /* ★ 关闭原生快速编辑 */
        'hierarchical'        => true,
        'rewrite'             => [ 'slug' => 'section', 'with_front' => false ],
    ] );
}, 5 );

/* ═══════════════════════════════════════════════
   注册 chapter_access · 访问级别
   ★ 同样关闭原生快速编辑
   ═══════════════════════════════════════════════ */
add_action( 'init', function () {
    register_taxonomy( 'chapter_access', [ 'chapter' ], [
        'labels' => [
            'name'          => '访问级别',
            'singular_name' => '访问级别',
            'search_items'  => '搜索访问级别',
            'all_items'     => '所有访问级别',
            'edit_item'     => '编辑访问级别',
            'update_item'   => '更新访问级别',
            'add_new_item'  => '添加访问级别',
            'new_item_name' => '新访问级别名称',
            'menu_name'     => '访问级别',
        ],
        'public'              => false,
        'publicly_queryable'  => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_rest'        => true,
        'show_admin_column'   => false,
        'show_in_quick_edit'  => false,   /* ★ 关闭原生快速编辑 */
        'hierarchical'        => false,
        'rewrite'             => false,
    ] );
}, 6 );

/* ═══════════════════════════════════════════════
   首次创建默认 terms
   ═══════════════════════════════════════════════ */
add_action( 'admin_init', function () {
    if ( get_option( 'ai_tax_seeded_v2' ) ) return;
    if ( ! current_user_can( 'manage_options' ) ) return;

    $sections = [
        'part-1'   => '第一部分 · 问题',
        'part-2'   => '第二部分 · 范式',
        'part-3'   => '第三部分 · 框架',
        'part-4'   => '第四部分 · 实践',
        'appendix' => '附录',
    ];
    foreach ( $sections as $slug => $name ) {
        if ( ! term_exists( $slug, 'chapter_section' ) ) {
            wp_insert_term( $name, 'chapter_section', [ 'slug' => $slug ] );
        }
    }

    if ( ! term_exists( 'free', 'chapter_access' ) ) {
        wp_insert_term( '免费', 'chapter_access', [ 'slug' => 'free' ] );
    }
    if ( ! term_exists( 'paid', 'chapter_access' ) ) {
        wp_insert_term( '付费', 'chapter_access', [ 'slug' => 'paid' ] );
    }

    update_option( 'ai_tax_seeded_v2', 1 );
} );

/* ═══════════════════════════════════════════════
   后台章节列表 · 列定义
   ★ "访问" → "级别"
   ═══════════════════════════════════════════════ */
add_filter( 'manage_chapter_posts_columns', function ( $columns ) {
    $cb = $columns['cb'] ?? '<input type="checkbox" />';

    return [
        'cb'               => $cb,
        'chapter_number'   => '序号',
        'title'            => '标题',
        'chapter_section'  => '分组',
        'chapter_access'   => '级别',
        'chapter_words'    => '字数',
        'chapter_est'      => '阅读',
        'date'             => '日期',
        'ai_stat_views'    => '浏览',
        'ai_stat_reads'    => '深度阅读',
        'ai_stat_comments' => '评论',
    ];
}, 100 );

/* 渲染列 */
add_action( 'manage_chapter_posts_custom_column', function ( $column, $post_id ) {
    switch ( $column ) {
        case 'chapter_number':
            $v = get_post_meta( $post_id, 'chapter_number', true );
            echo $v !== '' ? esc_html( $v ) : '<span class="ai-empty">—</span>';
            break;

        case 'chapter_section':
            $terms = wp_get_post_terms( $post_id, 'chapter_section', [ 'fields' => 'all' ] );
            if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                $t = $terms[0];
                printf(
                    '<span class="ai-qe-value" data-value="%s">%s</span>',
                    esc_attr( $t->term_id ),
                    esc_html( $t->name )
                );
            } else {
                echo '<span class="ai-qe-value ai-empty" data-value="">—</span>';
            }
            break;

        case 'chapter_access':
            $terms = wp_get_post_terms( $post_id, 'chapter_access', [ 'fields' => 'all' ] );
            if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                $t = $terms[0];
                printf(
                    '<span class="ai-qe-value" data-value="%s">%s</span>',
                    esc_attr( $t->term_id ),
                    esc_html( $t->name )
                );
            } else {
                echo '<span class="ai-qe-value ai-empty" data-value="">—</span>';
            }
            break;

        case 'chapter_words':
            if ( function_exists( 'ai_count_words' ) ) {
                echo esc_html( number_format( ai_count_words( $post_id ) ) );
            }
            break;

        case 'chapter_est':
            if ( function_exists( 'ai_get_reading_minutes' ) ) {
                echo esc_html( ai_get_reading_minutes( $post_id ) . ' 分钟' );
            }
            break;
    }
}, 10, 2 );

/* ═══════════════════════════════════════════════
   列宽 CSS
   ═══════════════════════════════════════════════ */
add_action( 'admin_head-edit.php', function () {
    $screen = get_current_screen();
    if ( ! $screen || $screen->id !== 'edit-chapter' ) return;
    ?>
    <style>
      .post-type-chapter .wp-list-table {
        table-layout: fixed !important;
        min-width: 1300px !important;
      }

      .post-type-chapter .wp-list-table .column-cb               { width: 36px  !important; }
      .post-type-chapter .wp-list-table .column-chapter_number   { width: 60px  !important; }
      .post-type-chapter .wp-list-table .column-title            { width: 380px !important; }
      .post-type-chapter .wp-list-table .column-chapter_section  { width: 130px !important; }
      .post-type-chapter .wp-list-table .column-chapter_access   { width: 70px  !important; }
      .post-type-chapter .wp-list-table .column-chapter_words    { width: 70px  !important; }
      .post-type-chapter .wp-list-table .column-chapter_est      { width: 80px  !important; }
      .post-type-chapter .wp-list-table .column-date             { width: 110px !important; }
      .post-type-chapter .wp-list-table .column-ai_stat_views    { width: 60px  !important; }
      .post-type-chapter .wp-list-table .column-ai_stat_reads    { width: 80px  !important; }
      .post-type-chapter .wp-list-table .column-ai_stat_comments { width: 60px  !important; }

      .post-type-chapter .wp-list-table .column-title strong a {
        display: inline-block;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        vertical-align: middle;
      }

      .post-type-chapter .wp-list-table th,
      .post-type-chapter .wp-list-table td {
        vertical-align: middle;
        padding: 10px 8px;
      }

      .ai-empty {
        color: #bbb;
      }
    </style>
    <?php
} );

/* ═══════════════════════════════════════════════
   快速编辑 · 分组 + 访问级别
   ★ 因为关闭了 show_in_quick_edit，这里输出的是唯一控件
   ═══════════════════════════════════════════════ */
add_action( 'quick_edit_custom_box', function ( $column_name, $post_type ) {
    if ( $post_type !== 'chapter' ) return;
    if ( $column_name !== 'chapter_section' ) return;

    wp_nonce_field( 'ai_quick_edit', 'ai_quick_edit_nonce' );

    $sections = get_terms( [ 'taxonomy' => 'chapter_section', 'hide_empty' => false ] );
    $accesses = get_terms( [ 'taxonomy' => 'chapter_access',  'hide_empty' => false ] );
    ?>
    <fieldset class="inline-edit-col-right">
      <div class="inline-edit-col">
        <label>
          <span class="title">分组</span>
          <select name="chapter_section" id="ai-qe-section">
            <option value="">— 无 —</option>
            <?php if ( ! is_wp_error( $sections ) ) : foreach ( $sections as $t ) : ?>
              <option value="<?php echo esc_attr( $t->term_id ); ?>"><?php echo esc_html( $t->name ); ?></option>
            <?php endforeach; endif; ?>
          </select>
        </label>
        <label>
          <span class="title">访问级别</span>
          <select name="chapter_access" id="ai-qe-access">
            <option value="">— 无 —</option>
            <?php if ( ! is_wp_error( $accesses ) ) : foreach ( $accesses as $t ) : ?>
              <option value="<?php echo esc_attr( $t->term_id ); ?>"><?php echo esc_html( $t->name ); ?></option>
            <?php endforeach; endif; ?>
          </select>
        </label>
      </div>
    </fieldset>
    <?php
}, 10, 2 );

/* 快速编辑 · JS 填充当前值 */
add_action( 'admin_footer-edit.php', function () {
    $screen = get_current_screen();
    if ( ! $screen || $screen->id !== 'edit-chapter' ) return;
    ?>
    <script>
    (function ($) {
      if (typeof inlineEditPost === 'undefined') return;
      var orig = inlineEditPost.edit;
      inlineEditPost.edit = function (id) {
        orig.apply(this, arguments);
        var postId = 0;
        if (typeof id === 'object') postId = parseInt(this.getId(id), 10);
        if (postId > 0) {
          var $row = $('#post-' + postId);
          var s = $row.find('.column-chapter_section .ai-qe-value').data('value') || '';
          var a = $row.find('.column-chapter_access .ai-qe-value').data('value') || '';
          $('#ai-qe-section').val(String(s));
          $('#ai-qe-access').val(String(a));
        }
      };
    })(jQuery);
    </script>
    <?php
} );

/* ═══════════════════════════════════════════════
   批量编辑 · 分组 + 访问级别
   ═══════════════════════════════════════════════ */
add_action( 'bulk_edit_custom_box', function ( $column_name, $post_type ) {
    if ( $post_type !== 'chapter' ) return;
    if ( $column_name !== 'chapter_section' ) return;

    /* 批量编辑自带 nonce，但为了区分，用独立字段名 */
    $sections = get_terms( [ 'taxonomy' => 'chapter_section', 'hide_empty' => false ] );
    $accesses = get_terms( [ 'taxonomy' => 'chapter_access',  'hide_empty' => false ] );
    ?>
    <fieldset class="inline-edit-col-right">
      <div class="inline-edit-col">
        <label>
          <span class="title">分组</span>
          <select name="ai_bulk_section">
            <option value="-1">— 不修改 —</option>
            <option value="0">— 清除 —</option>
            <?php if ( ! is_wp_error( $sections ) ) : foreach ( $sections as $t ) : ?>
              <option value="<?php echo esc_attr( $t->term_id ); ?>"><?php echo esc_html( $t->name ); ?></option>
            <?php endforeach; endif; ?>
          </select>
        </label>
        <label>
          <span class="title">访问级别</span>
          <select name="ai_bulk_access">
            <option value="-1">— 不修改 —</option>
            <option value="0">— 清除 —</option>
            <?php if ( ! is_wp_error( $accesses ) ) : foreach ( $accesses as $t ) : ?>
              <option value="<?php echo esc_attr( $t->term_id ); ?>"><?php echo esc_html( $t->name ); ?></option>
            <?php endforeach; endif; ?>
          </select>
        </label>
      </div>
    </fieldset>
    <?php
}, 10, 2 );

/* ═══════════════════════════════════════════════
   统一保存（快速编辑 + 批量编辑 + 单篇编辑）
   ═══════════════════════════════════════════════ */
add_action( 'save_post_chapter', function ( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    /* ── 快速编辑 ── */
    if ( isset( $_POST['ai_quick_edit_nonce'] ) ) {
        if ( ! wp_verify_nonce( $_POST['ai_quick_edit_nonce'], 'ai_quick_edit' ) ) return;

        if ( array_key_exists( 'chapter_section', $_POST ) ) {
            $tid = absint( $_POST['chapter_section'] );
            wp_set_object_terms( $post_id, $tid ? [ $tid ] : [], 'chapter_section' );
        }
        if ( array_key_exists( 'chapter_access', $_POST ) ) {
            $tid = absint( $_POST['chapter_access'] );
            wp_set_object_terms( $post_id, $tid ? [ $tid ] : [], 'chapter_access' );
        }
        return;
    }

    /* ── 批量编辑 ── */
    if ( isset( $_REQUEST['bulk_edit'] ) ) {
        /* 分组 */
        if ( isset( $_REQUEST['ai_bulk_section'] ) ) {
            $val = $_REQUEST['ai_bulk_section'];
            if ( $val !== '-1' ) {
                $tid = absint( $val );
                wp_set_object_terms( $post_id, $tid ? [ $tid ] : [], 'chapter_section' );
            }
        }
        /* 访问级别 */
        if ( isset( $_REQUEST['ai_bulk_access'] ) ) {
            $val = $_REQUEST['ai_bulk_access'];
            if ( $val !== '-1' ) {
                $tid = absint( $val );
                wp_set_object_terms( $post_id, $tid ? [ $tid ] : [], 'chapter_access' );
            }
        }
    }
} );

/* ═══════════════════════════════════════════════
   排序
   ═══════════════════════════════════════════════ */
add_filter( 'manage_edit-chapter_sortable_columns', function ( $columns ) {
    $columns['chapter_number'] = 'chapter_number';
    return $columns;
} );

add_action( 'pre_get_posts', function ( $q ) {
    if ( ! is_admin() || ! $q->is_main_query() ) return;
    if ( $q->get( 'post_type' ) !== 'chapter' ) return;

    if ( $q->get( 'orderby' ) === 'chapter_number' ) {
        $q->set( 'meta_key', 'chapter_number' );
        $q->set( 'orderby', 'meta_value' );
    }
}, 20 );