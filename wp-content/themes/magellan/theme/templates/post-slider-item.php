<?php
	global $post;
    $thumb = magellan_get_thumbnail('magellan_post_list_item_medium', true, true);
	$post_image_width = magellan_get_post_image_width($post->ID);
?>
<div class="post post-block post-video">
	
	<?php get_template_part('theme/templates/post-categories-compact'); ?>
	
	<div class="overlay-wrapper <?php  MagellanInstance()->get_image_gradient_class(); ?>">
		<div class="content">
			<div>
				<?php
				if($post_image_width == 'video_autoplay' || $post_image_width == 'video') 
				{
					?><a href="<?php the_permalink(); ?>" class="btn-circle btn-play"></a><?php
				}
				else
				{
					?><a href="<?php the_permalink(); ?>" class="btn btn-default btn-dark"><?php esc_html_e('Read more', 'magellan'); ?></a><?php
				}
				?>
				<a href="<?php the_permalink(); ?>" class="link"></a>
			</div>
		</div>
		
		<?php if($thumb) : ?>
			<div class="overlay" style="background-image: url(<?php echo esc_url($thumb); ?>);"></div>
		<?php endif; ?>
			
	</div>
	<div class="image <?php  MagellanInstance()->get_image_gradient_class(); ?>">
		
		<?php
		if($post_image_width == 'video_autoplay' || $post_image_width == 'video') 
		{
			?><a href="<?php the_permalink(); ?>" class="btn-circle btn-play"></a><?php
		} 
		?>
		
		<?php if($thumb) : ?>
			<a href="<?php the_permalink(); ?>"><img src="<?php echo esc_url($thumb); ?>" alt="<?php the_title(); ?>"></a>
		<?php endif; ?>
	</div>
	<div class="title">
		<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<?php get_template_part('theme/templates/title-legend'); ?>
	</div>
</div>