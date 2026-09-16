<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/* 由本主题统一输出 canonical，移除 WP 核心的 rel_canonical，避免重复 */
remove_action( 'wp_head', 'rel_canonical' );

/**
 * SEO · 完整版
 * meta box 放主编辑区下方（normal）
 */

function ai_seo_title() {
    if ( is_singular() ) {
        $custom = get_post_meta( get_the_ID(), '_ai_seo_title', true );
        if ( $custom ) return $custom;
    }
    return wp_get_document_title();
}

function ai_seo_description() {
    if ( is_singular() ) {
        $custom = get_post_meta( get_the_ID(), '_ai_seo_description', true );
        if ( $custom ) return $custom;
    }

    $desc = '';
    if ( is_singular() ) {
        if ( has_excerpt() ) $desc = get_the_excerpt();
        else $desc = wp_strip_all_tags( get_the_content() );
    } elseif ( is_front_page() ) {
        $desc = get_bloginfo( 'description' );
    } elseif ( is_category() || is_tag() || is_tax() ) {
        $desc = wp_strip_all_tags( term_description() );
    } elseif ( is_archive() ) {
        $desc = wp_strip_all_tags( get_the_archive_description() );
    } elseif ( is_search() ) {
        $desc = '搜索：' . get_search_query();
    }

    $desc = preg_replace( '/\s+/', ' ', $desc );
    $desc = trim( $desc );
    if ( mb_strlen( $desc ) > 160 ) $desc = mb_substr( $desc, 0, 157 ) . '…';
    return $desc;
}

add_action( 'wp_head', 'ai_output_canonical', 3 );
function ai_output_canonical() {
    if ( is_singular() ) {
        $custom = get_post_meta( get_the_ID(), '_ai_seo_canonical', true );
        if ( $custom ) {
            printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $custom ) );
            return;
        }
    }

    $url = '';
    if ( is_front_page() )      $url = home_url( '/' );
    elseif ( is_singular() )    $url = get_permalink();
    elseif ( is_category() || is_tag() || is_tax() ) {
        $term = get_queried_object();
        if ( $term && ! is_wp_error( $term ) ) $url = get_term_link( $term );
    }
    elseif ( is_author() )      $url = get_author_posts_url( get_queried_object_id() );
    elseif ( is_search() )      $url = get_search_link();
    elseif ( is_archive() )     $url = get_pagenum_link();

    if ( $url && ! is_wp_error( $url ) ) {
        printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $url ) );
    }
}

add_action( 'wp_head', function () {
    if ( is_singular() && get_post_meta( get_the_ID(), '_ai_seo_noindex', true ) === '1' ) {
        echo '<meta name="robots" content="noindex, nofollow">' . "\n";
    }
}, 2 );

add_action( 'wp_head', 'ai_output_seo_meta', 5 );
function ai_output_seo_meta() {
    $site_name = get_bloginfo( 'name' );
    $title     = ai_seo_title();
    $desc      = ai_seo_description();
    $url       = '';
    $type      = 'website';
    $image     = '';

    if ( is_singular() ) {
        $url  = get_permalink();
        $type = 'article';
        if ( has_post_thumbnail() ) $image = get_the_post_thumbnail_url( null, 'large' );
    } elseif ( is_front_page() ) {
        $url = home_url( '/' );
    } elseif ( is_archive() ) {
        $url = get_pagenum_link();
    } elseif ( is_search() ) {
        $url = get_search_link();
    }

    echo "\n<!-- AI Coding SEO -->\n";

    printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( $site_name ) );
    printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
    if ( $desc ) printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $desc ) );
    printf( '<meta property="og:type" content="%s">' . "\n", esc_attr( $type ) );
    if ( $url )   printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );
    if ( $image ) printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $image ) );

    printf( '<meta name="twitter:card" content="%s">' . "\n", $image ? 'summary_large_image' : 'summary' );
    printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $title ) );
    if ( $desc )  printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $desc ) );
    if ( $image ) printf( '<meta name="twitter:image" content="%s">' . "\n", esc_url( $image ) );

    ai_output_json_ld();
}

