<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Cron · 每天清理 log 表
 *   1. 时间清理：删除 30 天前的记录
 *   2. 行数上限：超过 50000 行时删除最旧的
 */

/* 注册定时任务 */
add_action( 'wp', function () {
    if ( ! wp_next_scheduled( 'ai_daily_cleanup' ) ) {
        wp_schedule_event( strtotime( 'tomorrow 03:00' ), 'daily', 'ai_daily_cleanup' );
    }
} );

/* 执行清理 */
add_action( 'ai_daily_cleanup', 'ai_do_daily_cleanup' );
function ai_do_daily_cleanup() {
    if ( ! ai_stats_table_exists( 'track_log' ) ) return;

    global $wpdb;
    $log = ai_table( 'track_log' );

    /* 1. 时间清理 */
    $cutoff = gmdate( 'Y-m-d H:i:s', time() - 30 * DAY_IN_SECONDS );
    $deleted_time = $wpdb->query( $wpdb->prepare(
        "DELETE FROM `$log` WHERE created_at < %s",
        $cutoff
    ) );

    /* 2. 行数上限 */
    $max = 50000;
    $count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM `$log`" );
    $deleted_rows = 0;

    if ( $count > $max ) {
        $excess = $count - $max;
        $deleted_rows = $wpdb->query( $wpdb->prepare(
            "DELETE FROM `$log` ORDER BY id ASC LIMIT %d",
            $excess
        ) );
    }

    if ( $deleted_time || $deleted_rows ) {
        error_log( "[AI Stats] 清理：时间 {$deleted_time} 条，行数 {$deleted_rows} 条" );
    }
}

/* 切主题时清理计划任务 */
add_action( 'switch_theme', function () {
    wp_clear_scheduled_hook( 'ai_daily_cleanup' );
} );