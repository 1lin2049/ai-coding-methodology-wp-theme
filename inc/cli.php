<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) return;

class AI_Chapter_CLI {

    /**
     * 导出所有章节为 CSV
     *
     * ## OPTIONS
     *
     * [--file=<path>]
     * : 输出文件路径。省略则输出到 stdout。
     *
     * ## EXAMPLES
     *
     *     wp ai-chapter export > chapters.csv
     *     wp ai-chapter export --file=chapters.csv
     */
    public function export( $args, $assoc_args ) {
        $file = $assoc_args['file'] ?? null;

        $chapters = get_posts( [
            'post_type'      => 'chapter',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
            'post_status'    => [ 'publish', 'draft', 'private' ],
        ] );

        $rows   = [];
        $rows[] = [
            'chapter_number', 'post_slug', 'title',
            'chapter_words', 'chapter_time', 'chapter_access',
            'content',
        ];

        foreach ( $chapters as $c ) {
            $access_terms = wp_get_post_terms( $c->ID, 'chapter_access', [ 'fields' => 'slugs' ] );
            $rows[] = [
                get_post_meta( $c->ID, 'chapter_number', true ),
                $c->post_name,
                $c->post_title,
                get_post_meta( $c->ID, 'chapter_words', true ),
                get_post_meta( $c->ID, 'chapter_time', true ),
                ( ! is_wp_error( $access_terms ) && ! empty( $access_terms ) ) ? $access_terms[0] : 'free',
                $c->post_content,
            ];
        }

        if ( $file ) {
            $fh = fopen( $file, 'w' );
            if ( ! $fh ) WP_CLI::error( "无法写入文件：{$file}" );
            fprintf( $fh, chr(0xEF) . chr(0xBB) . chr(0xBF) );
            foreach ( $rows as $r ) fputcsv( $fh, $r );
            fclose( $fh );
            WP_CLI::success( '已导出 ' . count( $chapters ) . " 章 → {$file}" );
        } else {
            $out = fopen( 'php://stdout', 'w' );
            fprintf( $out, chr(0xEF) . chr(0xBB) . chr(0xBF) );
            foreach ( $rows as $r ) fputcsv( $out, $r );
            fclose( $out );
        }
    }

    /**
     * 从 CSV 导入章节
     *
     * ## OPTIONS
     *
     * <file>
     * : CSV 文件路径
     *
     * ## EXAMPLES
     *
     *     wp ai-chapter import chapters.csv
     */
    public function import( $args, $assoc_args ) {
        $file = $args[0] ?? '';
        if ( ! $file || ! file_exists( $file ) ) {
            WP_CLI::error( '文件不存在' );
        }

        $result = ai_import_chapters_from_file( $file );

        WP_CLI::log( "新增：{$result['created']} 章" );
        WP_CLI::log( "更新：{$result['updated']} 章" );

        if ( ! empty( $result['errors'] ) ) {
            foreach ( $result['errors'] as $err ) {
                WP_CLI::warning( $err );
            }
            WP_CLI::error( '导入完成，但存在 ' . count( $result['errors'] ) . ' 个错误', false );
        }

        WP_CLI::success( '导入完成' );
    }

    /**
     * 列出所有章节
     *
     * ## OPTIONS
     *
     * [--access=<access>]
     * : 按访问级别过滤：free / paid
     *
     * ## EXAMPLES
     *
     *     wp ai-chapter list
     *     wp ai-chapter list --access=free
     */
    public function list( $args, $assoc_args ) {
        $query = [
            'post_type'      => 'chapter',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
            'post_status'    => [ 'publish', 'draft', 'private' ],
        ];

        if ( ! empty( $assoc_args['access'] ) ) {
            $query['tax_query'] = [ [
                'taxonomy' => 'chapter_access',
                'field'    => 'slug',
                'terms'    => sanitize_key( $assoc_args['access'] ),
            ] ];
        }

        $chapters = get_posts( $query );

        if ( empty( $chapters ) ) {
            WP_CLI::log( '无章节' );
            return;
        }

        $items = [];
        foreach ( $chapters as $c ) {
            $access_terms = wp_get_post_terms( $c->ID, 'chapter_access', [ 'fields' => 'slugs' ] );
            $items[] = [
                'order'  => $c->menu_order,
                'number' => get_post_meta( $c->ID, 'chapter_number', true ),
                'slug'   => $c->post_name,
                'title'  => $c->post_title,
                'words'  => get_post_meta( $c->ID, 'chapter_words', true ),
                'time'   => get_post_meta( $c->ID, 'chapter_time', true ),
                'access' => ( ! is_wp_error( $access_terms ) && ! empty( $access_terms ) ) ? $access_terms[0] : 'free',
                'status' => $c->post_status,
            ];
        }

        \WP_CLI\Utils\format_items(
            'table',
            $items,
            [ 'order', 'number', 'slug', 'title', 'words', 'time', 'access', 'status' ]
        );
    }
}

WP_CLI::add_command( 'ai-chapter', 'AI_Chapter_CLI' );