<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * 6 种页面布局
 */

function ai_layouts() {
    return [
        'fullscreen'    => '全屏',
        'wide'          => '全宽 · 1200px',
        'narrow'        => '窄栏 · 820px',
        'sidebar-left'  => '左侧栏',
        'sidebar-right' => '右侧栏',
        'custom'        => '自定义宽度',
    ];
}

function ai_layout_default() {
    $saved   = get_theme_mod( 'ai_default_layout', 'wide' );
    $layouts = ai_layouts();
    if ( ! array_key_exists( $saved, $layouts ) ) return 'wide';
    return $saved;
}

function ai_allow_layout_override() {
    return (bool) get_theme_mod( 'ai_allow_layout_override', true );
}

function ai_get_layout( $post_id = null ) {
    if ( ! ai_allow_layout_override() ) {
        return ai_layout_default();
    }

    $post_id = $post_id ?: get_queried_object_id();
    if ( ! $post_id ) return ai_layout_default();

    $layout = get_post_meta( $post_id, '_ai_layout', true );
    if ( ! $layout || ! array_key_exists( $layout, ai_layouts() ) ) {
        return ai_layout_default();
    }
    return $layout;
}

function ai_layout_has_sidebar( $layout = null ) {
    $layout = $layout ?: ai_get_layout();
    return in_array( $layout, [ 'sidebar-left', 'sidebar-right' ], true );
}

function ai_layout_sidebar_id( $layout = null ) {
    $layout = $layout ?: ai_get_layout();
    return $layout === 'sidebar-left' ? 'sidebar-left' : 'sidebar-right';
}

/* ── 自定义 vw · PC ── */
function ai_get_custom_vw_pc( $post_id = null ) {
    $post_id = $post_id ?: get_queried_object_id();

    if ( $post_id ) {
        $meta = get_post_meta( $post_id, '_ai_layout_custom_vw', true );
        if ( $meta !== '' && is_numeric( $meta ) ) {
            $vw = (int) $meta;
            if ( $vw >= 30 && $vw <= 100 ) return $vw;
        }
    }

    $global = (int) get_theme_mod( 'ai_layout_custom_vw_pc', 60 );
    return max( 30, min( 100, $global ) );
}

/* ── 自定义 vw · 移动端 ── */
function ai_get_custom_vw_mobile( $post_id = null ) {
    $post_id = $post_id ?: get_queried_object_id();

    if ( $post_id ) {
        $meta = get_post_meta( $post_id, '_ai_layout_custom_vw_mobile', true );
        if ( $meta !== '' && is_numeric( $meta ) ) {
            $vw = (int) $meta;
            if ( $vw >= 30 && $vw <= 100 ) return $vw;
        }
    }

    $global = (int) get_theme_mod( 'ai_layout_custom_vw_mobile', 95 );
    return max( 30, min( 100, $global ) );
}


/* ═══════════════════════════════════════════════
   body_class
   ═══════════════════════════════════════════════ */
add_filter( 'body_class', function ( $classes ) {
    if ( is_front_page() ) return $classes;
    if ( is_page_template( 'page-preview.php' ) ) return $classes;

    $classes[] = 'layout-' . ai_get_layout();
    return $classes;
} );


/* ═══════════════════════════════════════════════
   内联注入 · 仅 custom 布局的 --ai-custom-vw
   ═══════════════════════════════════════════════ */
add_action( 'wp_head', function () {
    if ( ai_get_layout() !== 'custom' ) return;

    $pc     = ai_get_custom_vw_pc();
    $mobile = ai_get_custom_vw_mobile();

    $selector = 'body.layout-custom, body.layout-custom.r-single-chapter';

    $css  = "\n";
    $css .= $selector . " { --ai-custom-vw: " . $pc . "vw; }\n";
    $css .= "@media (max-width: 900px) {\n";
    $css .= "  " . $selector . " { --ai-custom-vw: " . $mobile . "vw; }\n";
    $css .= "}\n";

    echo '<style id="ai-layout-custom-vw">' . $css . '</style>' . "\n";
}, 99 );


/* ═══════════════════════════════════════════════
   Customizer
   ═══════════════════════════════════════════════ */
