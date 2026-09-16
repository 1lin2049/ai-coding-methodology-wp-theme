<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * 章节内容导入导出
 *
 *   /wp-admin/tools.php?page=ai-chapter-io
 */

/* ═══════════════════════════════════════════════
   后台菜单 + 页面
   ═══════════════════════════════════════════════ */
add_action( 'admin_menu', function () {
    add_management_page(
        '章节导入导出',
        '章节导入导出',
        'manage_options',
        'ai-chapter-io',
        'ai_chapter_io_page'
    );
} );

function ai_chapter_io_page() {
    ?>
    <div class="wrap">
      <h1>章节导入导出</h1>

      <?php if ( isset( $_GET['ai_io'] ) ) : ?>
        <?php
        $status  = sanitize_key( wp_unslash( $_GET['ai_io'] ) );
        $created = (int) ( $_GET['created'] ?? 0 );
        $updated = (int) ( $_GET['updated'] ?? 0 );
        $errors  = (int) ( $_GET['errors'] ?? 0 );
        ?>
        <?php if ( $status === 'success' ) : ?>
          <div class="notice notice-success is-dismissible">
            <p>
              <strong>导入完成</strong> ·
              新增 <strong><?php echo $created; ?></strong> 章 ·
              更新 <strong><?php echo $updated; ?></strong> 章
              <?php if ( $errors > 0 ) : ?>
                · <span style="color:#d63638"><?php echo $errors; ?> 行出错</span>
              <?php endif; ?>
            </p>
          </div>
        <?php elseif ( $status === 'error' ) : ?>
          <div class="notice notice-error is-dismissible">
            <p>导入失败，请检查 CSV 格式。</p>
          </div>
        <?php endif; ?>
      <?php endif; ?>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-top:24px;max-width:960px">

        <div style="padding:24px;border:1px solid #c3c4c7;background:#fff;border-radius:4px">
          <h2 style="margin-top:0">导出</h2>
          <p>导出全部章节为 CSV 文件，包含：章节号、slug、标题、字数、时长、访问级别、正文。</p>
          <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
            <?php wp_nonce_field( 'ai_chapter_export' ); ?>
            <input type="hidden" name="action" value="ai_chapter_export">
            <button type="submit" class="button button-primary">下载 CSV</button>
          </form>
        </div>

        <div style="padding:24px;border:1px solid #c3c4c7;background:#fff;border-radius:4px">
          <h2 style="margin-top:0">导入</h2>
          <p>上传 CSV 文件。已存在的章节（按 slug 或章节号匹配）会被更新，其余新增。</p>
          <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data">
            <?php wp_nonce_field( 'ai_chapter_import' ); ?>
            <input type="hidden" name="action" value="ai_chapter_import">
            <input type="file" name="csv_file" accept=".csv,text/csv" required>
            <button type="submit" class="button button-primary" style="margin-left:8px">上传并导入</button>
          </form>
        </div>

      </div>

      <div style="margin-top:32px;max-width:960px;padding:16px;background:#f0f0f1;border-left:4px solid #72aee6;border-radius:4px">
        <h3 style="margin-top:0">CSV 格式</h3>
        <p>表头固定为：<br>
          <code>chapter_number,post_slug,title,chapter_words,chapter_time,chapter_access,content</code>
        </p>
        <p>示例：</p>
        <pre style="background:#fff;padding:12px;border-radius:4px;overflow:auto;font-size:12px">chapter_number,post_slug,title,chapter_words,chapter_time,chapter_access,content
00,00-preface,代码越来越便宜之后,"4,200 字",14 min,free,"&lt;p&gt;正文...&lt;/p&gt;"
01,01,AI Coding 改变了什么,"6,800 字",22 min,free,"&lt;p&gt;正文...&lt;/p&gt;"</pre>
        <p><strong>访问级别</strong>：<code>free</code>（免费）或 <code>paid</code>（付费）。留空视为 <code>free</code>。</p>
        <p><strong>注意</strong>：正文含逗号 / 换行 / 双引号时，必须用双引号包裹字段，双引号本身用 <code>""</code> 转义。推荐用 Excel / Numbers 编辑后导出为 CSV。</p>
      </div>
    </div>
    <?php
}

