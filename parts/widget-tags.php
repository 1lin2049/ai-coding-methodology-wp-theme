<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! is_singular( 'chapter' ) ) return;

$terms = wp_get_post_terms( get_the_ID(), 'chapter_section', [ 'fields' => 'all' ] );
if ( is_wp_error( $terms ) || empty( $terms ) ) return;
?>

<div class="widget widget-tags">
	<div class="widget-head">
		<h4 class="widget-title">所属分组</h4>
	</div>
	<div class="widget-body">
		<div class="tagcloud">
			<?php foreach ( $terms as $term ) : ?>
				<a href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a>
			<?php endforeach; ?>
		</div>
	</div>
</div>