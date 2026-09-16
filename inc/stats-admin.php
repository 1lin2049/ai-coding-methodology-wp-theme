<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Stats Admin · 后台自检页
 * 工具 → 统计自检
 */

add_action( 'admin_menu', function () {
    add_management_page(
        '统计自检',
        '统计自检',
        'manage_options',
        'ai-stats-check',
        'ai_stats_check_page'
    );
} );

function ai_stats_check_page() {
    global $wpdb;

    $tables = [ 'post_stats', 'post_daily', 'track_log' ];
    ?>

    <div class="wrap">
      <h1>统计自检</h1>

      <?php if ( isset( $_GET['ai_action'] ) ) : ?>
        <div class="notice notice-success is-dismissible"><p>操作完成</p></div>
      <?php endif; ?>

      <?php
      $err = get_option( 'ai_stats_last_error' );
      if ( $err ) :
      ?>
        <div class="notice notice-error">
          <p><strong>建表错误：</strong></p>
          <pre style="background:#fff;padding:12px;overflow:auto"><?php echo esc_html( $err ); ?></pre>
        </div>
      <?php endif; ?>

      <h2>1. 表状态</h2>
      <table class="widefat striped" style="max-width:900px">
        <thead>
          <tr>
            <th>表名</th>
            <th>存在</th>
            <th>行数</th>
            <th>最后更新</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ( $tables as $t ) :
              $name = ai_table( $t );
              $exists = ai_stats_table_exists( $t );
              $count = $exists ? (int) $wpdb->get_var( "SELECT COUNT(*) FROM `$name`" ) : 0;
              $last  = $exists && $t === 'track_log'
                     ? $wpdb->get_var( "SELECT MAX(created_at) FROM `$name`" )
                     : '—';
              ?>
            <tr>
              <td><code><?php echo esc_html( $name ); ?></code></td>
              <td>
                <?php if ( $exists ) : ?>
                  <span style="color:#46b450">✓ 存在</span>
                <?php else : ?>
                  <span style="color:#dc3232">✗ 缺失</span>
                <?php endif; ?>
              </td>
              <td><?php echo esc_html( number_format_i18n( $count ) ); ?></td>
              <td><?php echo esc_html( $last ?: '—' ); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <h2 style="margin-top:32px">2. 当前状态</h2>
      <ul style="font-size:13px">
        <li>DB 版本：<code><?php echo esc_html( get_option( 'ai_stats_db_version', '未初始化' ) ); ?></code>（目标 <?php echo esc_html( AI_STATS_DB_VERSION ); ?>）</li>
        <li>旧数据迁移：<?php echo get_option( 'ai_stats_migrated_v3' ) ? '已完成' : '未执行'; ?></li>
        <li>Log 回填：<?php echo get_option( 'ai_stats_backfilled_v3' ) ? '已完成' : '未执行'; ?></li>
        <li>下次清理：<?php echo esc_html( wp_next_scheduled( 'ai_daily_cleanup' ) ? date_i18n( 'Y-m-d H:i', wp_next_scheduled( 'ai_daily_cleanup' ) ) : '未计划' ); ?></li>
      </ul>

      <h2 style="margin-top:32px">3. 最近 10 条事件</h2>
      <?php if ( ai_stats_table_exists( 'track_log' ) ) :
          $log = ai_table( 'track_log' );
          $rows = $wpdb->get_results( "SELECT * FROM `$log` ORDER BY id DESC LIMIT 10", ARRAY_A );
      ?>
        <table class="widefat striped" style="max-width:1000px">
          <thead>
            <tr><th>ID</th><th>post_id</th><th>event</th><th>value</th><th>visitor</th><th>时间</th></tr>
          </thead>
          <tbody>
            <?php if ( empty( $rows ) ) : ?>
              <tr><td colspan="6">暂无记录</td></tr>
            <?php else : foreach ( $rows as $r ) : ?>
              <tr>
                <td><?php echo (int) $r['id']; ?></td>
                <td><?php echo (int) $r['post_id']; ?></td>
                <td><code><?php echo esc_html( $r['event'] ); ?></code></td>
                <td><?php echo esc_html( $r['value'] ); ?></td>
                <td><code><?php echo esc_html( substr( $r['visitor_id'], 0, 12 ) ); ?>…</code></td>
                <td><?php echo esc_html( $r['created_at'] ); ?></td>
              </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      <?php endif; ?>

      <h2 style="margin-top:32px">4. 聚合数据（前 20 篇）</h2>
      <?php if ( ai_stats_table_exists( 'post_stats' ) ) :
          $stats = ai_table( 'post_stats' );
          $rows = $wpdb->get_results( "SELECT * FROM `$stats` ORDER BY views DESC LIMIT 20", ARRAY_A );
      ?>
        <table class="widefat striped" style="max-width:1000px">
          <thead>
            <tr><th>post_id</th><th>标题</th><th>浏览</th><th>阅读</th><th>时长(s)</th><th>p25/50/75/100</th></tr>
          </thead>
          <tbody>
            <?php if ( empty( $rows ) ) : ?>
              <tr><td colspan="6">暂无数据</td></tr>
            <?php else : foreach ( $rows as $r ) : ?>
              <tr>
                <td><?php echo (int) $r['post_id']; ?></td>
                <td><?php echo esc_html( get_the_title( $r['post_id'] ) ?: '—' ); ?></td>
                <td><strong><?php echo (int) $r['views']; ?></strong></td>
                <td><?php echo (int) $r['reads']; ?></td>
                <td><?php echo (int) $r['time_total']; ?></td>
                <td><?php echo (int) $r['p25']; ?> / <?php echo (int) $r['p50']; ?> / <?php echo (int) $r['p75']; ?> / <?php echo (int) $r['p100']; ?></td>
              </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      <?php endif; ?>

      <h2 style="margin-top:32px">5. 维护操作</h2>
      <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:flex;gap:8px;flex-wrap:wrap">
        <?php wp_nonce_field( 'ai_stats_action' ); ?>
        <input type="hidden" name="action" value="ai_stats_action">

        <button type="submit" name="op" value="rebuild" class="button">重建缺失的表</button>
        <button type="submit" name="op" value="backfill" class="button">重新回填聚合数据</button>
        <button type="submit" name="op" value="clean_log" class="button" onclick="return confirm('清空 log 表？此操作不可恢复')">清空 log 表</button>
        <button type="submit" name="op" value="recheck" class="button button-primary">重新自检</button>
      </form>
    </div>
    <?php
}

/* 维护操作处理 */
add_action( 'admin_post_ai_stats_action', function () {
    if ( ! current_user_can( 'manage_options' ) ) wp_die( 'no permission' );
    check_admin_referer( 'ai_stats_action' );

    $op = $_POST['op'] ?? '';

    switch ( $op ) {
        case 'rebuild':
            ai_create_stats_tables();
            ai_migrate_legacy_postmeta();
            ai_backfill_from_log();
            break;

        case 'backfill':
            delete_option( 'ai_stats_backfilled_v3' );
            ai_backfill_from_log();
            break;

        case 'clean_log':
            global $wpdb;
            $wpdb->query( 'TRUNCATE TABLE `' . ai_table( 'track_log' ) . '`' );
            break;

        case 'recheck':
            delete_option( 'ai_stats_db_version' );
            ai_ensure_stats_db();
            break;
    }

    wp_safe_redirect( add_query_arg(
        [ 'page' => 'ai-stats-check', 'ai_action' => 'done' ],
        admin_url( 'tools.php' )
    ) );
    exit;
} );