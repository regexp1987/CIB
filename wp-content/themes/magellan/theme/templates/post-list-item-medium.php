<?php
		
	$thumb = magellan_get_thumbnail('magellan_post_list_item_medium', true, false);
?>

<div class="post">
	
	<?php if($thumb) : ?>
		<div class="image <?php  MagellanInstance()->get_image_gradient_class(); ?>">
			<a href="<?php the_permalink(); ?>"><img src="<?php echo esc_url($thumb); ?>" alt="<?php the_title(); ?>"></a>
		</div>
	<?php endif; ?>
	
	<div class="title">
		
		<?php get_template_part('theme/templates/post-categories-compact-dropdown'); ?>
		
		<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?> <?php if( MagellanInstance()->is_post_hot(get_the_ID())) { ?><span class="hot"><?php esc_html_e('Hot', 'magellan');?></span><?php } ?></a></h3>
		
		<?php get_template_part('theme/templates/title-legend'); ?>
		
	</div>
</div>