/* ═══════════════════════════════════════════════
   导出
   ═══════════════════════════════════════════════ */
add_action( 'admin_post_ai_chapter_export', 'ai_handle_chapter_export' );
function ai_handle_chapter_export() {
    if ( ! current_user_can( 'manage_options' ) ) wp_die( 'No permission.' );
    check_admin_referer( 'ai_chapter_export' );

    $filename = 'ai-chapters-' . date( 'Ymd-His' ) . '.csv';

    header( 'Content-Type: text/csv; charset=utf-8' );
    header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
    header( 'Pragma: no-cache' );
    header( 'Expires: 0' );

    $out = fopen( 'php://output', 'w' );

    /* UTF-8 BOM，Excel 打开不乱码 */
    fprintf( $out, chr(0xEF) . chr(0xBB) . chr(0xBF) );

    fputcsv( $out, [
        'chapter_number', 'post_slug', 'title',
        'chapter_words', 'chapter_time', 'chapter_access',
        'content',
    ] );

    $chapters = get_posts( [
        'post_type'      => 'chapter',
        'posts_per_page' => -1,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
        'post_status'    => [ 'publish', 'draft', 'private' ],
    ] );

    foreach ( $chapters as $c ) {
        $access_terms = wp_get_post_terms( $c->ID, 'chapter_access', [ 'fields' => 'slugs' ] );
        $access       = ( ! is_wp_error( $access_terms ) && ! empty( $access_terms ) ) ? $access_terms[0] : 'free';

        fputcsv( $out, [
            get_post_meta( $c->ID, 'chapter_number', true ),
            $c->post_name,
            $c->post_title,
            get_post_meta( $c->ID, 'chapter_words', true ),
            get_post_meta( $c->ID, 'chapter_time', true ),
            $access,
            $c->post_content,
        ] );
    }

    fclose( $out );
    exit;
}

/* ═══════════════════════════════════════════════
   导入
   ═══════════════════════════════════════════════ */
add_action( 'admin_post_ai_chapter_import', 'ai_handle_chapter_import' );
function ai_handle_chapter_import() {
    if ( ! current_user_can( 'manage_options' ) ) wp_die( 'No permission.' );
    check_admin_referer( 'ai_chapter_import' );

    if ( empty( $_FILES['csv_file']['tmp_name'] ) ) {
        wp_safe_redirect( add_query_arg(
            [ 'page' => 'ai-chapter-io', 'ai_io' => 'error' ],
            admin_url( 'tools.php' )
        ) );
        exit;
    }

    /* 文件类型校验 */
    $check = wp_check_filetype_and_ext(
        $_FILES['csv_file']['tmp_name'],
        $_FILES['csv_file']['name']
    );
    if ( ! in_array( strtolower( $check['ext'] ?? '' ), [ 'csv', 'txt' ], true ) ) {
        wp_safe_redirect( add_query_arg(
            [ 'page' => 'ai-chapter-io', 'ai_io' => 'error' ],
            admin_url( 'tools.php' )
        ) );
        exit;
    }

    $result = ai_import_chapters_from_file( $_FILES['csv_file']['tmp_name'] );

    wp_safe_redirect( add_query_arg( [
        'page'    => 'ai-chapter-io',
        'ai_io'   => 'success',
        'created' => $result['created'],
        'updated' => $result['updated'],
        'errors'  => count( $result['errors'] ),
    ], admin_url( 'tools.php' ) ) );
    exit;
}

/* ═══════════════════════════════════════════════
   核心函数（admin + CLI 共用）
   ═══════════════════════════════════════════════ */
