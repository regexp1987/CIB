<!DOCTYPE html>
<!--[if lt IE 7]>      <html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html <?php language_attributes(); ?> class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html <?php language_attributes(); ?> class="no-js"> <!--<![endif]-->
    
	<!-- BEGIN head -->
	<head>        		
        <!-- Meta tags -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <meta charset="<?php bloginfo( 'charset' ); ?>">
        
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
		<?php
			if ( is_singular() && get_option( 'thread_comments' ) )
            {
                wp_enqueue_script( 'comment-reply' );
            }
		?>
        
        <?php if (have_posts()):while(have_posts()):the_post(); endwhile; endif;?>
                
		<?php if (is_single()) : ?>
		<?php $img = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'magellan_product_single'); ?>
		<script type="application/ld+json">
		{
		  "@context": "http://schema.org",
		  "@type": "NewsArticle",
		  "headline": "<?php the_title(); ?>",
		  "alternativeHeadline": "<?php echo magellan_excerpt(20); ?>",
		  
		 "image": ["<?php if($img) { echo esc_url($img[0]); } ?>"],
		  "datePublished": "<?php echo esc_attr(get_the_date('Y-m-dTH:i:s')); ?>",
		  "description": "<?php echo magellan_excerpt(50); ?>",
		  "articleBody": "<?php echo strip_tags(get_the_content()); ?>"
		}
		</script>
		<?php endif; ?>
		
        <?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
		
		<?php 
            $overlay = get_theme_mod('enable-bg-overlay', magellan_gs('enable-bg-overlay'));
			if($overlay)
			{
				?><div class="pattern-color"></div><?php
			}
		?>
		
        <?php get_template_part('theme/templates/header'); ?>