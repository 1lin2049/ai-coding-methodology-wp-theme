<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/* 禁用浏览器刷新恢复滚动位置 */
add_action( 'wp_head', function () {
    echo '<script>if(window.history&&history.scrollRestoration){history.scrollRestoration="manual";}</script>' . "\n";
}, 1 );


/* ═══════════════════════════════════════════════
   埋点接收（含 5 道防线）
   ═══════════════════════════════════════════════ */
add_action( 'wp_ajax_nopriv_ai_track', 'ai_handle_track' );
add_action( 'wp_ajax_ai_track',        'ai_handle_track' );

function ai_handle_track() {
    /* ── 防线 ① Origin / Referer 检查 ── */
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    if ( $origin === '' ) {
        $origin = $_SERVER['HTTP_REFERER'] ?? '';
    }

    if ( $origin !== '' ) {
        $host     = parse_url( home_url(), PHP_URL_HOST );
        $origin_h = parse_url( $origin, PHP_URL_HOST );

        if ( $origin_h && $host && strcasecmp( $origin_h, $host ) !== 0 ) {
            wp_die( 'origin rejected', '', [ 'response' => 403 ] );
        }
    }

    /* ── 防线 ② IP 速率限制（每分钟 30 次） ── */
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    if ( $ip !== '' && ! ai_check_rate_limit( $ip, 30, 60 ) ) {
        wp_die( 'rate limited', '', [ 'response' => 429 ] );
    }

    /* ── 参数读取 ── */
    $post_id    = isset( $_POST['post_id'] )    ? absint( $_POST['post_id'] ) : 0;
    $event      = isset( $_POST['event'] )      ? sanitize_key( $_POST['event'] ) : '';
    $value      = isset( $_POST['value'] )      ? sanitize_text_field( wp_unslash( $_POST['value'] ) ) : '';
    $visitor_id = isset( $_POST['visitor_id'] ) ? sanitize_text_field( wp_unslash( $_POST['visitor_id'] ) ) : '';

    /* ── 防线 ③ 参数校验 ── */
    if ( ! $post_id || ! $event ) wp_die( 'missing', '', [ 'response' => 400 ] );
    if ( ! in_array( get_post_type( $post_id ), [ 'post', 'page', 'chapter' ], true ) ) {
        wp_die( 'type', '', [ 'response' => 400 ] );
    }
    if ( ! in_array( $event, [ 'view', 'read', 'progress', 'time' ], true ) ) {
        wp_die( 'event', '', [ 'response' => 400 ] );
    }
    if ( $visitor_id === '' ) $visitor_id = 'anon';
    if ( strlen( $visitor_id ) > 32 ) $visitor_id = substr( $visitor_id, 0, 32 );

    if ( $event === 'time' ) {
        $sec = absint( $value );
        if ( $sec <= 0 || $sec > 300 ) {
            wp_die( 'invalid time', '', [ 'response' => 400 ] );
        }
    }

    /* ── 防线 ④ visitor 去重 ── */
    $window = [ 'view' => 60, 'read' => 1800, 'progress' => 86400, 'time' => 0 ][ $event ] ?? 0;

    if ( $window > 0 && ai_is_duplicate( $post_id, $event, $value, $visitor_id, $window ) ) {
        wp_die( 'dup', '', [ 'response' => 200 ] );
    }

    /* ── 防线 ⑤ 表行数上限检查（10% 概率） ── */
    if ( wp_rand( 1, 10 ) === 1 ) {
        ai_maybe_trim_log();
    }

    /* ── 写入 ── */
    ai_log_event( $post_id, $event, $value, $visitor_id );
    ai_increment_stat( $post_id, $event, $value );
    if ( in_array( $event, [ 'view', 'read' ], true ) ) {
        ai_increment_daily( $post_id, $event );
    }

    wp_die( 'ok', '', [ 'response' => 200 ] );
}


/* ═══════════════════════════════════════════════
   防线辅助函数
   ═══════════════════════════════════════════════ */
function ai_check_rate_limit( $ip, $max = 30, $window = 60 ) {
    $key   = 'ai_rl_' . md5( $ip );
    $count = (int) get_transient( $key );

    if ( $count >= $max ) {
        return false;
    }

    set_transient( $key, $count + 1, $window );
    return true;
}

