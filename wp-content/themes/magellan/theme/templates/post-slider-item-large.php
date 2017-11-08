<?php
	global $post;
	$thumb = magellan_get_thumbnail('magellan_post_list_item_medium', true, true);
?>
<div class="post-block editors-choice">
	<div class="post-featured <?php  MagellanInstance()->get_image_gradient_class(); ?>" <?php if($thumb) { ?>style="background-image: url(<?php echo esc_url($thumb); ?>);" <?php } ?>>

		<?php get_template_part('theme/templates/post-editors-choice-small'); ?>

		<div class="overlay-wrapper">
			<div class="title">
				
				<?php get_template_part('theme/templates/post-categories-compact'); ?>
				
				<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
				
				<?php get_template_part('theme/templates/title-legend'); ?>
				
				<a href="<?php the_permalink(); ?>" class="btn btn-default btn-dark"><?php esc_html_e('Read more', 'magellan'); ?></a>
				
			</div>
			<div class="overlay <?php  MagellanInstance()->get_image_gradient_class(); ?>" <?php if($thumb) { ?>style="background-image: url(<?php echo esc_url($thumb); ?>);"<?php } ?>></div>
		</div>
	</div>
</div>