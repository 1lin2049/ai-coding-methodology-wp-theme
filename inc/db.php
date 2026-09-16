<?php
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'AI_STATS_DB_VERSION', '3.0.0' );

function ai_table( $name ) {
    global $wpdb;
    return $wpdb->prefix . 'ai_' . $name;
}

function ai_stats_table_exists( $name ) {
    global $wpdb;
    $table = ai_table( $name );
    return $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) === $table;
}

function ai_stats_all_tables_exist() {
    foreach ( [ 'post_stats', 'post_daily', 'track_log' ] as $t ) {
        if ( ! ai_stats_table_exists( $t ) ) return false;
    }
    return true;
}

add_action( 'admin_init', 'ai_ensure_stats_db', 1 );
add_action( 'wp_loaded',  'ai_ensure_stats_db', 1 );

function ai_ensure_stats_db() {
    static $ran = false;
    if ( $ran ) return;
    $ran = true;

    $missing = [];
    foreach ( [ 'post_stats', 'post_daily', 'track_log' ] as $t ) {
        if ( ! ai_stats_table_exists( $t ) ) $missing[] = $t;
    }

    $version_ok = ( get_option( 'ai_stats_db_version' ) === AI_STATS_DB_VERSION );
    if ( empty( $missing ) && $version_ok ) return;

    if ( ! empty( $missing ) ) {
        ai_create_stats_tables();
    } else {
        ai_alter_stats_tables();
    }

    ai_migrate_legacy_postmeta();
    ai_backfill_from_log();

    if ( ai_stats_all_tables_exist() ) {
        update_option( 'ai_stats_db_version', AI_STATS_DB_VERSION );
    }
}

function ai_create_stats_tables() {
    global $wpdb;
    $charset = $wpdb->get_charset_collate();

    $sqls = [];

    $sqls[] = "CREATE TABLE IF NOT EXISTS `" . ai_table( 'post_stats' ) . "` (
        `post_id`    BIGINT UNSIGNED NOT NULL,
        `views`      BIGINT UNSIGNED NOT NULL DEFAULT 0,
        `reads`      BIGINT UNSIGNED NOT NULL DEFAULT 0,
        `time_total` BIGINT UNSIGNED NOT NULL DEFAULT 0,
        `p25`        BIGINT UNSIGNED NOT NULL DEFAULT 0,
        `p50`        BIGINT UNSIGNED NOT NULL DEFAULT 0,
        `p75`        BIGINT UNSIGNED NOT NULL DEFAULT 0,
        `p100`       BIGINT UNSIGNED NOT NULL DEFAULT 0,
        `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`post_id`),
        KEY `updated_at` (`updated_at`)
    ) ENGINE=InnoDB $charset";

    $sqls[] = "CREATE TABLE IF NOT EXISTS `" . ai_table( 'post_daily' ) . "` (
        `post_id`   BIGINT UNSIGNED NOT NULL,
        `stat_date` DATE NOT NULL,
        `views`     BIGINT UNSIGNED NOT NULL DEFAULT 0,
        `reads`     BIGINT UNSIGNED NOT NULL DEFAULT 0,
        PRIMARY KEY (`post_id`, `stat_date`),
        KEY `stat_date` (`stat_date`)
    ) ENGINE=InnoDB $charset";

    $sqls[] = "CREATE TABLE IF NOT EXISTS `" . ai_table( 'track_log' ) . "` (
        `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `post_id`    BIGINT UNSIGNED NOT NULL,
        `event`      VARCHAR(20) NOT NULL DEFAULT '',
        `value`      VARCHAR(20) NOT NULL DEFAULT '',
        `visitor_id` VARCHAR(32) NOT NULL DEFAULT '',
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `post_event` (`post_id`, `event`),
        KEY `visitor_lookup` (`visitor_id`, `post_id`, `event`, `created_at`),
        KEY `created_at` (`created_at`)
    ) ENGINE=InnoDB $charset";

    $errors = [];
    foreach ( $sqls as $sql ) {
        $r = $wpdb->query( $sql );
        if ( $r === false ) {
            $errors[] = $wpdb->last_error;
            error_log( '[AI Stats] CREATE TABLE 失败: ' . $wpdb->last_error );
        }
    }

    if ( ! empty( $errors ) ) {
        update_option( 'ai_stats_last_error', implode( "\n", $errors ) );
    } else {
        delete_option( 'ai_stats_last_error' );
    }
}

function ai_alter_stats_tables() {
    global $wpdb;
    $table = ai_table( 'post_stats' );
    if ( ! ai_stats_table_exists( 'post_stats' ) ) return;
    $cols = $wpdb->get_col( "SHOW COLUMNS FROM `$table`", 0 );
    if ( ! in_array( 'updated_at', $cols, true ) ) {
        $wpdb->query( "ALTER TABLE `$table` ADD COLUMN `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP" );
    }
}

