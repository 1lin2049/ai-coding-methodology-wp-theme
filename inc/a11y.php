<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Accessibility · Skip link / aria / 焦点可见
 */

/* ═══════════════════════════════════════════════
   Skip link
   ═══════════════════════════════════════════════ */
add_action( 'wp_body_open', 'ai_skip_link', 1 );
function ai_skip_link() {
    printf(
        '<a class="skip-link" href="#top">%s</a>',
        esc_html__( '跳到主内容', 'ai-coding' )
    );
}


/* ═══════════════════════════════════════════════
   主题切换按钮 aria-pressed
   ═══════════════════════════════════════════════ */
add_action( 'wp_footer', function () {
    ?>
    <script>
    (function(){
      var btn = document.getElementById('themeToggle');
      if (!btn) return;
      function sync() {
        var t = document.documentElement.getAttribute('data-theme');
        btn.setAttribute('aria-pressed', t === 'dark' ? 'true' : 'false');
      }
      sync();
      btn.addEventListener('click', function(){ setTimeout(sync, 0); });
    })();
    </script>
    <?php
}, 5 );


/* ═══════════════════════════════════════════════
   搜索表单加 role
   ═══════════════════════════════════════════════ */
add_filter( 'get_search_form', function ( $form ) {
    return str_replace( '<form role="search"', '<form role="search" aria-label="站点搜索"', $form );
} );


/* ═══════════════════════════════════════════════
   图片 alt 兜底
   ═══════════════════════════════════════════════ */
add_filter( 'wp_get_attachment_image_attributes', function ( $attr ) {
    if ( empty( $attr['alt'] ) ) {
        $attr['alt'] = '';
        $attr['aria-hidden'] = 'true';
    }
    return $attr;
} );


/* ═══════════════════════════════════════════════
   外链自动加 rel="noopener"
   ═══════════════════════════════════════════════ */
add_filter( 'the_content', function ( $content ) {
    if ( ! $content || is_admin() ) return $content;

    return preg_replace_callback(
        '/<a\s+([^>]*href=["\']https?:\/\/(?![^"\']*' . preg_quote( home_url(), '/' ) . ')[^"\']*["\'][^>]*)>/i',
        function ( $m ) {
            $attrs = $m[1];
            if ( strpos( $attrs, 'target=' ) !== false && strpos( $attrs, 'rel=' ) === false ) {
                return '<a ' . $attrs . ' rel="noopener noreferrer">';
            }
            return $m[0];
        },
        $content
    );
} );