<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'init', function () {
    register_block_pattern_category( 'ai-coding', [
        'label' => __( 'AI Coding 方法论', 'ai-coding' ),
    ] );
} );

add_action( 'init', function () {
    $dir = AI_CODING_DIR . '/patterns';
    if ( ! is_dir( $dir ) ) return;

    foreach ( glob( $dir . '/*.php' ) as $file ) {
        $meta = get_file_data( $file, [
            'title'       => 'Title',
            'slug'        => 'Slug',
            'description' => 'Description',
            'categories'  => 'Categories',
        ] );

        if ( empty( $meta['title'] ) ) continue;

        $slug = ! empty( $meta['slug'] )
            ? $meta['slug']
            : 'ai-coding/' . basename( $file, '.php' );

        ob_start();
        include $file;
        $content = ob_get_clean();

        register_block_pattern( $slug, [
            'title'       => $meta['title'],
            'description' => $meta['description'] ?? '',
            'categories'  => array_filter( array_map( 'trim', explode( ',', $meta['categories'] ?: 'ai-coding' ) ) ),
            'content'     => $content,
        ] );
    }
} );