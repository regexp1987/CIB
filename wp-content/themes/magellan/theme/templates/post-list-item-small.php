<?php
		
	$thumb = magellan_get_thumbnail('magellan_post_list_item_small', true, false);
?>
<div class="post">
	<?php if($thumb) : ?>
		<div class="overlay-wrapper text-overlay <?php  MagellanInstance()->get_image_gradient_class(); ?>">
			<div class="content">
				<div>				
					<?php get_template_part('theme/templates/post-categories-compact'); ?>
					<a href="<?php the_permalink(); ?>" class="btn btn-default btn-dark"><?php esc_html_e('Read more', 'magellan'); ?></a>
				</div>
			</div>

			<div class="overlay" style="background-image: url(<?php echo esc_url($thumb); ?>);"></div>

		</div>
	<?php endif; ?>
	
	<?php if($thumb) : ?>
		<div class="image <?php  MagellanInstance()->get_image_gradient_class(); ?>">
			<a href="<?php the_permalink(); ?>">
				<?php
				$post_image_width = magellan_get_post_image_width($post->ID);
				if($post_image_width == 'video' || $post_image_width == 'video_autoplay')
				{
					?><span class="btn-circle btn-play"></span><?php
				}
				?>
				<img src="<?php echo esc_url($thumb); ?>" alt="<?php the_title(); ?>">
			</a>
		</div>
	<?php endif; ?>
	
	<div class="title">
		<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?> <?php if( MagellanInstance()->is_post_hot(get_the_ID())) { ?><span class="hot"><?php esc_html_e('Hot', 'magellan');?></span><?php } ?></a></h3>
	</div>
</div>