function ai_get_breadcrumbs() {
    $crumbs = [ [ 'label' => '首页', 'url' => home_url( '/' ) ] ];

    if ( is_front_page() ) return $crumbs;

    if ( is_singular() ) {
        $post_type = get_post_type();

        if ( $post_type === 'chapter' ) {
            $crumbs[] = [ 'label' => '试读', 'url' => home_url( '/preview/' ) ];
        } elseif ( $post_type === 'post' ) {
            $cats = get_the_category();
            if ( ! empty( $cats ) ) {
                $crumbs[] = [ 'label' => $cats[0]->name, 'url' => get_category_link( $cats[0]->term_id ) ];
            }
        }

        $crumbs[] = [ 'label' => get_the_title(), 'url' => get_permalink() ];
    } elseif ( is_category() ) {
        $crumbs[] = [ 'label' => single_cat_title( '', false ), 'url' => get_category_link( get_queried_object_id() ) ];
    } elseif ( is_tag() ) {
        $crumbs[] = [ 'label' => single_tag_title( '', false ), 'url' => get_tag_link( get_queried_object_id() ) ];
    } elseif ( is_tax() ) {
        $term = get_queried_object();
        $crumbs[] = [ 'label' => single_term_title( '', false ), 'url' => $term ? get_term_link( $term ) : '' ];
    } elseif ( is_search() ) {
        $crumbs[] = [ 'label' => '搜索：' . get_search_query(), 'url' => get_search_link() ];
    } elseif ( is_404() ) {
        $crumbs[] = [ 'label' => '404', 'url' => '' ];
    }

    return $crumbs;
}

function ai_output_breadcrumb_json_ld() {
    $crumbs = ai_get_breadcrumbs();
    if ( count( $crumbs ) < 2 ) return;

    $items = [];
    $pos   = 1;
    foreach ( $crumbs as $c ) {
        $item = [
            '@type'    => 'ListItem',
            'position' => $pos,
            'name'     => $c['label'],
        ];
        if ( ! empty( $c['url'] ) ) $item['item'] = $c['url'];
        $items[] = $item;
        $pos++;
    }

    $data = [
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => $items,
    ];

    echo '<script type="application/ld+json">'
       . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
       . '</script>' . "\n";
}

function ai_output_json_ld() {
    $site_name = get_bloginfo( 'name' );
    $site_url  = home_url( '/' );
    $data      = null;

    if ( is_front_page() ) {
        $data = [
            '@context'    => 'https://schema.org',
            '@type'       => 'WebSite',
            'name'        => $site_name,
            'url'         => $site_url,
            'description' => get_bloginfo( 'description' ),
        ];
    } elseif ( is_singular() ) {
        $pt   = get_post_type();
        $type = ( $pt === 'post' || $pt === 'chapter' ) ? 'Article' : 'WebPage';

        $data = [
            '@context'      => 'https://schema.org',
            '@type'         => $type,
            'headline'      => get_the_title(),
            'url'           => get_permalink(),
            'datePublished' => get_the_date( 'c' ),
            'dateModified'  => get_the_modified_date( 'c' ),
            'author'        => [ '@type' => 'Person', 'name' => get_the_author() ],
            'publisher'     => [ '@type' => 'Organization', 'name' => $site_name ],
        ];

        $desc = ai_seo_description();
        if ( $desc ) $data['description'] = $desc;

        if ( has_post_thumbnail() ) {
            $img = get_the_post_thumbnail_url( null, 'large' );
            if ( $img ) $data['image'] = $img;
        }

        if ( $pt === 'chapter' && function_exists( 'ai_count_words' ) ) {
            $data['wordCount'] = ai_count_words();
        }
    }

    if ( $data ) {
        echo '<script type="application/ld+json">'
           . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
           . '</script>' . "\n";
    }

    ai_output_breadcrumb_json_ld();
}