function ai_maybe_trim_log() {
    global $wpdb;
    $table = $wpdb->prefix . 'ai_track_log';
    $max   = 3000000;

    $rows = (int) $wpdb->get_var( "SELECT TABLE_ROWS FROM information_schema.TABLES
                                   WHERE TABLE_SCHEMA = DATABASE()
                                   AND TABLE_NAME = '{$table}'" );

    if ( $rows <= $max ) return;

    $wpdb->query( "DELETE FROM `{$table}` ORDER BY id ASC LIMIT 1000000" );
}


/* ═══════════════════════════════════════════════
   原有函数
   ═══════════════════════════════════════════════ */
function ai_is_duplicate( $post_id, $event, $value, $visitor_id, $window ) {
    global $wpdb;
    $table  = $wpdb->prefix . 'ai_track_log';
    $cutoff = gmdate( 'Y-m-d H:i:s', time() - $window );
    $sql    = "SELECT id FROM `{$table}` WHERE post_id = %d AND event = %s AND visitor_id = %s AND created_at > %s";
    $params = [ $post_id, $event, $visitor_id, $cutoff ];
    if ( $event === 'progress' && $value !== '' ) { $sql .= " AND value = %s"; $params[] = $value; }
    $sql .= " LIMIT 1";
    return $wpdb->get_var( $wpdb->prepare( $sql, $params ) ) !== null;
}

function ai_log_event( $post_id, $event, $value, $visitor_id ) {
    global $wpdb;
    $wpdb->insert( $wpdb->prefix . 'ai_track_log', [
        'post_id'    => $post_id,
        'event'      => $event,
        'value'      => $value,
        'visitor_id' => $visitor_id,
        'created_at' => current_time( 'mysql', true ),
    ], [ '%d', '%s', '%s', '%s', '%s' ] );
}

function ai_increment_stat( $post_id, $event, $value ) {
    global $wpdb;
    $table = $wpdb->prefix . 'ai_post_stats';
    $field = null;
    $delta = 1;

    switch ( $event ) {
        case 'view': $field = 'views'; break;
        case 'read': $field = 'reads'; break;
        case 'progress':
            $map = [ '25' => 'p25', '50' => 'p50', '75' => 'p75', '100' => 'p100' ];
            if ( isset( $map[ $value ] ) ) $field = $map[ $value ];
            break;
        case 'time':
            $sec = absint( $value );
            if ( $sec > 0 && $sec <= 300 ) { $field = 'time_total'; $delta = $sec; }
            break;
    }
    if ( ! $field ) return;

    $post_id = (int) $post_id;
    $delta   = (int) $delta;

    $affected = $wpdb->query( "UPDATE `{$table}` SET `{$field}` = `{$field}` + {$delta} WHERE post_id = {$post_id}" );
    if ( $affected === 0 ) {
        $wpdb->query( "INSERT INTO `{$table}` (post_id, `{$field}`) VALUES ({$post_id}, {$delta})" );
    }
}

function ai_increment_daily( $post_id, $event ) {
    global $wpdb;
    $table = $wpdb->prefix . 'ai_post_daily';
    $today = current_time( 'Y-m-d' );
    if ( ! in_array( $event, [ 'view', 'read' ], true ) ) return;

    $post_id = (int) $post_id;
    $event_s = ( $event === 'view' ) ? 'views' : 'reads';

    $affected = $wpdb->query( $wpdb->prepare(
        "UPDATE `{$table}` SET `{$event_s}` = `{$event_s}` + 1 WHERE post_id = %d AND stat_date = %s",
        $post_id, $today
    ) );
    if ( $affected === 0 ) {
        $wpdb->query( $wpdb->prepare(
            "INSERT INTO `{$table}` (post_id, stat_date, `{$event_s}`) VALUES (%d, %s, 1)",
            $post_id, $today
        ) );
    }
}


/* ═══════════════════════════════════════════════
   数据读取
   ═══════════════════════════════════════════════ */
function ai_get_post_stats( $post_id = null ) {
    $post_id = (int) ( $post_id ?: get_the_ID() );
    $empty   = [ 'views' => 0, 'reads' => 0, 'time' => 0, 'p25' => 0, 'p50' => 0, 'p75' => 0, 'p100' => 0 ];
    if ( ! $post_id ) return $empty;

    global $wpdb;

    $stats = $wpdb->prefix . 'ai_post_stats';
    $row = $wpdb->get_row( $wpdb->prepare(
        "SELECT views, reads, time_total, p25, p50, p75, p100 FROM `{$stats}` WHERE post_id = %d LIMIT 1",
        $post_id
    ), ARRAY_A );

    if ( is_array( $row ) && ( (int) $row['views'] > 0 || (int) $row['reads'] > 0 ) ) {
        return [
            'views' => (int) $row['views'],
            'reads' => (int) $row['reads'],
            'time'  => (int) $row['time_total'],
            'p25'   => (int) $row['p25'],
            'p50'   => (int) $row['p50'],
            'p75'   => (int) $row['p75'],
            'p100'  => (int) $row['p100'],
        ];
    }

    $log = $wpdb->prefix . 'ai_track_log';
    $row = $wpdb->get_row( $wpdb->prepare(
        "SELECT
            SUM(CASE WHEN event = 'view' THEN 1 ELSE 0 END) AS v,
            SUM(CASE WHEN event = 'read' THEN 1 ELSE 0 END) AS r,
            SUM(CASE WHEN event = 'time' THEN CAST(value AS UNSIGNED) ELSE 0 END) AS t,
            SUM(CASE WHEN event = 'progress' AND value = '25'  THEN 1 ELSE 0 END) AS p25,
            SUM(CASE WHEN event = 'progress' AND value = '50'  THEN 1 ELSE 0 END) AS p50,
            SUM(CASE WHEN event = 'progress' AND value = '75'  THEN 1 ELSE 0 END) AS p75,
            SUM(CASE WHEN event = 'progress' AND value = '100' THEN 1 ELSE 0 END) AS p100
         FROM `{$log}` WHERE post_id = %d",
        $post_id
    ), ARRAY_A );

    if ( is_array( $row ) && ( (int) $row['v'] > 0 || (int) $row['r'] > 0 ) ) {
        $wpdb->query( $wpdb->prepare(
            "INSERT INTO `{$stats}` (post_id, views, reads, time_total, p25, p50, p75, p100)
             VALUES (%d, %d, %d, %d, %d, %d, %d, %d)
             ON DUPLICATE KEY UPDATE
               views = VALUES(views), reads = VALUES(reads), time_total = VALUES(time_total),
               p25 = VALUES(p25), p50 = VALUES(p50), p75 = VALUES(p75), p100 = VALUES(p100)",
            $post_id,
            (int) $row['v'], (int) $row['r'], (int) $row['t'],
            (int) $row['p25'], (int) $row['p50'], (int) $row['p75'], (int) $row['p100']
        ) );

        return [
            'views' => (int) $row['v'],
            'reads' => (int) $row['r'],
            'time'  => (int) $row['t'],
            'p25'   => (int) $row['p25'],
            'p50'   => (int) $row['p50'],
            'p75'   => (int) $row['p75'],
            'p100'  => (int) $row['p100'],
        ];
    }

    $views = (int) get_post_meta( $post_id, '_ai_stat_views', true );
    if ( $views === 0 ) $views = (int) get_post_meta( $post_id, '_ai_stat_read_count', true );

    return [
        'views' => $views,
        'reads' => (int) get_post_meta( $post_id, '_ai_stat_reads',      true ),
        'time'  => (int) get_post_meta( $post_id, '_ai_stat_time_total', true ),
        'p25'   => (int) get_post_meta( $post_id, '_ai_stat_read_25',    true ),
        'p50'   => (int) get_post_meta( $post_id, '_ai_stat_read_50',    true ),
        'p75'   => (int) get_post_meta( $post_id, '_ai_stat_read_75',    true ),
        'p100'  => (int) get_post_meta( $post_id, '_ai_stat_read_100',   true ),
    ];
}


/* ═══════════════════════════════════════════════
   字数 / 阅读时长 / 格式化
   ═══════════════════════════════════════════════ */
function ai_count_words( $post_id = null ) {
    $post_id = $post_id ?: get_the_ID();
    if ( ! $post_id ) return 0;

    $raw = (string) get_post_field( 'post_content', $post_id );
    $text = wp_strip_all_tags( $raw );
    $text = strip_shortcodes( $text );

    $text = str_replace(
        [ "\u{2018}", "\u{2019}", "\u{201A}", "\u{201B}", "\u{2032}", "\u{2035}" ],
        "'", $text
    );
    $text = str_replace(
        [ "\u{201C}", "\u{201D}", "\u{201E}", "\u{201F}", "\u{2033}", "\u{2036}" ],
        '"', $text
    );
    $text = str_replace( [ "\u{2013}", "\u{2014}" ], '-', $text );
    $text = str_replace( "\u{2026}", '...', $text );
    $text = str_replace( "\u{00A0}", ' ', $text );

    $text = preg_replace( '/[\r\n\t ]+/u', ' ', $text );
    $text = trim( $text );
    if ( $text === '' ) return 0;

    $words = preg_split( '/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY );
    $count = 0;

    foreach ( $words as $word ) {
        $len = mb_strlen( $word, 'UTF-8' );

        if ( $len <= 20 ) {
            preg_match_all( '/[\x{4e00}-\x{9fff}]/u', $word, $m );
            $cjk = count( $m[0] );

            if ( $cjk > 0 ) {
                $count += $cjk;
                $rest = preg_replace( '/[\x{4e00}-\x{9fff}]/u', '', $word );
                if ( $rest !== '' ) $count++;
            } else {
                $count++;
            }
            continue;
        }

        $offset = 0;
        while ( $offset < $len ) {
            $chunk = mb_substr( $word, $offset, 1000, 'UTF-8' );

            $matched = preg_match(
                '/^\s*([a-zA-Z]+|[0-9]+|[\x{4e00}-\x{9fff}]|.)/u',
                $chunk,
                $m
            );

            if ( ! $matched || empty( $m[1] ) ) {
                $offset++;
                continue;
            }

            $piece = $m[1];
            $piece_len = mb_strlen( $piece, 'UTF-8' );

            if ( preg_match( '/^[a-zA-Z]+$/', $piece ) ) {
                $count++;
            } elseif ( preg_match( '/^[0-9]+$/', $piece ) ) {
                $count++;
            } elseif ( preg_match( '/^[\x{4e00}-\x{9fff}]$/u', $piece ) ) {
                $count++;
            }

            $offset += $piece_len;
        }
    }

    return $count;
}

function ai_get_reading_minutes( $post_id = null ) {
    $post_id = $post_id ?: get_the_ID();
    if ( ! $post_id ) return 1;

    $html = (string) get_post_field( 'post_content', $post_id );
    $text = wp_strip_all_tags( strip_shortcodes( $html ) );
    $text = preg_replace( '/\s+/u', ' ', $text );

    preg_match_all( '/[\x{4e00}-\x{9fff}\x{3400}-\x{4dbf}]/u', $text, $cn );
    preg_match_all( '/[a-zA-Z]+/', $text, $en );
    preg_match_all( '/\d+/', $text, $num );

    $minutes  = count( $cn[0] ) / 400
              + count( $en[0] ) / 200
              + count( $num[0] ) / 300;

    preg_match_all( '/<img\b[^>]*>/i', $html, $imgs );
    $minutes += count( $imgs[0] ) * 0.2;
    preg_match_all( '/<pre\b[^>]*>/i', $html, $pres );
    $minutes += count( $pres[0] ) * 0.3;

    return max( 1, (int) ceil( $minutes ) );
}

function ai_estimate_read_minutes( $post_id = null ) {
    return ai_get_reading_minutes( $post_id );
}

function ai_format_count( $n ) {
    $n = (int) $n;
    if ( $n < 10000 ) return (string) $n;
    if ( $n < 1000000 ) {
        $v = round( $n / 10000, 1 );
        return rtrim( rtrim( $v, '0' ), '.' ) . '万';
    }
    return round( $n / 10000 ) . '万';
}


/* ═══════════════════════════════════════════════
   meta 行渲染
   顺序：浏览 · 深度阅读 · 约 N 字 · 预计阅读 · 评论
   ═══════════════════════════════════════════════ */
function ai_render_meta_extra( $post_id = null ) {
    $post_id = $post_id ?: get_the_ID();
    if ( ! $post_id ) return '';

    $s        = ai_get_post_stats( $post_id );
    $views    = (int) $s['views'];
    $reads    = (int) $s['reads'];
    $words    = ai_count_words( $post_id );
    $minutes  = ai_get_reading_minutes( $post_id );
    $comments = (int) get_comments_number( $post_id );

    $items = [
        '浏览 '     . ai_format_count( $views ),
        '深度阅读 ' . ai_format_count( $reads ),
        '约 '       . number_format( $words ) . ' 字',
        '预计阅读 ' . $minutes . ' 分钟',
        '评论 '     . ai_format_count( $comments ),
    ];

    $html = '';
    foreach ( $items as $item ) {
        $html .= '<span class="dot-sep">·</span><span class="entry-stat">' . esc_html( $item ) . '</span>';
    }

    return $html;
}


/* ═══════════════════════════════════════════════
   后台列
   ═══════════════════════════════════════════════ */
add_filter( 'manage_posts_columns', function ( $columns ) {
    unset( $columns['comments'] );
    return $columns;
}, 5 );
add_filter( 'manage_pages_columns', function ( $columns ) {
    unset( $columns['comments'] );
    return $columns;
}, 5 );

add_filter( 'manage_posts_columns',         'ai_add_stats_columns', 20 );
add_filter( 'manage_pages_columns',         'ai_add_stats_columns', 20 );
add_filter( 'manage_chapter_posts_columns', 'ai_add_stats_columns', 20 );

function ai_add_stats_columns( $columns ) {
    $columns['ai_stat_views']    = '浏览';
    $columns['ai_stat_reads']    = '深度阅读';
    $columns['ai_stat_comments'] = '评论';
    return $columns;
}

add_action( 'manage_posts_custom_column', 'ai_render_stats_column', 10, 2 );
add_action( 'manage_pages_custom_column', 'ai_render_stats_column', 10, 2 );

function ai_render_stats_column( $column, $post_id ) {
    switch ( $column ) {
        case 'ai_stat_views':
            $s = ai_get_post_stats( $post_id );
            echo esc_html( ai_format_count( $s['views'] ) );
            break;
        case 'ai_stat_reads':
            $s = ai_get_post_stats( $post_id );
            echo esc_html( ai_format_count( $s['reads'] ) );
            break;
        case 'ai_stat_comments':
            $n = (int) get_comments_number( $post_id );
            if ( $n > 0 ) {
                printf(
                    '<a href="%s" title="查看评论">%s</a>',
                    esc_url( admin_url( 'edit-comments.php?p=' . $post_id ) ),
                    esc_html( (string) $n )
                );
            } else {
                echo '0';
            }
            break;
    }
}


/* ═══════════════════════════════════════════════
   后台编辑器 · 加载 3 个脚本
   ═══════════════════════════════════════════════ */
add_action( 'admin_enqueue_scripts', function ( $hook ) {
    if ( ! in_array( $hook, [ 'post.php', 'post-new.php' ], true ) ) return;

    $post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;
    if ( ! $post_id ) return;

    $post_type = get_post_type( $post_id );
    if ( ! in_array( $post_type, [ 'post', 'page', 'chapter' ], true ) ) return;

    /* ── 1. 底部状态栏 · 主题统计 ── */
    $s = ai_get_post_stats( $post_id );

    wp_enqueue_script(
        'ai-admin-stats-bar',
        AI_CODING_URI . '/assets/js/admin-stats-bar.js',
        [],
        AI_CODING_VERSION,
        true
    );

    wp_localize_script( 'ai-admin-stats-bar', 'AI_STATS_BAR', [
        'views'       => (int) $s['views'],
        'reads'       => (int) $s['reads'],
        'comments'    => (int) get_comments_number( $post_id ),
        'words'       => (int) ai_count_words( $post_id ),
        'minutes'     => ai_get_reading_minutes( $post_id ),
        'commentsUrl' => admin_url( 'edit-comments.php?p=' . $post_id ),
    ] );

    /* ── 2. 隐藏 WP 原生统计 ── */
    wp_enqueue_script(
        'ai-admin-hide-native-stats',
        AI_CODING_URI . '/assets/js/admin-hide-native-stats.js',
        [],
        AI_CODING_VERSION,
        true
    );

    /* ── 3. 优化摘要面板 ── */
    wp_enqueue_script(
        'ai-admin-excerpt-optimize',
        AI_CODING_URI . '/assets/js/admin-excerpt-optimize.js',
        [],
        AI_CODING_VERSION,
        true
    );
} );