function ai_migrate_legacy_postmeta() {
    if ( get_option( 'ai_stats_migrated_v3' ) ) return;
    global $wpdb;
    $stats = ai_table( 'post_stats' );
    if ( ! ai_stats_table_exists( 'post_stats' ) ) return;

    $rows = $wpdb->get_results(
        "SELECT post_id, meta_key, meta_value FROM {$wpdb->postmeta}
         WHERE meta_key IN (
            '_ai_stat_views', '_ai_stat_reads', '_ai_stat_time_total',
            '_ai_stat_read_25', '_ai_stat_read_50', '_ai_stat_read_75', '_ai_stat_read_100',
            '_ai_stat_read_count'
         )", ARRAY_A );

    if ( empty( $rows ) ) {
        update_option( 'ai_stats_migrated_v3', 1 );
        return;
    }

    $map = [
        '_ai_stat_views'      => 'views',
        '_ai_stat_reads'      => 'reads',
        '_ai_stat_time_total' => 'time_total',
        '_ai_stat_read_25'    => 'p25',
        '_ai_stat_read_50'    => 'p50',
        '_ai_stat_read_75'    => 'p75',
        '_ai_stat_read_100'   => 'p100',
        '_ai_stat_read_count' => 'views',
    ];

    $by_post = [];
    foreach ( $rows as $r ) {
        $pid = (int) $r['post_id'];
        $key = $r['meta_key'];
        $val = (int) $r['meta_value'];
        if ( ! isset( $map[ $key ] ) ) continue;
        $field = $map[ $key ];
        $by_post[ $pid ][ $field ] = max( $by_post[ $pid ][ $field ] ?? 0, $val );
    }

    foreach ( $by_post as $pid => $fields ) {
        $cols = array_keys( $fields );
        $ph   = array_fill( 0, count( $cols ), '%d' );
        $vals = array_values( $fields );
        $update = [];
        foreach ( $cols as $c ) $update[] = "`$c` = GREATEST(`$c`, VALUES(`$c`))";
        $sql = "INSERT INTO `$stats` (`post_id`, `" . implode( '`, `', $cols ) . "`) VALUES (%d, " . implode( ', ', $ph ) . ") ON DUPLICATE KEY UPDATE " . implode( ', ', $update );
        $wpdb->query( $wpdb->prepare( $sql, array_merge( [ $pid ], $vals ) ) );
    }

    update_option( 'ai_stats_migrated_v3', 1 );
}

function ai_backfill_from_log() {
    if ( get_option( 'ai_stats_backfilled_v3' ) ) return;
    global $wpdb;
    $stats = ai_table( 'post_stats' );
    $daily = ai_table( 'post_daily' );
    $log   = ai_table( 'track_log' );
    if ( ! ai_stats_table_exists( 'track_log' ) ) return;

    $wpdb->query( "INSERT INTO `$stats` (post_id, views, reads, time_total, p25, p50, p75, p100)
        SELECT post_id,
            SUM(CASE WHEN event = 'view' THEN 1 ELSE 0 END),
            SUM(CASE WHEN event = 'read' THEN 1 ELSE 0 END),
            SUM(CASE WHEN event = 'time' THEN CAST(value AS UNSIGNED) ELSE 0 END),
            SUM(CASE WHEN event = 'progress' AND value = '25'  THEN 1 ELSE 0 END),
            SUM(CASE WHEN event = 'progress' AND value = '50'  THEN 1 ELSE 0 END),
            SUM(CASE WHEN event = 'progress' AND value = '75'  THEN 1 ELSE 0 END),
            SUM(CASE WHEN event = 'progress' AND value = '100' THEN 1 ELSE 0 END)
        FROM `$log` GROUP BY post_id
        ON DUPLICATE KEY UPDATE
            views = VALUES(views), reads = VALUES(reads), time_total = VALUES(time_total),
            p25 = VALUES(p25), p50 = VALUES(p50), p75 = VALUES(p75), p100 = VALUES(p100)" );

    $wpdb->query( "INSERT INTO `$daily` (post_id, stat_date, views, reads)
        SELECT post_id, DATE(created_at), 
            SUM(CASE WHEN event = 'view' THEN 1 ELSE 0 END),
            SUM(CASE WHEN event = 'read' THEN 1 ELSE 0 END)
        FROM `$log` GROUP BY post_id, DATE(created_at)
        ON DUPLICATE KEY UPDATE views = VALUES(views), reads = VALUES(reads)" );

    update_option( 'ai_stats_backfilled_v3', 1 );
}