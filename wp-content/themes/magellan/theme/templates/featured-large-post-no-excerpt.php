<?php
	$image = magellan_get_thumbnail('magellan_featured_list_item_large', true, true);
?>
<div class="post-featured no-animation <?php  MagellanInstance()->get_image_gradient_class(); ?>" <?php if($image) { ?>style="background-image: url(<?php echo esc_url($image); ?>);" <?php } ?>>
	<div class="overlay-wrapper">
		<div class="title">
			
			<?php get_template_part('theme/templates/post-categories-compact-dropdown'); ?>
			
			<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
			
			<?php get_template_part('theme/templates/title-legend'); ?>

			<a href="<?php the_permalink(); ?>" class="btn btn-default btn-dark"><?php esc_html_e('Read more', 'magellan'); ?></a>
		</div>
		<div class="overlay <?php  MagellanInstance()->get_image_gradient_class(); ?>" <?php if($image) { ?>style="background-image: url(<?php echo esc_url($image); ?>);"<?php } ?>></div>
	</div>
</div>