function ai_import_chapters_from_file( $file_path ) {
    $result = [ 'created' => 0, 'updated' => 0, 'errors' => [] ];

    $fh = fopen( $file_path, 'r' );
    if ( ! $fh ) {
        $result['errors'][] = '无法打开文件';
        return $result;
    }

    /* 表头 */
    $header = fgetcsv( $fh );
    if ( ! $header ) {
        fclose( $fh );
        $result['errors'][] = 'CSV 无表头';
        return $result;
    }

    /* 去 BOM */
    if ( isset( $header[0] ) ) {
        $header[0] = preg_replace( '/^\xEF\xBB\xBF/', '', $header[0] );
    }
    $header = array_map( 'trim', $header );

    foreach ( [ 'title', 'content' ] as $required ) {
        if ( ! in_array( $required, $header, true ) ) {
            fclose( $fh );
            $result['errors'][] = "CSV 缺少必填列：{$required}";
            return $result;
        }
    }

    $order = 0;
    while ( ( $row = fgetcsv( $fh ) ) !== false ) {
        /* 跳空行 */
        if ( count( $row ) === 1 && trim( (string) $row[0] ) === '' ) continue;

        $data = [];
        foreach ( $header as $i => $col ) {
            $data[ $col ] = $row[ $i ] ?? '';
        }

        $number  = trim( (string) ( $data['chapter_number'] ?? '' ) );
        $slug    = trim( (string) ( $data['post_slug']      ?? '' ) );
        $title   = trim( (string) ( $data['title']          ?? '' ) );
        $words   = trim( (string) ( $data['chapter_words']  ?? '' ) );
        $time    = trim( (string) ( $data['chapter_time']   ?? '' ) );
        $access  = trim( (string) ( $data['chapter_access'] ?? 'free' ) );
        $content = (string) ( $data['content'] ?? '' );

        if ( $title === '' ) {
            $result['errors'][] = "第 " . ( $order + 2 ) . " 行：标题为空";
            $order++;
            continue;
        }

        /* 判断已存在：slug 优先，其次 chapter_number */
        $existing = null;
        if ( $slug !== '' ) {
            $existing = get_page_by_path( $slug, OBJECT, 'chapter' );
        }
        if ( ! $existing && $number !== '' ) {
            $found = get_posts( [
                'post_type'      => 'chapter',
                'posts_per_page' => 1,
                'meta_key'       => 'chapter_number',
                'meta_value'     => $number,
                'post_status'    => [ 'publish', 'draft', 'private' ],
                'fields'         => 'all',
            ] );
            if ( ! empty( $found ) ) $existing = $found[0];
        }

        $post_data = [
            'post_type'    => 'chapter',
            'post_title'   => $title,
            'post_content' => $content,
            'post_status'  => 'publish',
            'menu_order'   => $order,
        ];
        if ( $slug !== '' ) $post_data['post_name'] = $slug;

        if ( $existing ) {
            $post_data['ID'] = $existing->ID;
            $post_id = wp_update_post( $post_data, true );
            if ( ! is_wp_error( $post_id ) ) $result['updated']++;
        } else {
            $post_id = wp_insert_post( $post_data, true );
            if ( ! is_wp_error( $post_id ) ) $result['created']++;
        }

        if ( is_wp_error( $post_id ) ) {
            $result['errors'][] = "第 " . ( $order + 2 ) . " 行：" . $post_id->get_error_message();
            $order++;
            continue;
        }

        if ( $number !== '' ) update_post_meta( $post_id, 'chapter_number', $number );
        if ( $words  !== '' ) update_post_meta( $post_id, 'chapter_words',  $words );
        if ( $time   !== '' ) update_post_meta( $post_id, 'chapter_time',   $time );

        /* taxonomy */
        $access = in_array( $access, [ 'free', 'paid' ], true ) ? $access : 'free';
        wp_set_object_terms( $post_id, $access, 'chapter_access' );

        $order++;
    }

    fclose( $fh );

    /* 刷 rewrite */
    flush_rewrite_rules();

    return $result;
}