/* ═══════════════════════════════════════════════
   ★ SEO meta box · 回到主编辑区下方（normal）
   ═══════════════════════════════════════════════ */
add_action( 'add_meta_boxes', function () {
    foreach ( [ 'post', 'page', 'chapter' ] as $type ) {
        add_meta_box(
            'ai-seo-box',
            'SEO',
            'ai_render_seo_meta_box',
            $type,
            'normal',       /* ★ 回到 normal */
            'default'
        );
    }
} );

function ai_render_seo_meta_box( $post ) {
    wp_nonce_field( 'ai_seo_save', 'ai_seo_nonce' );

    $title   = get_post_meta( $post->ID, '_ai_seo_title',       true );
    $desc    = get_post_meta( $post->ID, '_ai_seo_description', true );
    $canon   = get_post_meta( $post->ID, '_ai_seo_canonical',   true );
    $noindex = get_post_meta( $post->ID, '_ai_seo_noindex',     true );
    ?>
    <p style="margin:0 0 16px;color:#666;font-size:12px">留空则自动生成。</p>
    <table class="form-table" style="margin:0">
      <tr>
        <th style="width:140px;padding:8px 0"><label for="ai_seo_title">SEO 标题</label></th>
        <td style="padding:8px 0">
          <input type="text" name="ai_seo_title" id="ai_seo_title" class="large-text" value="<?php echo esc_attr( $title ); ?>" maxlength="70">
          <p class="description">建议 ≤ 60 字符</p>
        </td>
      </tr>
      <tr>
        <th style="padding:8px 0"><label for="ai_seo_description">SEO 描述</label></th>
        <td style="padding:8px 0">
          <textarea name="ai_seo_description" id="ai_seo_description" class="large-text" rows="3" maxlength="200"><?php echo esc_textarea( $desc ); ?></textarea>
          <p class="description">建议 ≤ 160 字符</p>
        </td>
      </tr>
      <tr>
        <th style="padding:8px 0"><label for="ai_seo_canonical">Canonical URL</label></th>
        <td style="padding:8px 0">
          <input type="url" name="ai_seo_canonical" id="ai_seo_canonical" class="large-text" value="<?php echo esc_attr( $canon ); ?>" placeholder="留空则用当前页面 URL">
        </td>
      </tr>
      <tr>
        <th style="padding:8px 0">搜索引擎收录</th>
        <td style="padding:8px 0">
          <label>
            <input type="checkbox" name="ai_seo_noindex" value="1" <?php checked( $noindex, '1' ); ?>>
            不索引本页（noindex）
          </label>
        </td>
      </tr>
    </table>
    <?php
}

add_action( 'save_post', function ( $post_id ) {
    if ( ! isset( $_POST['ai_seo_nonce'] ) ) return;
    if ( ! wp_verify_nonce( $_POST['ai_seo_nonce'], 'ai_seo_save' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    $map = [
        '_ai_seo_title'       => 'ai_seo_title',
        '_ai_seo_description' => 'ai_seo_description',
        '_ai_seo_canonical'   => 'ai_seo_canonical',
    ];
    foreach ( $map as $meta_key => $input ) {
        if ( isset( $_POST[ $input ] ) ) {
            $val = wp_unslash( $_POST[ $input ] );
            if ( $meta_key === '_ai_seo_canonical' ) $val = esc_url_raw( $val );
            else                                     $val = sanitize_text_field( $val );

            if ( $val === '' ) delete_post_meta( $post_id, $meta_key );
            else               update_post_meta( $post_id, $meta_key, $val );
        }
    }

    $noindex = isset( $_POST['ai_seo_noindex'] ) ? '1' : '';
    if ( $noindex ) update_post_meta( $post_id, '_ai_seo_noindex', '1' );
    else            delete_post_meta( $post_id, '_ai_seo_noindex' );
} );