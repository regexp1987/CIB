<?php
/*
Template Name: Page Builder Layout
*/
?>

<?php get_header(); ?>

<?php    
    if ( have_posts() ) : 
        while ( have_posts() ) : the_post();
            the_content();
        endwhile;
    else :
            echo esc_html_e('no posts found!', 'magellan');
    endif;
?>
<?php get_footer(); ?>