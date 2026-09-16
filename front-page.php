<?php get_header(); ?>

<main id="top">
<?php
foreach ( [
    'hero',
    'problem', 'paradigm', 'framework', 'project', 'assets',
    'data', 'author', 'pricing', 'preview', 'faq', 'final',
] as $part ) {
    get_template_part( 'parts/' . $part );
}
?>
</main>

<?php get_footer(); ?>