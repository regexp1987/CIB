<?php get_header(); ?>
				
<?php
    $post_style = get_post_meta(get_the_ID(), 'post_style', true );
    if((magellan_gs('post_style') == 'no-sidebar' && $post_style == 'global') || $post_style == 'no-sidebar')
    {
        get_template_part('theme/templates/post-full-width');
    }
    else
    {
        get_template_part('theme/templates/post-regular');
    }
?>
				
<?php get_footer(); ?>