add_action( 'customize_register', function ( $wp_customize ) {
    if ( ! $wp_customize->get_panel( 'ai_coding' ) ) {
        $wp_customize->add_panel( 'ai_coding', [
            'title'    => 'AI Coding 主题',
            'priority' => 10,
        ] );
    }

    $wp_customize->add_section( 'ai_layout', [
        'title'       => '⓪ 全站布局',
        'description' => '控制全站所有页面、文章、章节的容器宽度与侧栏位置',
        'panel'       => 'ai_coding',
        'priority'    => 1,
    ] );

    $wp_customize->add_setting( 'ai_default_layout', [
        'default'           => 'wide',
        'sanitize_callback' => 'ai_sanitize_layout_name',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'ai_default_layout', [
        'label'   => '全站默认布局',
        'section' => 'ai_layout',
        'type'    => 'radio',
        'choices' => ai_layouts(),
    ] );

    $wp_customize->add_setting( 'ai_layout_custom_vw_pc', [
        'default'           => 60,
        'sanitize_callback' => 'ai_sanitize_vw_num',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'ai_layout_custom_vw_pc', [
        'label'       => '自定义宽度 · PC',
        'description' => 'PC（>900px）默认 vw。60 = 视口 60%',
        'section'     => 'ai_layout',
        'type'        => 'number',
        'input_attrs' => [ 'min' => 30, 'max' => 100, 'step' => 5 ],
    ] );

    $wp_customize->add_setting( 'ai_layout_custom_vw_mobile', [
        'default'           => 95,
        'sanitize_callback' => 'ai_sanitize_vw_num',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'ai_layout_custom_vw_mobile', [
        'label'       => '自定义宽度 · 移动端',
        'description' => '移动端（≤900px）默认 vw。95 = 视口 95%',
        'section'     => 'ai_layout',
        'type'        => 'number',
        'input_attrs' => [ 'min' => 30, 'max' => 100, 'step' => 5 ],
    ] );

    $wp_customize->add_setting( 'ai_allow_layout_override', [
        'default'           => true,
        'sanitize_callback' => 'ai_sanitize_bool',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'ai_allow_layout_override', [
        'label'       => '允许单篇覆盖',
        'description' => '开启后：编辑文章/页面/章节可单独选布局',
        'section'     => 'ai_layout',
        'type'        => 'checkbox',
    ] );
} );

/* ── 唯一命名，避免与 customizer.php 冲突 ── */
function ai_sanitize_layout_name( $value ) {
    $layouts = ai_layouts();
    return array_key_exists( $value, $layouts ) ? $value : 'wide';
}
function ai_sanitize_bool( $value ) {
    return ( $value === '1' || $value === 1 || $value === true ) ? true : false;
}
function ai_sanitize_vw_num( $value ) {
    $value = (int) $value;
    return max( 30, min( 100, $value ) );
}


/* ═══════════════════════════════════════════════
   Meta Box
   ═══════════════════════════════════════════════ */
add_action( 'add_meta_boxes', function () {
    if ( ! ai_allow_layout_override() ) return;

    foreach ( [ 'page', 'post', 'chapter' ] as $type ) {
        add_meta_box(
            'ai-layout',
            '页面布局',
            'ai_layout_meta_box',
            $type,
            'side',
            'default'
        );
    }
} );

function ai_layout_meta_box( $post ) {
    wp_nonce_field( 'ai_layout_save', 'ai_layout_nonce' );

    $current      = get_post_meta( $post->ID, '_ai_layout', true ) ?: '';
    $default      = ai_layout_default();
    $custom_vw_pc = get_post_meta( $post->ID, '_ai_layout_custom_vw', true );
    $custom_vw_mb = get_post_meta( $post->ID, '_ai_layout_custom_vw_mobile', true );
    $global_vw_pc = (int) get_theme_mod( 'ai_layout_custom_vw_pc', 60 );
    $global_vw_mb = (int) get_theme_mod( 'ai_layout_custom_vw_mobile', 95 );
    $box_id       = 'ai-custom-vw-wrap-' . $post->ID;
    ?>
    <p style="margin:0 0 10px;color:#666;font-size:12px;line-height:1.5">
      留空则跟随全站默认（当前为 <code style="background:#f0f0f1;padding:1px 4px;border-radius:2px"><?php echo esc_html( $default ); ?></code>）。
    </p>

    <label style="display:block;padding:5px 0;cursor:pointer">
      <input type="radio" name="ai_layout" value="" <?php checked( $current, '' ); ?>>
      <em style="color:#666">跟随全站默认</em>
    </label>

    <?php foreach ( ai_layouts() as $slug => $label ) : ?>
      <label style="display:block;padding:5px 0;cursor:pointer">
        <input type="radio" name="ai_layout" value="<?php echo esc_attr( $slug ); ?>" <?php checked( $current, $slug ); ?>>
        <?php echo esc_html( $label ); ?>
      </label>
    <?php endforeach; ?>

    <div id="<?php echo esc_attr( $box_id ); ?>"
         style="margin-top:14px;padding-top:14px;border-top:1px solid #eee;<?php echo $current === 'custom' ? '' : 'display:none'; ?>">

      <p style="margin:0 0 12px;font-weight:600;font-size:12px;color:#1e1e1e">
        自定义宽度 · vw 值
      </p>

      <p style="margin:0 0 14px">
        <label style="display:block;margin-bottom:4px;font-size:12px">PC（&gt;900px）</label>
        <input type="number" name="ai_layout_custom_vw"
               value="<?php echo esc_attr( $custom_vw_pc !== '' ? $custom_vw_pc : '' ); ?>"
               min="30" max="100" step="5"
               placeholder="<?php echo esc_attr( $global_vw_pc ); ?>"
               style="width:100%">
        <span style="display:block;margin-top:4px;color:#666;font-size:11px">
          留空 = 全站默认 <?php echo esc_html( $global_vw_pc ); ?>vw
        </span>
      </p>

      <p style="margin:0">
        <label style="display:block;margin-bottom:4px;font-size:12px">移动端（≤900px）</label>
        <input type="number" name="ai_layout_custom_vw_mobile"
               value="<?php echo esc_attr( $custom_vw_mb !== '' ? $custom_vw_mb : '' ); ?>"
               min="30" max="100" step="5"
               placeholder="<?php echo esc_attr( $global_vw_mb ); ?>"
               style="width:100%">
        <span style="display:block;margin-top:4px;color:#666;font-size:11px">
          留空 = 全站默认 <?php echo esc_html( $global_vw_mb ); ?>vw
        </span>
      </p>
    </div>

    <script>
    (function(){
      var wrap = document.getElementById(<?php echo json_encode( $box_id ); ?>);
      if (!wrap) return;
      var radios = document.querySelectorAll('input[name="ai_layout"]');
      function update() {
        var checked = document.querySelector('input[name="ai_layout"]:checked');
        wrap.style.display = (checked && checked.value === 'custom') ? '' : 'none';
      }
      radios.forEach(function(r){ r.addEventListener('change', update); });
      update();
    })();
    </script>
    <?php
}

add_action( 'save_post', function ( $post_id ) {
    if ( ! isset( $_POST['ai_layout_nonce'] ) ) return;
    if ( ! wp_verify_nonce( $_POST['ai_layout_nonce'], 'ai_layout_save' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    $value = isset( $_POST['ai_layout'] ) ? sanitize_key( $_POST['ai_layout'] ) : '';
    if ( $value === '' || ! array_key_exists( $value, ai_layouts() ) ) {
        delete_post_meta( $post_id, '_ai_layout' );
    } else {
        update_post_meta( $post_id, '_ai_layout', $value );
    }

    $vw_pc = isset( $_POST['ai_layout_custom_vw'] ) ? trim( wp_unslash( $_POST['ai_layout_custom_vw'] ) ) : '';
    if ( $vw_pc === '' ) {
        delete_post_meta( $post_id, '_ai_layout_custom_vw' );
    } else {
        $vw_int = (int) $vw_pc;
        if ( $vw_int >= 30 && $vw_int <= 100 ) {
            update_post_meta( $post_id, '_ai_layout_custom_vw', $vw_int );
        } else {
            delete_post_meta( $post_id, '_ai_layout_custom_vw' );
        }
    }

    $vw_mb = isset( $_POST['ai_layout_custom_vw_mobile'] ) ? trim( wp_unslash( $_POST['ai_layout_custom_vw_mobile'] ) ) : '';
    if ( $vw_mb === '' ) {
        delete_post_meta( $post_id, '_ai_layout_custom_vw_mobile' );
    } else {
        $vw_int = (int) $vw_mb;
        if ( $vw_int >= 30 && $vw_int <= 100 ) {
            update_post_meta( $post_id, '_ai_layout_custom_vw_mobile', $vw_int );
        } else {
            delete_post_meta( $post_id, '_ai_layout_custom_vw_mobile' );
        }
    }
} );